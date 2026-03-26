<?php

if (!function_exists('ems_admin_table_exists')) {
    function ems_admin_table_exists($conn, $tableName)
    {
        static $cache = [];

        $table = trim((string)$tableName);
        if ($table === '') {
            return false;
        }

        if (array_key_exists($table, $cache)) {
            return $cache[$table];
        }

        $stmt = $conn->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1');
        if (!$stmt) {
            $cache[$table] = false;
            return false;
        }

        $stmt->bind_param('s', $table);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result && $result->num_rows > 0;
        $stmt->close();

        $cache[$table] = (bool)$exists;
        return $cache[$table];
    }
}

if (!function_exists('ems_admin_column_exists')) {
    function ems_admin_column_exists($conn, $tableName, $columnName)
    {
        static $cache = [];

        $table = trim((string)$tableName);
        $column = trim((string)$columnName);
        if ($table === '' || $column === '') {
            return false;
        }

        $cacheKey = $table . '::' . $column;
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $stmt = $conn->prepare('SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? LIMIT 1');
        if (!$stmt) {
            $cache[$cacheKey] = false;
            return false;
        }

        $stmt->bind_param('ss', $table, $column);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result && $result->num_rows > 0;
        $stmt->close();

        $cache[$cacheKey] = (bool)$exists;
        return $cache[$cacheKey];
    }
}

if (!function_exists('ems_admin_bind_and_execute')) {
    function ems_admin_bind_and_execute($stmt, $types, array &$params)
    {
        if ($types !== '') {
            $bindParams = [];
            $bindParams[] = $types;
            foreach ($params as $key => &$value) {
                $bindParams[] = &$params[$key];
            }
            call_user_func_array([$stmt, 'bind_param'], $bindParams);
        }

        return $stmt->execute();
    }
}

if (!function_exists('ems_admin_fetch_rows_prepared')) {
    function ems_admin_fetch_rows_prepared($conn, $sql, $types = '', array $params = [])
    {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        $ok = ems_admin_bind_and_execute($stmt, $types, $params);
        if (!$ok) {
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return is_array($rows) ? $rows : [];
    }
}

if (!function_exists('ems_admin_fetch_row_prepared')) {
    function ems_admin_fetch_row_prepared($conn, $sql, $types = '', array $params = [])
    {
        $rows = ems_admin_fetch_rows_prepared($conn, $sql, $types, $params);
        return isset($rows[0]) && is_array($rows[0]) ? $rows[0] : null;
    }
}

if (!function_exists('ems_admin_exec_prepared_row')) {
    function ems_admin_exec_prepared_row($conn, $sql, $types = '', array $params = [])
    {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'affected' => 0, 'insert_id' => 0];
        }

        $ok = ems_admin_bind_and_execute($stmt, $types, $params);
        $affected = $ok ? (int)$stmt->affected_rows : 0;
        $insertId = $ok ? (int)$stmt->insert_id : 0;
        $stmt->close();

        return ['ok' => (bool)$ok, 'affected' => $affected, 'insert_id' => $insertId];
    }
}

if (!function_exists('ems_admin_time_ago')) {
    function ems_admin_time_ago($dateTime)
    {
        $ts = strtotime((string)$dateTime);
        if ($ts === false) {
            return 'Just now';
        }

        $diff = max(0, time() - $ts);
        if ($diff < 60) {
            return $diff . ' sec ago';
        }
        if ($diff < 3600) {
            return floor($diff / 60) . ' min ago';
        }
        if ($diff < 86400) {
            return floor($diff / 3600) . ' hour' . (floor($diff / 3600) === 1 ? '' : 's') . ' ago';
        }
        if ($diff < 604800) {
            return floor($diff / 86400) . ' day' . (floor($diff / 86400) === 1 ? '' : 's') . ' ago';
        }

        return date('d M Y', $ts);
    }
}

if (!function_exists('ems_admin_bool_to_int')) {
    function ems_admin_bool_to_int($value)
    {
        return !empty($value) ? 1 : 0;
    }
}

if (!function_exists('ems_admin_log_activity')) {
    function ems_admin_log_activity($conn, $officerUserId, $actionType, $entityType, $entityId = 0, array $details = [])
    {
        if (!ems_admin_table_exists($conn, 'admin_activity_logs')) {
            return false;
        }

        $payload = !empty($details)
            ? json_encode($details, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : null;

        $result = ems_admin_exec_prepared_row(
            $conn,
            'INSERT INTO admin_activity_logs (officer_user_id, action_type, entity_type, entity_id, details_json, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())',
            'issis',
            [(int)$officerUserId, (string)$actionType, (string)$entityType, (int)$entityId, $payload]
        );

        return !empty($result['ok']);
    }
}

if (!function_exists('ems_admin_create_notification')) {
    function ems_admin_create_notification($conn, $officerUserId, $title, $message)
    {
        if (!ems_admin_table_exists($conn, 'admin_notifications')) {
            return false;
        }

        $result = ems_admin_exec_prepared_row(
            $conn,
            'INSERT INTO admin_notifications (officer_user_id, title, message_text, is_read, created_at)
             VALUES (?, ?, ?, 0, NOW())',
            'iss',
            [(int)$officerUserId, trim((string)$title), trim((string)$message)]
        );

        return !empty($result['ok']);
    }
}

if (!function_exists('ems_admin_fetch_notifications')) {
    function ems_admin_fetch_notifications($conn, $officerUserId, $limit = 5)
    {
        if (!ems_admin_table_exists($conn, 'admin_notifications')) {
            return [];
        }

        $safeLimit = max(1, min(50, (int)$limit));
        $rows = ems_admin_fetch_rows_prepared(
            $conn,
            "SELECT id, title, message_text, is_read, created_at
             FROM admin_notifications
             WHERE officer_user_id = ?
             ORDER BY created_at DESC
             LIMIT {$safeLimit}",
            'i',
            [(int)$officerUserId]
        );

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id' => (int)($row['id'] ?? 0),
                'title' => (string)($row['title'] ?? 'Notification'),
                'message' => (string)($row['message_text'] ?? ''),
                'is_read' => (int)($row['is_read'] ?? 0) === 1,
                'created_at' => $row['created_at'] ?? null,
                'time_ago' => ems_admin_time_ago($row['created_at'] ?? null),
            ];
        }

        return $result;
    }
}

if (!function_exists('ems_admin_count_unread_notifications')) {
    function ems_admin_count_unread_notifications($conn, $officerUserId)
    {
        if (!ems_admin_table_exists($conn, 'admin_notifications')) {
            return 0;
        }

        $row = ems_admin_fetch_row_prepared(
            $conn,
            'SELECT COUNT(*) AS total FROM admin_notifications WHERE officer_user_id = ? AND is_read = 0',
            'i',
            [(int)$officerUserId]
        );

        return (int)($row['total'] ?? 0);
    }
}

if (!function_exists('ems_admin_mark_notification_read')) {
    function ems_admin_mark_notification_read($conn, $officerUserId, $notificationId)
    {
        if (!ems_admin_table_exists($conn, 'admin_notifications')) {
            return false;
        }

        $result = ems_admin_exec_prepared_row(
            $conn,
            'UPDATE admin_notifications
             SET is_read = 1, read_at = NOW()
             WHERE id = ? AND officer_user_id = ?
             LIMIT 1',
            'ii',
            [(int)$notificationId, (int)$officerUserId]
        );

        return !empty($result['ok']);
    }
}

if (!function_exists('ems_admin_provider_status_meta')) {
    function ems_admin_provider_status_meta($status)
    {
        $value = strtolower(trim((string)$status));
        if ($value === 'pending') {
            return ['status' => 'pending', 'label' => 'Pending Review', 'class' => 'status-pending'];
        }
        if ($value === 'rejected') {
            return ['status' => 'rejected', 'label' => 'Rejected', 'class' => 'status-inactive'];
        }
        if ($value === 'approved') {
            return ['status' => 'approved', 'label' => 'Approved', 'class' => 'status-active'];
        }
        return ['status' => 'draft', 'label' => 'Profile Incomplete', 'class' => 'status-pending'];
    }
}

if (!function_exists('ems_admin_provider_fetch_management_rows')) {
    function ems_admin_provider_fetch_management_rows($conn, $filter = 'all', $limit = 250, $search = '')
    {
        if (!ems_admin_table_exists($conn, 'users') || !ems_admin_table_exists($conn, 'provider_profiles')) {
            return [];
        }

        $safeFilter = strtolower(trim((string)$filter));
        if (!in_array($safeFilter, ['all', 'approved', 'rejected', 'applications'], true)) {
            $safeFilter = 'all';
        }

        $safeLimit = max(1, min(500, (int)$limit));
        $searchValue = strtolower(trim((string)$search));

        $providers = ems_admin_fetch_rows_prepared(
            $conn,
            "SELECT u.id AS provider_user_id, u.full_name, u.email, u.status AS user_status, u.created_at,
                    pp.skill_category, pp.teaching_experience, pp.professional_title
             FROM users u
             LEFT JOIN provider_profiles pp ON pp.user_id = u.id
             WHERE u.role = 'provider'
             ORDER BY u.created_at DESC
             LIMIT {$safeLimit}"
        );

        $approvalMap = [];
        if (ems_admin_table_exists($conn, 'provider_approval_requests')) {
            $approvalRows = ems_admin_fetch_rows_prepared(
                $conn,
                'SELECT id, provider_user_id, request_status, submitted_at, reviewed_at, review_note, completion_score
                 FROM provider_approval_requests'
            );

            foreach ($approvalRows as $approvalRow) {
                $approvalMap[(int)($approvalRow['provider_user_id'] ?? 0)] = $approvalRow;
            }
        }

        $educationCount = [];
        if (ems_admin_table_exists($conn, 'provider_educations')) {
            $rows = ems_admin_fetch_rows_prepared($conn, 'SELECT provider_user_id, COUNT(*) AS total FROM provider_educations GROUP BY provider_user_id');
            foreach ($rows as $row) {
                $educationCount[(int)($row['provider_user_id'] ?? 0)] = (int)($row['total'] ?? 0);
            }
        }

        $experienceCount = [];
        if (ems_admin_table_exists($conn, 'provider_experiences')) {
            $rows = ems_admin_fetch_rows_prepared($conn, 'SELECT provider_user_id, COUNT(*) AS total FROM provider_experiences GROUP BY provider_user_id');
            foreach ($rows as $row) {
                $experienceCount[(int)($row['provider_user_id'] ?? 0)] = (int)($row['total'] ?? 0);
            }
        }

        $certificationCount = [];
        if (ems_admin_table_exists($conn, 'provider_certifications')) {
            $rows = ems_admin_fetch_rows_prepared($conn, 'SELECT provider_user_id, COUNT(*) AS total FROM provider_certifications GROUP BY provider_user_id');
            foreach ($rows as $row) {
                $certificationCount[(int)($row['provider_user_id'] ?? 0)] = (int)($row['total'] ?? 0);
            }
        }

        $result = [];
        foreach ($providers as $providerRow) {
            $providerId = (int)($providerRow['provider_user_id'] ?? 0);
            if ($providerId <= 0) {
                continue;
            }

            $approval = $approvalMap[$providerId] ?? null;
            $status = $approval
                ? strtolower(trim((string)($approval['request_status'] ?? 'draft')))
                : ((string)($providerRow['user_status'] ?? '') === 'active' ? 'approved' : 'draft');

            if (!in_array($status, ['draft', 'pending', 'approved', 'rejected'], true)) {
                $status = 'draft';
            }

            if ($safeFilter === 'applications' && $status !== 'pending') {
                continue;
            }
            if ($safeFilter === 'rejected' && $status !== 'rejected') {
                continue;
            }
            if ($safeFilter === 'approved' && !in_array($status, ['approved', 'draft'], true)) {
                continue;
            }

            $providerName = (string)($providerRow['full_name'] ?? 'Provider');
            $providerEmail = (string)($providerRow['email'] ?? '');
            $specialization = (string)($providerRow['skill_category'] ?? 'General');

            if ($searchValue !== '') {
                $haystack = strtolower($providerName . ' ' . $providerEmail . ' ' . $specialization);
                if (strpos($haystack, $searchValue) === false) {
                    continue;
                }
            }

            $meta = ems_admin_provider_status_meta($status);
            $submittedAt = $approval['submitted_at'] ?? null;
            $joinedAt = $providerRow['created_at'] ?? null;

            $educationTotal = (int)($educationCount[$providerId] ?? 0);
            $experienceTotal = (int)($experienceCount[$providerId] ?? 0);
            $certificationTotal = (int)($certificationCount[$providerId] ?? 0);

            $result[] = [
                'provider_user_id' => $providerId,
                'approval_request_id' => (int)($approval['id'] ?? 0),
                'provider_name' => $providerName,
                'email' => $providerEmail,
                'specialization' => $specialization,
                'experience' => (string)($providerRow['teaching_experience'] ?? 'Not provided'),
                'professional_title' => (string)($providerRow['professional_title'] ?? 'Instructor'),
                'status' => $meta['status'],
                'status_label' => $meta['label'],
                'status_class' => $meta['class'],
                'applied_on' => $submittedAt,
                'applied_on_text' => $submittedAt ? date('d M Y', strtotime((string)$submittedAt)) : '-',
                'joined_on' => $joinedAt,
                'joined_on_text' => $joinedAt ? date('d M Y', strtotime((string)$joinedAt)) : '-',
                'completion_score' => (float)($approval['completion_score'] ?? 0),
                'review_note' => (string)($approval['review_note'] ?? ''),
                'education_count' => $educationTotal,
                'experience_count' => $experienceTotal,
                'certification_count' => $certificationTotal,
                'docs_text' => 'Education: ' . $educationTotal . ' • Experience: ' . $experienceTotal . ' • Certificates: ' . $certificationTotal,
            ];
        }

        return $result;
    }
}

if (!function_exists('ems_admin_provider_review_application')) {
    function ems_admin_provider_review_application($conn, $approvalRequestId, $officerUserId, $decision, $note = '')
    {
        $requestId = (int)$approvalRequestId;
        $reviewerId = (int)$officerUserId;
        $decisionValue = strtolower(trim((string)$decision));

        if ($requestId <= 0 || $reviewerId <= 0) {
            return ['ok' => false, 'message' => 'Invalid review request payload.'];
        }
        if (!in_array($decisionValue, ['approve', 'reject'], true)) {
            return ['ok' => false, 'message' => 'Invalid decision.'];
        }
        if (!ems_admin_table_exists($conn, 'provider_approval_requests')) {
            return ['ok' => false, 'message' => 'Approval request table is unavailable.'];
        }

        $row = ems_admin_fetch_row_prepared(
            $conn,
            'SELECT id, provider_user_id FROM provider_approval_requests WHERE id = ? LIMIT 1',
            'i',
            [$requestId]
        );

        if (!$row) {
            return ['ok' => false, 'message' => 'Approval request not found.'];
        }

        $nextStatus = $decisionValue === 'approve' ? 'approved' : 'rejected';
        $reviewNote = trim((string)$note);

        $conn->begin_transaction();
        try {
            $update = ems_admin_exec_prepared_row(
                $conn,
                'UPDATE provider_approval_requests
                 SET request_status = ?, reviewed_at = NOW(), reviewed_by_user_id = ?, review_note = ?, updated_at = NOW()
                 WHERE id = ?
                 LIMIT 1',
                'sisi',
                [$nextStatus, $reviewerId, $reviewNote !== '' ? $reviewNote : null, $requestId]
            );

            if (!$update['ok']) {
                throw new RuntimeException('Unable to update approval request status.');
            }

            if (ems_admin_table_exists($conn, 'users')) {
                $targetStatus = $nextStatus === 'approved' ? 'active' : 'inactive';
                ems_admin_exec_prepared_row(
                    $conn,
                    'UPDATE users SET status = ? WHERE id = ? AND role = "provider" LIMIT 1',
                    'si',
                    [$targetStatus, (int)($row['provider_user_id'] ?? 0)]
                );
            }

            $conn->commit();

            ems_admin_log_activity(
                $conn,
                $reviewerId,
                'provider_' . $nextStatus,
                'provider_approval_request',
                $requestId,
                [
                    'provider_user_id' => (int)($row['provider_user_id'] ?? 0),
                    'review_note' => $reviewNote,
                ]
            );

            $meta = ems_admin_provider_status_meta($nextStatus);
            return [
                'ok' => true,
                'message' => $nextStatus === 'approved' ? 'Provider application approved.' : 'Provider application rejected.',
                'status' => $meta['status'],
                'status_label' => $meta['label'],
                'status_class' => $meta['class'],
            ];
        } catch (Throwable $throwable) {
            $conn->rollback();
            return ['ok' => false, 'message' => $throwable->getMessage()];
        }
    }
}

if (!function_exists('ems_admin_fetch_dashboard_metrics')) {
    function ems_admin_fetch_dashboard_metrics($conn)
    {
        $metrics = [
            'total_users' => 0,
            'total_learners' => 0,
            'total_providers' => 0,
            'total_officers' => 0,
            'active_courses' => 0,
            'total_revenue' => 0.0,
            'completed_enrollments' => 0,
            'monthly_active_users' => 0,
            'provider_approval_rate' => 0.0,
            'learner_completion_rate' => 0.0,
            'revenue_growth_percent' => 0.0,
            'monthly_revenue' => 0.0,
            'previous_month_revenue' => 0.0,
        ];

        if (ems_admin_table_exists($conn, 'users')) {
            $row = ems_admin_fetch_row_prepared(
                $conn,
                "SELECT COUNT(*) AS total_users,
                        SUM(CASE WHEN role='learner' THEN 1 ELSE 0 END) AS total_learners,
                        SUM(CASE WHEN role='provider' THEN 1 ELSE 0 END) AS total_providers,
                        SUM(CASE WHEN role='officer' THEN 1 ELSE 0 END) AS total_officers,
                        SUM(CASE WHEN created_at >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 ELSE 0 END) AS monthly_active_users
                 FROM users"
            );

            if ($row) {
                $metrics['total_users'] = (int)($row['total_users'] ?? 0);
                $metrics['total_learners'] = (int)($row['total_learners'] ?? 0);
                $metrics['total_providers'] = (int)($row['total_providers'] ?? 0);
                $metrics['total_officers'] = (int)($row['total_officers'] ?? 0);
                $metrics['monthly_active_users'] = (int)($row['monthly_active_users'] ?? 0);
            }
        }

        if (ems_admin_table_exists($conn, 'courses')) {
            $row = ems_admin_fetch_row_prepared(
                $conn,
                "SELECT COUNT(*) AS active_courses FROM courses WHERE status = 'published'"
            );
            $metrics['active_courses'] = (int)($row['active_courses'] ?? 0);
        }

        if (ems_admin_table_exists($conn, 'enrollments') && ems_admin_table_exists($conn, 'courses')) {
            $row = ems_admin_fetch_row_prepared(
                $conn,
                "SELECT
                    SUM(CASE WHEN e.enrollment_status = 'completed' THEN 1 ELSE 0 END) AS completed_enrollments,
                    COALESCE(SUM(CASE WHEN c.access_type='paid' AND e.enrollment_status IN ('active','completed') THEN c.price_amount ELSE 0 END), 0) AS total_revenue,
                    COALESCE(SUM(CASE WHEN c.access_type='paid' AND e.enrollment_status IN ('active','completed')
                        AND e.enrolled_at >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN c.price_amount ELSE 0 END), 0) AS monthly_revenue,
                    COALESCE(SUM(CASE WHEN c.access_type='paid' AND e.enrollment_status IN ('active','completed')
                        AND e.enrolled_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
                        AND e.enrolled_at < DATE_FORMAT(NOW(), '%Y-%m-01') THEN c.price_amount ELSE 0 END), 0) AS previous_month_revenue,
                    SUM(CASE WHEN e.enrollment_status IN ('active','completed') THEN 1 ELSE 0 END) AS active_or_completed
                 FROM enrollments e
                 INNER JOIN courses c ON c.id = e.course_id"
            );

            if ($row) {
                $metrics['completed_enrollments'] = (int)($row['completed_enrollments'] ?? 0);
                $metrics['total_revenue'] = (float)($row['total_revenue'] ?? 0);
                $metrics['monthly_revenue'] = (float)($row['monthly_revenue'] ?? 0);
                $metrics['previous_month_revenue'] = (float)($row['previous_month_revenue'] ?? 0);

                $base = (int)($row['active_or_completed'] ?? 0);
                $metrics['learner_completion_rate'] = $base > 0
                    ? round(($metrics['completed_enrollments'] / $base) * 100, 2)
                    : 0.0;
            }
        }

        if (ems_admin_table_exists($conn, 'provider_approval_requests')) {
            $row = ems_admin_fetch_row_prepared(
                $conn,
                "SELECT
                    SUM(CASE WHEN request_status = 'approved' THEN 1 ELSE 0 END) AS approved_total,
                    SUM(CASE WHEN request_status = 'rejected' THEN 1 ELSE 0 END) AS rejected_total
                 FROM provider_approval_requests"
            );

            $approvedTotal = (int)($row['approved_total'] ?? 0);
            $rejectedTotal = (int)($row['rejected_total'] ?? 0);
            $reviewedTotal = $approvedTotal + $rejectedTotal;

            $metrics['provider_approval_rate'] = $reviewedTotal > 0
                ? round(($approvedTotal / $reviewedTotal) * 100, 2)
                : 0.0;
        }

        $previous = (float)$metrics['previous_month_revenue'];
        $current = (float)$metrics['monthly_revenue'];
        if ($previous > 0) {
            $metrics['revenue_growth_percent'] = round((($current - $previous) / $previous) * 100, 2);
        } elseif ($current > 0) {
            $metrics['revenue_growth_percent'] = 100.0;
        }

        return $metrics;
    }
}

if (!function_exists('ems_admin_fetch_activity_trend')) {
    function ems_admin_fetch_activity_trend($conn, $weeks = 4)
    {
        $safeWeeks = max(2, min(8, (int)$weeks));
        $trend = [];

        for ($i = $safeWeeks - 1; $i >= 0; $i--) {
            $weekStart = strtotime('monday this week -' . $i . ' week');
            $start = date('Y-m-d 00:00:00', $weekStart);
            $end = date('Y-m-d 23:59:59', strtotime('+6 days', $weekStart));
            $label = 'Week ' . ($safeWeeks - $i);
            $count = 0;

            if (ems_admin_table_exists($conn, 'users')) {
                $row = ems_admin_fetch_row_prepared(
                    $conn,
                    'SELECT COUNT(*) AS total FROM users WHERE created_at BETWEEN ? AND ?',
                    'ss',
                    [$start, $end]
                );
                $count += (int)($row['total'] ?? 0);
            }

            if (ems_admin_table_exists($conn, 'enrollments')) {
                $row = ems_admin_fetch_row_prepared(
                    $conn,
                    'SELECT COUNT(*) AS total FROM enrollments WHERE enrolled_at BETWEEN ? AND ?',
                    'ss',
                    [$start, $end]
                );
                $count += (int)($row['total'] ?? 0);
            }

            $trend[] = [
                'label' => $label,
                'value' => $count,
            ];
        }

        return $trend;
    }
}

if (!function_exists('ems_admin_fetch_revenue_breakdown')) {
    function ems_admin_fetch_revenue_breakdown($conn)
    {
        $metrics = ems_admin_fetch_dashboard_metrics($conn);
        $total = max(0.0, (float)($metrics['total_revenue'] ?? 0));

        $coursesAmount = $total;
        $certificatesAmount = 0.0;
        $subscriptionsAmount = 0.0;

        $safeTotal = $total > 0 ? $total : 1;
        return [
            'courses' => [
                'amount' => $coursesAmount,
                'percent' => round(($coursesAmount / $safeTotal) * 100, 2),
            ],
            'certificates' => [
                'amount' => $certificatesAmount,
                'percent' => round(($certificatesAmount / $safeTotal) * 100, 2),
            ],
            'subscriptions' => [
                'amount' => $subscriptionsAmount,
                'percent' => round(($subscriptionsAmount / $safeTotal) * 100, 2),
            ],
        ];
    }
}

if (!function_exists('ems_admin_fetch_recent_activity')) {
    function ems_admin_fetch_recent_activity($conn, $limit = 12)
    {
        $safeLimit = max(1, min(100, (int)$limit));
        $items = [];

        if (ems_admin_table_exists($conn, 'admin_activity_logs')) {
            $rows = ems_admin_fetch_rows_prepared(
                $conn,
                "SELECT l.action_type, l.entity_type, l.entity_id, l.details_json, l.created_at,
                        u.full_name AS actor_name
                 FROM admin_activity_logs l
                 LEFT JOIN users u ON u.id = l.officer_user_id
                 ORDER BY l.created_at DESC
                 LIMIT {$safeLimit}"
            );

            foreach ($rows as $row) {
                $items[] = [
                    'activity_type' => ucwords(str_replace('_', ' ', (string)($row['action_type'] ?? 'Admin Action'))),
                    'user' => (string)($row['actor_name'] ?? 'Admin Officer'),
                    'details' => 'Entity: ' . (string)($row['entity_type'] ?? 'system') . ' #' . (int)($row['entity_id'] ?? 0),
                    'timestamp' => (string)($row['created_at'] ?? ''),
                ];
            }
        }

        if (count($items) < $safeLimit && ems_admin_table_exists($conn, 'users')) {
            $need = $safeLimit - count($items);
            $rows = ems_admin_fetch_rows_prepared(
                $conn,
                "SELECT full_name, role, created_at
                 FROM users
                 ORDER BY created_at DESC
                 LIMIT {$need}"
            );
            foreach ($rows as $row) {
                $items[] = [
                    'activity_type' => 'User Signup',
                    'user' => (string)($row['full_name'] ?? 'User'),
                    'details' => 'New ' . ucfirst((string)($row['role'] ?? 'user')) . ' registered',
                    'timestamp' => (string)($row['created_at'] ?? ''),
                ];
            }
        }

        if (count($items) < $safeLimit && ems_admin_table_exists($conn, 'enrollments') && ems_admin_table_exists($conn, 'users') && ems_admin_table_exists($conn, 'courses')) {
            $need = $safeLimit - count($items);
            $rows = ems_admin_fetch_rows_prepared(
                $conn,
                "SELECT e.enrolled_at, u.full_name AS learner_name, c.title AS course_title
                 FROM enrollments e
                 INNER JOIN users u ON u.id = e.learner_user_id
                 INNER JOIN courses c ON c.id = e.course_id
                 ORDER BY e.enrolled_at DESC
                 LIMIT {$need}"
            );
            foreach ($rows as $row) {
                $items[] = [
                    'activity_type' => 'Enrollment',
                    'user' => (string)($row['learner_name'] ?? 'Learner'),
                    'details' => 'Enrolled in ' . (string)($row['course_title'] ?? 'course'),
                    'timestamp' => (string)($row['enrolled_at'] ?? ''),
                ];
            }
        }

        usort($items, static function ($a, $b) {
            $aTs = strtotime((string)($a['timestamp'] ?? '')) ?: 0;
            $bTs = strtotime((string)($b['timestamp'] ?? '')) ?: 0;
            return $bTs <=> $aTs;
        });

        $items = array_slice($items, 0, $safeLimit);
        foreach ($items as &$item) {
            $item['timestamp_text'] = ems_admin_time_ago($item['timestamp'] ?? null);
        }
        unset($item);

        return $items;
    }
}

if (!function_exists('ems_admin_fetch_learner_management_rows')) {
    function ems_admin_fetch_learner_management_rows($conn, $status = 'all', $search = '', $limit = 250)
    {
        if (!ems_admin_table_exists($conn, 'users')) {
            return [];
        }

        $safeStatus = strtolower(trim((string)$status));
        if (!in_array($safeStatus, ['all', 'active', 'inactive'], true)) {
            $safeStatus = 'all';
        }

        $safeSearch = trim((string)$search);
        $safeLimit = max(1, min(500, (int)$limit));

        $sql = "SELECT u.id, u.full_name, u.email, u.status, u.created_at";
        if (ems_admin_table_exists($conn, 'enrollments')) {
            $sql .= ", COALESCE(e.total_courses, 0) AS enrolled_courses,
                     COALESCE(e.avg_progress, 0) AS avg_progress";
        } else {
            $sql .= ", 0 AS enrolled_courses, 0 AS avg_progress";
        }

        $sql .= " FROM users u";
        if (ems_admin_table_exists($conn, 'enrollments')) {
            $sql .= " LEFT JOIN (
                        SELECT learner_user_id,
                               COUNT(DISTINCT course_id) AS total_courses,
                               COALESCE(AVG(progress_percent), 0) AS avg_progress
                        FROM enrollments
                        GROUP BY learner_user_id
                     ) e ON e.learner_user_id = u.id";
        }

        $sql .= " WHERE u.role = 'learner'";
        $types = '';
        $params = [];

        if ($safeStatus !== 'all') {
            $sql .= ' AND u.status = ?';
            $types .= 's';
            $params[] = $safeStatus;
        }

        if ($safeSearch !== '') {
            $sql .= ' AND (u.full_name LIKE ? OR u.email LIKE ?)';
            $types .= 'ss';
            $like = '%' . $safeSearch . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY u.created_at DESC LIMIT {$safeLimit}";
        $rows = ems_admin_fetch_rows_prepared($conn, $sql, $types, $params);

        foreach ($rows as &$row) {
            $row['enrolled_courses'] = (int)($row['enrolled_courses'] ?? 0);
            $row['avg_progress'] = round((float)($row['avg_progress'] ?? 0), 1);
            $row['status_class'] = strtolower((string)($row['status'] ?? 'inactive')) === 'active' ? 'status-active' : 'status-inactive';
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_admin_course_status_meta')) {
    function ems_admin_course_status_meta($status)
    {
        $value = strtolower(trim((string)$status));
        if ($value === 'published') {
            return ['status' => 'published', 'label' => 'Published', 'class' => 'status-active'];
        }
        if ($value === 'archived') {
            return ['status' => 'archived', 'label' => 'Suspended', 'class' => 'status-inactive'];
        }
        return ['status' => 'draft', 'label' => 'Pending', 'class' => 'status-pending'];
    }
}

if (!function_exists('ems_admin_fetch_course_management_rows')) {
    function ems_admin_fetch_course_management_rows($conn, $status = 'all', $search = '', $limit = 250)
    {
        if (!ems_admin_table_exists($conn, 'courses') || !ems_admin_table_exists($conn, 'users')) {
            return [];
        }

        $safeStatus = strtolower(trim((string)$status));
        if (!in_array($safeStatus, ['all', 'pending', 'published', 'suspended'], true)) {
            $safeStatus = 'all';
        }

        $safeSearch = trim((string)$search);
        $safeLimit = max(1, min(500, (int)$limit));

        $sql = "SELECT c.id, c.title, c.status, c.created_at, c.access_type, c.price_amount, c.currency_code,
                       u.full_name AS instructor_name,
                       COALESCE(pp.skill_category, 'General') AS category";

        if (ems_admin_table_exists($conn, 'enrollments')) {
            $sql .= ", COUNT(DISTINCT CASE WHEN e.enrollment_status IN ('active','completed') THEN e.id END) AS enrollments";
        } else {
            $sql .= ", 0 AS enrollments";
        }

        $sql .= "
            FROM courses c
            INNER JOIN users u ON u.id = c.provider_user_id
            LEFT JOIN provider_profiles pp ON pp.user_id = u.id";

        if (ems_admin_table_exists($conn, 'enrollments')) {
            $sql .= " LEFT JOIN enrollments e ON e.course_id = c.id";
        }

        $sql .= ' WHERE 1=1';
        $types = '';
        $params = [];

        if ($safeStatus === 'pending') {
            $sql .= " AND c.status = 'draft'";
        }
        if ($safeStatus === 'published') {
            $sql .= " AND c.status = 'published'";
        }
        if ($safeStatus === 'suspended') {
            $sql .= " AND c.status = 'archived'";
        }

        if ($safeSearch !== '') {
            $sql .= ' AND (c.title LIKE ? OR u.full_name LIKE ? OR u.email LIKE ?)';
            $types .= 'sss';
            $like = '%' . $safeSearch . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= ' GROUP BY c.id, c.title, c.status, c.created_at, c.access_type, c.price_amount, c.currency_code, u.full_name, pp.skill_category';
        $sql .= " ORDER BY c.created_at DESC LIMIT {$safeLimit}";

        $rows = ems_admin_fetch_rows_prepared($conn, $sql, $types, $params);
        foreach ($rows as &$row) {
            $meta = ems_admin_course_status_meta($row['status'] ?? 'draft');
            $row['status_label'] = $meta['label'];
            $row['status_class'] = $meta['class'];
            $row['enrollments'] = (int)($row['enrollments'] ?? 0);
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_admin_course_review')) {
    function ems_admin_course_review($conn, $courseId, $officerUserId, $decision, $note = '')
    {
        $targetCourseId = (int)$courseId;
        $reviewerId = (int)$officerUserId;
        $decisionValue = strtolower(trim((string)$decision));

        if ($targetCourseId <= 0 || $reviewerId <= 0) {
            return ['ok' => false, 'message' => 'Invalid course review payload.'];
        }
        if (!in_array($decisionValue, ['approve', 'reject', 'archive'], true)) {
            return ['ok' => false, 'message' => 'Invalid review decision.'];
        }
        if (!ems_admin_table_exists($conn, 'courses')) {
            return ['ok' => false, 'message' => 'Course table is unavailable.'];
        }

        $course = ems_admin_fetch_row_prepared(
            $conn,
            'SELECT id, title, status FROM courses WHERE id = ? LIMIT 1',
            'i',
            [$targetCourseId]
        );
        if (!$course) {
            return ['ok' => false, 'message' => 'Course not found.'];
        }

        $nextStatus = $decisionValue === 'approve' ? 'published' : 'archived';
        $sql = 'UPDATE courses SET status = ?, updated_at = NOW()';
        $types = 's';
        $params = [$nextStatus];

        if ($nextStatus === 'published') {
            $sql .= ', published_at = COALESCE(published_at, NOW())';
        }

        if (ems_admin_column_exists($conn, 'courses', 'moderated_by_user_id')) {
            $sql .= ', moderated_by_user_id = ?';
            $types .= 'i';
            $params[] = $reviewerId;
        }
        if (ems_admin_column_exists($conn, 'courses', 'moderated_at')) {
            $sql .= ', moderated_at = NOW()';
        }
        if (ems_admin_column_exists($conn, 'courses', 'moderation_note')) {
            $sql .= ', moderation_note = ?';
            $types .= 's';
            $params[] = trim((string)$note);
        }

        $sql .= ' WHERE id = ? LIMIT 1';
        $types .= 'i';
        $params[] = $targetCourseId;

        $result = ems_admin_exec_prepared_row($conn, $sql, $types, $params);
        if (empty($result['ok'])) {
            return ['ok' => false, 'message' => 'Unable to update course status.'];
        }

        ems_admin_log_activity(
            $conn,
            $reviewerId,
            'course_' . $decisionValue,
            'course',
            $targetCourseId,
            [
                'course_title' => (string)($course['title'] ?? ''),
                'from_status' => (string)($course['status'] ?? 'draft'),
                'to_status' => $nextStatus,
                'note' => trim((string)$note),
            ]
        );

        return [
            'ok' => true,
            'message' => $nextStatus === 'published' ? 'Course approved and published.' : 'Course moved to suspended state.',
            'status' => $nextStatus,
        ];
    }
}

if (!function_exists('ems_admin_fetch_user_management_rows')) {
    function ems_admin_fetch_user_management_rows($conn, $role = 'all', $status = 'all', $search = '', $limit = 250)
    {
        if (!ems_admin_table_exists($conn, 'users')) {
            return [];
        }

        $safeRole = strtolower(trim((string)$role));
        if (!in_array($safeRole, ['all', 'learner', 'provider', 'officer'], true)) {
            $safeRole = 'all';
        }

        $safeStatus = strtolower(trim((string)$status));
        if (!in_array($safeStatus, ['all', 'active', 'inactive'], true)) {
            $safeStatus = 'all';
        }

        $safeSearch = trim((string)$search);
        $safeLimit = max(1, min(500, (int)$limit));

        $sql = 'SELECT id, full_name, email, role, status, created_at FROM users WHERE 1=1';
        $types = '';
        $params = [];

        if ($safeRole !== 'all') {
            $sql .= ' AND role = ?';
            $types .= 's';
            $params[] = $safeRole;
        }
        if ($safeStatus !== 'all') {
            $sql .= ' AND status = ?';
            $types .= 's';
            $params[] = $safeStatus;
        }
        if ($safeSearch !== '') {
            $sql .= ' AND (full_name LIKE ? OR email LIKE ?)';
            $types .= 'ss';
            $like = '%' . $safeSearch . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $sql .= " ORDER BY created_at DESC LIMIT {$safeLimit}";
        $rows = ems_admin_fetch_rows_prepared($conn, $sql, $types, $params);

        foreach ($rows as &$row) {
            $row['role_label'] = ucfirst((string)($row['role'] ?? 'user'));
            $row['status_class'] = strtolower((string)($row['status'] ?? 'inactive')) === 'active' ? 'status-active' : 'status-inactive';
            $createdAt = $row['created_at'] ?? null;
            $row['joined_on_text'] = $createdAt ? date('d M Y', strtotime((string)$createdAt)) : '-';
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_admin_user_update_status')) {
    function ems_admin_user_update_status($conn, $targetUserId, $officerUserId, $nextStatus)
    {
        $userId = (int)$targetUserId;
        $reviewerId = (int)$officerUserId;
        $status = strtolower(trim((string)$nextStatus));

        if ($userId <= 0 || $reviewerId <= 0) {
            return ['ok' => false, 'message' => 'Invalid status update request.'];
        }
        if (!in_array($status, ['active', 'inactive'], true)) {
            return ['ok' => false, 'message' => 'Invalid status value.'];
        }
        if ($userId === $reviewerId && $status === 'inactive') {
            return ['ok' => false, 'message' => 'You cannot deactivate your own account.'];
        }
        if (!ems_admin_table_exists($conn, 'users')) {
            return ['ok' => false, 'message' => 'Users table is unavailable.'];
        }

        $user = ems_admin_fetch_row_prepared($conn, 'SELECT id, full_name, role, status FROM users WHERE id = ? LIMIT 1', 'i', [$userId]);
        if (!$user) {
            return ['ok' => false, 'message' => 'User not found.'];
        }

        $result = ems_admin_exec_prepared_row(
            $conn,
            'UPDATE users SET status = ?, updated_at = NOW() WHERE id = ? LIMIT 1',
            'si',
            [$status, $userId]
        );

        if (empty($result['ok'])) {
            return ['ok' => false, 'message' => 'Unable to update user status.'];
        }

        ems_admin_log_activity(
            $conn,
            $reviewerId,
            'user_status_update',
            'user',
            $userId,
            [
                'full_name' => (string)($user['full_name'] ?? ''),
                'role' => (string)($user['role'] ?? ''),
                'from_status' => (string)($user['status'] ?? ''),
                'to_status' => $status,
            ]
        );

        return [
            'ok' => true,
            'message' => 'User status updated successfully.',
        ];
    }
}

if (!function_exists('ems_admin_fetch_reports_overview')) {
    function ems_admin_fetch_reports_overview($conn)
    {
        $dashboard = ems_admin_fetch_dashboard_metrics($conn);
        $overview = [
            'user_growth_percent' => 0.0,
            'course_completion_rate' => (float)($dashboard['learner_completion_rate'] ?? 0),
            'monthly_revenue' => (float)($dashboard['monthly_revenue'] ?? 0),
            'revenue_growth_percent' => (float)($dashboard['revenue_growth_percent'] ?? 0),
            'avg_rating' => 0.0,
            'review_count' => 0,
            'provider_approval_rate' => (float)($dashboard['provider_approval_rate'] ?? 0),
            'monthly_active_users' => (int)($dashboard['monthly_active_users'] ?? 0),
        ];

        if (ems_admin_table_exists($conn, 'users')) {
            $row = ems_admin_fetch_row_prepared(
                $conn,
                "SELECT
                    SUM(CASE WHEN created_at >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 ELSE 0 END) AS current_month,
                    SUM(CASE WHEN created_at >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01')
                         AND created_at < DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 ELSE 0 END) AS previous_month
                 FROM users"
            );

            $current = (int)($row['current_month'] ?? 0);
            $previous = (int)($row['previous_month'] ?? 0);
            if ($previous > 0) {
                $overview['user_growth_percent'] = round((($current - $previous) / $previous) * 100, 2);
            } elseif ($current > 0) {
                $overview['user_growth_percent'] = 100.0;
            }
        }

        if (ems_admin_table_exists($conn, 'reviews')) {
            $row = ems_admin_fetch_row_prepared(
                $conn,
                'SELECT COALESCE(ROUND(AVG(rating), 1), 0) AS avg_rating, COUNT(*) AS review_count FROM reviews WHERE is_visible = 1'
            );
            $overview['avg_rating'] = (float)($row['avg_rating'] ?? 0);
            $overview['review_count'] = (int)($row['review_count'] ?? 0);
        }

        return $overview;
    }
}

if (!function_exists('ems_admin_default_platform_settings')) {
    function ems_admin_default_platform_settings()
    {
        return [
            'platform_name' => 'EduSkill Marketplace',
            'platform_email' => 'support@eduskill.com',
            'support_phone' => '+977-01-0000000',
            'platform_commission' => '20',
            'minimum_payout_amount' => '100',
            'auto_approve_verified_instructors' => '1',
            'require_content_review' => '0',
        ];
    }
}

if (!function_exists('ems_admin_fetch_platform_settings')) {
    function ems_admin_fetch_platform_settings($conn)
    {
        $settings = ems_admin_default_platform_settings();
        if (!ems_admin_table_exists($conn, 'platform_settings')) {
            return $settings;
        }

        $rows = ems_admin_fetch_rows_prepared($conn, 'SELECT setting_key, setting_value FROM platform_settings');
        foreach ($rows as $row) {
            $key = (string)($row['setting_key'] ?? '');
            if ($key === '') {
                continue;
            }
            $settings[$key] = (string)($row['setting_value'] ?? '');
        }

        return $settings;
    }
}

if (!function_exists('ems_admin_save_platform_settings')) {
    function ems_admin_save_platform_settings($conn, $officerUserId, array $input)
    {
        if (!ems_admin_table_exists($conn, 'platform_settings')) {
            return ['ok' => false, 'message' => 'Platform settings table is unavailable.', 'errors' => []];
        }

        $payload = [
            'platform_name' => trim((string)($input['platform_name'] ?? '')),
            'platform_email' => trim((string)($input['platform_email'] ?? '')),
            'support_phone' => trim((string)($input['support_phone'] ?? '')),
            'platform_commission' => trim((string)($input['platform_commission'] ?? '')),
            'minimum_payout_amount' => trim((string)($input['minimum_payout_amount'] ?? '')),
            'auto_approve_verified_instructors' => !empty($input['auto_approve_verified_instructors']) ? '1' : '0',
            'require_content_review' => !empty($input['require_content_review']) ? '1' : '0',
        ];

        $errors = [];
        if ($payload['platform_name'] === '') {
            $errors['platform_name'] = 'Platform name is required.';
        }
        if (!filter_var($payload['platform_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['platform_email'] = 'A valid platform email is required.';
        }
        if (!is_numeric($payload['platform_commission'])) {
            $errors['platform_commission'] = 'Commission must be numeric.';
        } else {
            $commission = (float)$payload['platform_commission'];
            if ($commission < 0 || $commission > 100) {
                $errors['platform_commission'] = 'Commission must be between 0 and 100.';
            }
        }
        if (!is_numeric($payload['minimum_payout_amount']) || (float)$payload['minimum_payout_amount'] < 0) {
            $errors['minimum_payout_amount'] = 'Minimum payout must be a non-negative number.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'message' => 'Please fix validation errors.', 'errors' => $errors];
        }

        $conn->begin_transaction();
        try {
            foreach ($payload as $key => $value) {
                $result = ems_admin_exec_prepared_row(
                    $conn,
                    'INSERT INTO platform_settings (setting_key, setting_value, updated_by_user_id, updated_at)
                     VALUES (?, ?, ?, NOW())
                     ON DUPLICATE KEY UPDATE
                         setting_value = VALUES(setting_value),
                         updated_by_user_id = VALUES(updated_by_user_id),
                         updated_at = NOW()',
                    'ssi',
                    [(string)$key, (string)$value, (int)$officerUserId]
                );

                if (empty($result['ok'])) {
                    throw new RuntimeException('Unable to persist platform settings.');
                }
            }

            $conn->commit();
            ems_admin_log_activity($conn, (int)$officerUserId, 'settings_update', 'platform_settings', 0, ['keys' => array_keys($payload)]);

            return ['ok' => true, 'message' => 'Platform settings updated successfully.', 'errors' => []];
        } catch (Throwable $throwable) {
            $conn->rollback();
            return ['ok' => false, 'message' => $throwable->getMessage(), 'errors' => []];
        }
    }
}

if (!function_exists('ems_admin_fetch_officer_profile')) {
    function ems_admin_fetch_officer_profile($conn, $officerUserId, array $portalUser = [])
    {
        $officerId = (int)$officerUserId;

        $profile = [
            'id' => $officerId,
            'full_name' => trim((string)($portalUser['full_name'] ?? 'Admin Officer')),
            'email' => trim((string)($portalUser['email'] ?? '')),
            'status' => trim((string)($portalUser['status'] ?? 'active')),
            'joined_on' => !empty($portalUser['created_at']) ? date('F d, Y', strtotime((string)$portalUser['created_at'])) : 'Not available',
            'designation' => 'System Administrator',
            'phone' => '',
            'employee_id' => 'AO-' . str_pad((string)$officerId, 4, '0', STR_PAD_LEFT),
            'department' => 'Platform Operations',
            'location' => 'EduSkill Marketplace Office, Kathmandu, Nepal',
            'timezone' => 'Asia/Kathmandu (UTC+05:45)',
            'language' => 'English',
            'responsibilities' => 'Provider approval workflow, learner complaint resolution, course quality monitoring, and monthly compliance reporting.',
            'last_login_text' => 'Not available',
            'password_meta' => 'Last changed recently',
            'pref_email_alerts' => true,
            'pref_daily_digest' => true,
            'pref_auto_archive' => false,
            'two_factor_enabled' => true,
            'active_sessions' => 1,
        ];

        if (ems_admin_table_exists($conn, 'users')) {
            $userRow = ems_admin_fetch_row_prepared(
                $conn,
                'SELECT full_name, email, status, created_at FROM users WHERE id = ? AND role = "officer" LIMIT 1',
                'i',
                [$officerId]
            );
            if ($userRow) {
                $profile['full_name'] = trim((string)($userRow['full_name'] ?? $profile['full_name']));
                $profile['email'] = trim((string)($userRow['email'] ?? $profile['email']));
                $profile['status'] = trim((string)($userRow['status'] ?? $profile['status']));
                if (!empty($userRow['created_at'])) {
                    $profile['joined_on'] = date('F d, Y', strtotime((string)$userRow['created_at']));
                }
            }
        }

        if (ems_admin_table_exists($conn, 'admin_officer_profiles')) {
            $row = ems_admin_fetch_row_prepared(
                $conn,
                'SELECT designation, phone_number, employee_code, department, office_location, timezone, language_code,
                        responsibilities, last_login_at, password_updated_at, active_sessions
                 FROM admin_officer_profiles
                 WHERE user_id = ?
                 LIMIT 1',
                'i',
                [$officerId]
            );

            if ($row) {
                $profile['designation'] = trim((string)($row['designation'] ?? $profile['designation'])) ?: $profile['designation'];
                $profile['phone'] = trim((string)($row['phone_number'] ?? $profile['phone']));
                $profile['employee_id'] = trim((string)($row['employee_code'] ?? $profile['employee_id'])) ?: $profile['employee_id'];
                $profile['department'] = trim((string)($row['department'] ?? $profile['department'])) ?: $profile['department'];
                $profile['location'] = trim((string)($row['office_location'] ?? $profile['location'])) ?: $profile['location'];
                $profile['timezone'] = trim((string)($row['timezone'] ?? $profile['timezone'])) ?: $profile['timezone'];
                $profile['language'] = trim((string)($row['language_code'] ?? $profile['language'])) ?: $profile['language'];
                $profile['responsibilities'] = trim((string)($row['responsibilities'] ?? $profile['responsibilities'])) ?: $profile['responsibilities'];

                if (!empty($row['last_login_at'])) {
                    $profile['last_login_text'] = date('M d, Y h:i A', strtotime((string)$row['last_login_at']));
                }
                if (!empty($row['password_updated_at'])) {
                    $profile['password_meta'] = 'Last changed on ' . date('M d, Y', strtotime((string)$row['password_updated_at']));
                }
                $profile['active_sessions'] = max(1, (int)($row['active_sessions'] ?? 1));
            }
        }

        if (ems_admin_table_exists($conn, 'admin_officer_preferences')) {
            $row = ems_admin_fetch_row_prepared(
                $conn,
                'SELECT pref_email_alerts, pref_daily_digest, pref_auto_archive, two_factor_enabled
                 FROM admin_officer_preferences
                 WHERE user_id = ?
                 LIMIT 1',
                'i',
                [$officerId]
            );

            if ($row) {
                $profile['pref_email_alerts'] = (int)($row['pref_email_alerts'] ?? 1) === 1;
                $profile['pref_daily_digest'] = (int)($row['pref_daily_digest'] ?? 1) === 1;
                $profile['pref_auto_archive'] = (int)($row['pref_auto_archive'] ?? 0) === 1;
                $profile['two_factor_enabled'] = (int)($row['two_factor_enabled'] ?? 1) === 1;
            }
        }

        return $profile;
    }
}

if (!function_exists('ems_admin_update_officer_profile')) {
    function ems_admin_update_officer_profile($conn, $officerUserId, array $payload)
    {
        $officerId = (int)$officerUserId;
        if ($officerId <= 0) {
            return ['ok' => false, 'message' => 'Invalid officer account.', 'errors' => []];
        }

        $data = [
            'full_name' => trim((string)($payload['full_name'] ?? '')),
            'email' => trim((string)($payload['email'] ?? '')),
            'designation' => trim((string)($payload['designation'] ?? 'System Administrator')),
            'phone' => trim((string)($payload['phone'] ?? '')),
            'employee_id' => trim((string)($payload['employee_id'] ?? ('AO-' . str_pad((string)$officerId, 4, '0', STR_PAD_LEFT)))),
            'department' => trim((string)($payload['department'] ?? 'Platform Operations')),
            'location' => trim((string)($payload['location'] ?? '')),
            'timezone' => trim((string)($payload['timezone'] ?? 'Asia/Kathmandu (UTC+05:45)')),
            'language' => trim((string)($payload['language'] ?? 'English')),
            'responsibilities' => trim((string)($payload['responsibilities'] ?? '')),
        ];

        $errors = [];
        if ($data['full_name'] === '' || mb_strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Full name must contain at least 2 characters.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email address is required.';
        }
        if ($data['phone'] !== '' && !preg_match('/^[0-9+\-()\s]{7,20}$/', $data['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }

        if (!empty($errors)) {
            return ['ok' => false, 'message' => 'Please fix validation errors.', 'errors' => $errors];
        }

        if (ems_admin_table_exists($conn, 'users')) {
            $existing = ems_admin_fetch_row_prepared(
                $conn,
                'SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1',
                'si',
                [$data['email'], $officerId]
            );
            if ($existing) {
                return ['ok' => false, 'message' => 'Email is already in use by another user.', 'errors' => ['email' => 'Email is already in use.']];
            }
        }

        $conn->begin_transaction();
        try {
            if (ems_admin_table_exists($conn, 'users')) {
                $userUpdate = ems_admin_exec_prepared_row(
                    $conn,
                    'UPDATE users SET full_name = ?, email = ?, updated_at = NOW() WHERE id = ? AND role = "officer" LIMIT 1',
                    'ssi',
                    [$data['full_name'], $data['email'], $officerId]
                );

                if (empty($userUpdate['ok'])) {
                    throw new RuntimeException('Unable to update officer account.');
                }
            }

            if (ems_admin_table_exists($conn, 'admin_officer_profiles')) {
                $profileUpdate = ems_admin_exec_prepared_row(
                    $conn,
                    'INSERT INTO admin_officer_profiles
                        (user_id, designation, phone_number, employee_code, department, office_location, timezone, language_code, responsibilities, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                     ON DUPLICATE KEY UPDATE
                        designation = VALUES(designation),
                        phone_number = VALUES(phone_number),
                        employee_code = VALUES(employee_code),
                        department = VALUES(department),
                        office_location = VALUES(office_location),
                        timezone = VALUES(timezone),
                        language_code = VALUES(language_code),
                        responsibilities = VALUES(responsibilities),
                        updated_at = NOW()',
                    'issssssss',
                    [
                        $officerId,
                        $data['designation'],
                        $data['phone'],
                        $data['employee_id'],
                        $data['department'],
                        $data['location'],
                        $data['timezone'],
                        $data['language'],
                        $data['responsibilities'],
                    ]
                );

                if (empty($profileUpdate['ok'])) {
                    throw new RuntimeException('Unable to update officer profile details.');
                }
            }

            $conn->commit();
            ems_admin_log_activity($conn, $officerId, 'profile_update', 'officer_profile', $officerId, ['full_name' => $data['full_name']]);

            return ['ok' => true, 'message' => 'Profile updated successfully.', 'errors' => []];
        } catch (Throwable $throwable) {
            $conn->rollback();
            return ['ok' => false, 'message' => $throwable->getMessage(), 'errors' => []];
        }
    }
}

if (!function_exists('ems_admin_update_officer_preferences')) {
    function ems_admin_update_officer_preferences($conn, $officerUserId, array $payload)
    {
        $officerId = (int)$officerUserId;
        if ($officerId <= 0) {
            return ['ok' => false, 'message' => 'Invalid officer account.'];
        }
        if (!ems_admin_table_exists($conn, 'admin_officer_preferences')) {
            return ['ok' => false, 'message' => 'Officer preferences table is unavailable.'];
        }

        $emailAlerts = ems_admin_bool_to_int($payload['pref_email_alerts'] ?? 0);
        $dailyDigest = ems_admin_bool_to_int($payload['pref_daily_digest'] ?? 0);
        $autoArchive = ems_admin_bool_to_int($payload['pref_auto_archive'] ?? 0);
        $twoFactor = ems_admin_bool_to_int($payload['two_factor_enabled'] ?? 0);

        $result = ems_admin_exec_prepared_row(
            $conn,
            'INSERT INTO admin_officer_preferences
                (user_id, pref_email_alerts, pref_daily_digest, pref_auto_archive, two_factor_enabled, updated_at)
             VALUES (?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                pref_email_alerts = VALUES(pref_email_alerts),
                pref_daily_digest = VALUES(pref_daily_digest),
                pref_auto_archive = VALUES(pref_auto_archive),
                two_factor_enabled = VALUES(two_factor_enabled),
                updated_at = NOW()',
            'iiiii',
            [$officerId, $emailAlerts, $dailyDigest, $autoArchive, $twoFactor]
        );

        if (empty($result['ok'])) {
            return ['ok' => false, 'message' => 'Unable to update preferences.'];
        }

        ems_admin_log_activity($conn, $officerId, 'preferences_update', 'officer_preferences', $officerId, []);
        return ['ok' => true, 'message' => 'Preferences updated successfully.'];
    }
}

if (!function_exists('ems_admin_fetch_provider_performance_rows')) {
    function ems_admin_fetch_provider_performance_rows($conn, $limit = 200)
    {
        if (!ems_admin_table_exists($conn, 'users') || !ems_admin_table_exists($conn, 'courses')) {
            return [];
        }

        $safeLimit = max(1, min(1000, (int)$limit));
        $sql = "SELECT u.id, u.full_name, u.email,
                       COUNT(DISTINCT c.id) AS total_courses,
                       COUNT(DISTINCT CASE WHEN c.status = 'published' THEN c.id END) AS published_courses";

        if (ems_admin_table_exists($conn, 'enrollments')) {
            $sql .= ", COUNT(DISTINCT CASE WHEN e.enrollment_status IN ('active','completed') THEN e.id END) AS total_enrollments,
                     COALESCE(SUM(CASE WHEN c.access_type = 'paid' AND e.enrollment_status IN ('active','completed') THEN c.price_amount ELSE 0 END), 0) AS total_revenue";
        } else {
            $sql .= ", 0 AS total_enrollments, 0 AS total_revenue";
        }

        if (ems_admin_table_exists($conn, 'reviews')) {
            $sql .= ", COALESCE(ROUND(AVG(CASE WHEN r.is_visible = 1 THEN r.rating END), 2), 0) AS avg_rating";
        } else {
            $sql .= ", 0 AS avg_rating";
        }

        $sql .= "
            FROM users u
            LEFT JOIN courses c ON c.provider_user_id = u.id";

        if (ems_admin_table_exists($conn, 'enrollments')) {
            $sql .= ' LEFT JOIN enrollments e ON e.course_id = c.id';
        }
        if (ems_admin_table_exists($conn, 'reviews')) {
            $sql .= ' LEFT JOIN reviews r ON r.course_id = c.id';
        }

        $sql .= "
            WHERE u.role = 'provider'
            GROUP BY u.id, u.full_name, u.email
            ORDER BY total_revenue DESC, total_enrollments DESC
            LIMIT {$safeLimit}";

        return ems_admin_fetch_rows_prepared($conn, $sql);
    }
}

if (!function_exists('ems_admin_report_export_payload')) {
    function ems_admin_report_export_payload($conn, $reportType)
    {
        $type = strtolower(trim((string)$reportType));
        $today = date('Ymd_His');

        if ($type === 'provider-review') {
            $rows = ems_admin_provider_fetch_management_rows($conn, 'all', 1000);
            return [
                'filename' => 'provider-review-report-' . $today . '.csv',
                'headers' => ['Provider Name', 'Email', 'Specialization', 'Status', 'Applied On', 'Joined On', 'Reviewer Note'],
                'rows' => array_map(static function ($row) {
                    return [
                        (string)($row['provider_name'] ?? ''),
                        (string)($row['email'] ?? ''),
                        (string)($row['specialization'] ?? ''),
                        (string)($row['status_label'] ?? ''),
                        (string)($row['applied_on_text'] ?? '-'),
                        (string)($row['joined_on_text'] ?? '-'),
                        (string)($row['review_note'] ?? ''),
                    ];
                }, $rows),
            ];
        }

        if ($type === 'user-engagement' || $type === 'learner-engagement') {
            $rows = ems_admin_fetch_learner_management_rows($conn, 'all', '', 1000);
            return [
                'filename' => 'learner-engagement-report-' . $today . '.csv',
                'headers' => ['Learner Name', 'Email', 'Enrolled Courses', 'Average Progress', 'Status'],
                'rows' => array_map(static function ($row) {
                    return [
                        (string)($row['full_name'] ?? ''),
                        (string)($row['email'] ?? ''),
                        (string)($row['enrolled_courses'] ?? 0),
                        (string)($row['avg_progress'] ?? 0) . '%',
                        ucfirst((string)($row['status'] ?? 'inactive')),
                    ];
                }, $rows),
            ];
        }

        if ($type === 'instructor-performance') {
            $rows = ems_admin_fetch_provider_performance_rows($conn, 1000);
            return [
                'filename' => 'instructor-performance-report-' . $today . '.csv',
                'headers' => ['Provider Name', 'Email', 'Total Courses', 'Published Courses', 'Enrollments', 'Revenue (USD)', 'Average Rating'],
                'rows' => array_map(static function ($row) {
                    return [
                        (string)($row['full_name'] ?? ''),
                        (string)($row['email'] ?? ''),
                        (string)($row['total_courses'] ?? 0),
                        (string)($row['published_courses'] ?? 0),
                        (string)($row['total_enrollments'] ?? 0),
                        number_format((float)($row['total_revenue'] ?? 0), 2),
                        number_format((float)($row['avg_rating'] ?? 0), 2),
                    ];
                }, $rows),
            ];
        }

        $dashboard = ems_admin_fetch_dashboard_metrics($conn);
        $overview = ems_admin_fetch_reports_overview($conn);
        return [
            'filename' => 'monthly-performance-report-' . $today . '.csv',
            'headers' => ['Metric', 'Value'],
            'rows' => [
                ['Total Users', (string)($dashboard['total_users'] ?? 0)],
                ['Total Learners', (string)($dashboard['total_learners'] ?? 0)],
                ['Total Providers', (string)($dashboard['total_providers'] ?? 0)],
                ['Active Courses', (string)($dashboard['active_courses'] ?? 0)],
                ['Completed Enrollments', (string)($dashboard['completed_enrollments'] ?? 0)],
                ['Monthly Revenue (USD)', number_format((float)($dashboard['monthly_revenue'] ?? 0), 2)],
                ['Total Revenue (USD)', number_format((float)($dashboard['total_revenue'] ?? 0), 2)],
                ['User Growth (%)', number_format((float)($overview['user_growth_percent'] ?? 0), 2)],
                ['Provider Approval Rate (%)', number_format((float)($dashboard['provider_approval_rate'] ?? 0), 2)],
                ['Learner Completion Rate (%)', number_format((float)($dashboard['learner_completion_rate'] ?? 0), 2)],
            ],
        ];
    }
}

