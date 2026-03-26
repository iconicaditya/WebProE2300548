<?php

if (!function_exists('ems_provider_tables_ready')) {
    function ems_provider_tables_ready($conn)
    {
        static $ready = null;
        if ($ready !== null) {
            return $ready;
        }

        $required = ['courses', 'course_sections', 'course_lessons', 'course_resources', 'enrollments', 'reviews'];
        foreach ($required as $table) {
            $stmt = $conn->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1');
            if (!$stmt) {
                $ready = false;
                return $ready;
            }
            $stmt->bind_param('s', $table);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result && $result->num_rows > 0;
            $stmt->close();

            if (!$exists) {
                $ready = false;
                return $ready;
            }
        }

        $ready = true;
        return $ready;
    }
}

if (!function_exists('ems_provider_currency_format')) {
    function ems_provider_currency_format($amount, $currency = 'USD')
    {
        $value = (float)$amount;
        if ($currency === 'NPR') {
            return 'NPR ' . number_format($value, 2);
        }
        if ($currency === 'EUR') {
            return '€' . number_format($value, 2);
        }
        return '$' . number_format($value, 2);
    }
}

if (!function_exists('ems_provider_course_status_badge')) {
    function ems_provider_course_status_badge($status)
    {
        $value = strtolower(trim((string)$status));
        if ($value === 'published') {
            return ['label' => 'Published', 'class' => 'status-badge status-active'];
        }
        if ($value === 'draft') {
            return ['label' => 'Draft', 'class' => 'payment-badge payment-pending'];
        }
        if ($value === 'archived') {
            return ['label' => 'Archived', 'class' => 'status-badge status-inactive'];
        }
        return ['label' => 'Unknown', 'class' => 'status-badge status-inactive'];
    }
}

if (!function_exists('ems_provider_fetch_courses')) {
    function ems_provider_fetch_courses($conn, $providerUserId, $limit = 50)
    {
        if (!ems_provider_tables_ready($conn)) {
            return [];
        }

        $limit = max(1, min(200, (int)$limit));
        $sql = "
            SELECT
                c.id,
                c.title,
                c.status,
                c.access_type,
                c.price_amount,
                c.currency_code,
                c.created_at,
                COUNT(DISTINCT CASE WHEN e.enrollment_status IN ('active','completed') THEN e.id END) AS students_count,
                COALESCE(ROUND(AVG(CASE WHEN r.is_visible = 1 THEN r.rating END), 1), 0) AS avg_rating,
                COUNT(DISTINCT CASE WHEN r.is_visible = 1 THEN r.id END) AS review_count
            FROM courses c
            LEFT JOIN enrollments e ON e.course_id = c.id
            LEFT JOIN reviews r ON r.course_id = c.id
            WHERE c.provider_user_id = ?
            GROUP BY c.id
            ORDER BY c.created_at DESC
            LIMIT {$limit}
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $providerUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return is_array($rows) ? $rows : [];
    }
}

if (!function_exists('ems_provider_fetch_dashboard_metrics')) {
    function ems_provider_fetch_dashboard_metrics($conn, $providerUserId)
    {
        $metrics = [
            'total_courses' => 0,
            'published_courses' => 0,
            'draft_courses' => 0,
            'total_students' => 0,
            'monthly_students' => 0,
            'monthly_revenue' => 0.0,
            'total_revenue' => 0.0,
            'avg_rating' => 0.0,
            'review_count' => 0,
            'completion_rate' => 0.0,
        ];

        if (!ems_provider_tables_ready($conn)) {
            return $metrics;
        }

        $courseStmt = $conn->prepare("SELECT
            COUNT(*) AS total_courses,
            SUM(CASE WHEN status='published' THEN 1 ELSE 0 END) AS published_courses,
            SUM(CASE WHEN status='draft' THEN 1 ELSE 0 END) AS draft_courses
            FROM courses
            WHERE provider_user_id = ?
        ");
        if ($courseStmt) {
            $courseStmt->bind_param('i', $providerUserId);
            $courseStmt->execute();
            $courseRow = $courseStmt->get_result()->fetch_assoc();
            $courseStmt->close();
            if ($courseRow) {
                $metrics['total_courses'] = (int)($courseRow['total_courses'] ?? 0);
                $metrics['published_courses'] = (int)($courseRow['published_courses'] ?? 0);
                $metrics['draft_courses'] = (int)($courseRow['draft_courses'] ?? 0);
            }
        }

        $enrollmentStmt = $conn->prepare("SELECT
            COUNT(DISTINCT CASE WHEN e.enrollment_status IN ('active','completed') THEN e.id END) AS total_students,
            COUNT(DISTINCT CASE WHEN e.enrollment_status IN ('active','completed') AND e.enrolled_at >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN e.id END) AS monthly_students,
            COALESCE(AVG(CASE WHEN e.enrollment_status IN ('active','completed') THEN e.progress_percent END), 0) AS completion_rate,
            COALESCE(SUM(CASE WHEN c.access_type='paid' AND e.enrollment_status IN ('active','completed') THEN c.price_amount ELSE 0 END), 0) AS total_revenue,
            COALESCE(SUM(CASE WHEN c.access_type='paid' AND e.enrollment_status IN ('active','completed') AND e.enrolled_at >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN c.price_amount ELSE 0 END), 0) AS monthly_revenue
            FROM courses c
            LEFT JOIN enrollments e ON e.course_id = c.id
            WHERE c.provider_user_id = ?
        ");
        if ($enrollmentStmt) {
            $enrollmentStmt->bind_param('i', $providerUserId);
            $enrollmentStmt->execute();
            $enrollmentRow = $enrollmentStmt->get_result()->fetch_assoc();
            $enrollmentStmt->close();
            if ($enrollmentRow) {
                $metrics['total_students'] = (int)($enrollmentRow['total_students'] ?? 0);
                $metrics['monthly_students'] = (int)($enrollmentRow['monthly_students'] ?? 0);
                $metrics['completion_rate'] = (float)($enrollmentRow['completion_rate'] ?? 0);
                $metrics['total_revenue'] = (float)($enrollmentRow['total_revenue'] ?? 0);
                $metrics['monthly_revenue'] = (float)($enrollmentRow['monthly_revenue'] ?? 0);
            }
        }

        $ratingStmt = $conn->prepare("SELECT
            COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating,
            COUNT(*) AS review_count
            FROM reviews r
            INNER JOIN courses c ON c.id = r.course_id
            WHERE c.provider_user_id = ? AND r.is_visible = 1
        ");
        if ($ratingStmt) {
            $ratingStmt->bind_param('i', $providerUserId);
            $ratingStmt->execute();
            $ratingRow = $ratingStmt->get_result()->fetch_assoc();
            $ratingStmt->close();
            if ($ratingRow) {
                $metrics['avg_rating'] = (float)($ratingRow['avg_rating'] ?? 0);
                $metrics['review_count'] = (int)($ratingRow['review_count'] ?? 0);
            }
        }

        return $metrics;
    }
}

if (!function_exists('ems_provider_fetch_recent_enrollments')) {
    function ems_provider_fetch_recent_enrollments($conn, $providerUserId, $limit = 20)
    {
        if (!ems_provider_tables_ready($conn)) {
            return [];
        }

        $limit = max(1, min(200, (int)$limit));
        $sql = "
            SELECT
                e.id,
                e.enrolled_at,
                e.enrollment_status,
                e.progress_percent,
                u.full_name AS learner_name,
                u.email AS learner_email,
                c.title AS course_title,
                c.access_type,
                c.price_amount,
                c.currency_code
            FROM enrollments e
            INNER JOIN courses c ON c.id = e.course_id
            INNER JOIN users u ON u.id = e.learner_user_id
            WHERE c.provider_user_id = ?
            ORDER BY e.enrolled_at DESC
            LIMIT {$limit}
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $providerUserId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return is_array($rows) ? $rows : [];
    }
}

if (!function_exists('ems_provider_fetch_recent_reviews')) {
    function ems_provider_fetch_recent_reviews($conn, $providerUserId, $limit = 20)
    {
        if (!ems_provider_tables_ready($conn)) {
            return [];
        }

        $limit = max(1, min(200, (int)$limit));
        $sql = "
            SELECT
                r.id,
                r.rating,
                r.review_text,
                r.created_at,
                u.full_name AS learner_name,
                c.title AS course_title
            FROM reviews r
            INNER JOIN courses c ON c.id = r.course_id
            INNER JOIN users u ON u.id = r.learner_user_id
            WHERE c.provider_user_id = ? AND r.is_visible = 1
            ORDER BY r.created_at DESC
            LIMIT {$limit}
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $providerUserId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return is_array($rows) ? $rows : [];
    }
}

if (!function_exists('ems_provider_fetch_rating_breakdown')) {
    function ems_provider_fetch_rating_breakdown($conn, $providerUserId)
    {
        $breakdown = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        if (!ems_provider_tables_ready($conn)) {
            return $breakdown;
        }

        $stmt = $conn->prepare("SELECT r.rating, COUNT(*) AS total
            FROM reviews r
            INNER JOIN courses c ON c.id = r.course_id
            WHERE c.provider_user_id = ? AND r.is_visible = 1
            GROUP BY r.rating
        ");
        if (!$stmt) {
            return $breakdown;
        }
        $stmt->bind_param('i', $providerUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $rating = (int)($row['rating'] ?? 0);
            if ($rating >= 1 && $rating <= 5) {
                $breakdown[$rating] = (int)($row['total'] ?? 0);
            }
        }
        $stmt->close();

        return $breakdown;
    }
}

if (!function_exists('ems_provider_fetch_monthly_enrollment_trend')) {
    function ems_provider_fetch_monthly_enrollment_trend($conn, $providerUserId, $months = 6)
    {
        $months = max(1, min(12, (int)$months));
        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $key = date('Y-m', strtotime('-' . $i . ' months'));
            $trend[$key] = 0;
        }

        if (!ems_provider_tables_ready($conn)) {
            return $trend;
        }

        $startDate = date('Y-m-01', strtotime('-' . ($months - 1) . ' months'));
        $stmt = $conn->prepare("SELECT DATE_FORMAT(e.enrolled_at, '%Y-%m') AS ym, COUNT(*) AS total
            FROM enrollments e
            INNER JOIN courses c ON c.id = e.course_id
            WHERE c.provider_user_id = ? AND e.enrolled_at >= ?
            GROUP BY ym
            ORDER BY ym ASC
        ");
        if (!$stmt) {
            return $trend;
        }
        $stmt->bind_param('is', $providerUserId, $startDate);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $key = (string)($row['ym'] ?? '');
            if (array_key_exists($key, $trend)) {
                $trend[$key] = (int)($row['total'] ?? 0);
            }
        }
        $stmt->close();

        return $trend;
    }
}

if (!function_exists('ems_provider_fetch_monthly_revenue_trend')) {
    function ems_provider_fetch_monthly_revenue_trend($conn, $providerUserId, $months = 6)
    {
        $months = max(1, min(12, (int)$months));
        $trend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $key = date('Y-m', strtotime('-' . $i . ' months'));
            $trend[$key] = 0.0;
        }

        if (!ems_provider_tables_ready($conn)) {
            return $trend;
        }

        $startDate = date('Y-m-01', strtotime('-' . ($months - 1) . ' months'));
        $stmt = $conn->prepare("SELECT DATE_FORMAT(e.enrolled_at, '%Y-%m') AS ym,
                COALESCE(SUM(CASE WHEN c.access_type='paid' AND e.enrollment_status IN ('active','completed') THEN c.price_amount ELSE 0 END), 0) AS total
            FROM enrollments e
            INNER JOIN courses c ON c.id = e.course_id
            WHERE c.provider_user_id = ? AND e.enrolled_at >= ?
            GROUP BY ym
            ORDER BY ym ASC
        ");
        if (!$stmt) {
            return $trend;
        }
        $stmt->bind_param('is', $providerUserId, $startDate);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $key = (string)($row['ym'] ?? '');
            if (array_key_exists($key, $trend)) {
                $trend[$key] = (float)($row['total'] ?? 0);
            }
        }
        $stmt->close();

        return $trend;
    }
}

if (!function_exists('ems_provider_table_exists')) {
    function ems_provider_table_exists($conn, $tableName)
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

        $cache[$table] = $exists;
        return $exists;
    }
}

if (!function_exists('ems_provider_bind_and_execute')) {
    function ems_provider_bind_and_execute($stmt, $types, array &$params)
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

if (!function_exists('ems_provider_fetch_rows_prepared')) {
    function ems_provider_fetch_rows_prepared($conn, $sql, $types = '', array $params = [])
    {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        ems_provider_bind_and_execute($stmt, $types, $params);
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return is_array($rows) ? $rows : [];
    }
}

if (!function_exists('ems_provider_fetch_row_prepared')) {
    function ems_provider_fetch_row_prepared($conn, $sql, $types = '', array $params = [])
    {
        $rows = ems_provider_fetch_rows_prepared($conn, $sql, $types, $params);
        return isset($rows[0]) && is_array($rows[0]) ? $rows[0] : null;
    }
}

if (!function_exists('ems_provider_exec_prepared_row')) {
    function ems_provider_exec_prepared_row($conn, $sql, $types = '', array $params = [])
    {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'affected' => 0, 'insert_id' => 0];
        }

        $ok = ems_provider_bind_and_execute($stmt, $types, $params);
        $affected = $ok ? (int)$stmt->affected_rows : 0;
        $insertId = $ok ? (int)$stmt->insert_id : 0;
        $stmt->close();

        return ['ok' => (bool)$ok, 'affected' => $affected, 'insert_id' => $insertId];
    }
}

if (!function_exists('ems_provider_profile_tables_ready')) {
    function ems_provider_profile_tables_ready($conn)
    {
        $required = [
            'users',
            'provider_profiles',
            'provider_educations',
            'provider_experiences',
            'provider_certifications',
            'provider_approval_requests',
        ];

        foreach ($required as $table) {
            if (!ems_provider_table_exists($conn, $table)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('ems_provider_fetch_profile_counts')) {
    function ems_provider_fetch_profile_counts($conn, $providerUserId)
    {
        $providerId = (int)$providerUserId;
        $counts = [
            'educations' => 0,
            'experiences' => 0,
            'certifications' => 0,
        ];

        if ($providerId <= 0) {
            return $counts;
        }

        if (ems_provider_table_exists($conn, 'provider_educations')) {
            $row = ems_provider_fetch_row_prepared(
                $conn,
                'SELECT COUNT(*) AS total FROM provider_educations WHERE provider_user_id = ?',
                'i',
                [$providerId]
            );
            $counts['educations'] = (int)($row['total'] ?? 0);
        }

        if (ems_provider_table_exists($conn, 'provider_experiences')) {
            $row = ems_provider_fetch_row_prepared(
                $conn,
                'SELECT COUNT(*) AS total FROM provider_experiences WHERE provider_user_id = ?',
                'i',
                [$providerId]
            );
            $counts['experiences'] = (int)($row['total'] ?? 0);
        }

        if (ems_provider_table_exists($conn, 'provider_certifications')) {
            $row = ems_provider_fetch_row_prepared(
                $conn,
                'SELECT COUNT(*) AS total FROM provider_certifications WHERE provider_user_id = ?',
                'i',
                [$providerId]
            );
            $counts['certifications'] = (int)($row['total'] ?? 0);
        }

        return $counts;
    }
}

if (!function_exists('ems_provider_calculate_profile_completion')) {
    function ems_provider_calculate_profile_completion(array $portalUser, array $counts)
    {
        $score = 0.0;

        $weights = [
            'full_name' => 10,
            'email' => 8,
            'mobile_number' => 10,
            'professional_title' => 12,
            'skill_category' => 10,
            'teaching_experience' => 10,
            'short_bio' => 15,
            'profile_photo_url' => 5,
        ];

        foreach ($weights as $field => $weight) {
            $value = trim((string)($portalUser[$field] ?? ''));
            if ($value !== '') {
                $score += (float)$weight;
            }
        }

        if ((int)($counts['educations'] ?? 0) > 0) {
            $score += 8;
        }
        if ((int)($counts['experiences'] ?? 0) > 0) {
            $score += 8;
        }
        if ((int)($counts['certifications'] ?? 0) > 0) {
            $score += 12;
        }

        $score = max(0.0, min(100.0, $score));
        return round($score, 2);
    }
}

if (!function_exists('ems_provider_fetch_educations')) {
    function ems_provider_fetch_educations($conn, $providerUserId, $limit = 100)
    {
        $providerId = (int)$providerUserId;
        if ($providerId <= 0 || !ems_provider_table_exists($conn, 'provider_educations')) {
            return [];
        }

        $safeLimit = max(1, min(300, (int)$limit));
        $rows = ems_provider_fetch_rows_prepared(
            $conn,
            "SELECT id, degree_title, institution_name, field_of_study, start_year, end_year, is_current, description, created_at
             FROM provider_educations
             WHERE provider_user_id = ?
             ORDER BY COALESCE(end_year, start_year, 0) DESC, id DESC
             LIMIT {$safeLimit}",
            'i',
            [$providerId]
        );

        foreach ($rows as &$row) {
            $row['is_current'] = (int)($row['is_current'] ?? 0) === 1;
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_provider_fetch_experiences')) {
    function ems_provider_fetch_experiences($conn, $providerUserId, $limit = 100)
    {
        $providerId = (int)$providerUserId;
        if ($providerId <= 0 || !ems_provider_table_exists($conn, 'provider_experiences')) {
            return [];
        }

        $safeLimit = max(1, min(300, (int)$limit));
        $rows = ems_provider_fetch_rows_prepared(
            $conn,
            "SELECT id, job_title, company_name, employment_type, start_date, end_date, is_current, description, created_at
             FROM provider_experiences
             WHERE provider_user_id = ?
             ORDER BY COALESCE(end_date, start_date, '0000-00-00') DESC, id DESC
             LIMIT {$safeLimit}",
            'i',
            [$providerId]
        );

        foreach ($rows as &$row) {
            $row['is_current'] = (int)($row['is_current'] ?? 0) === 1;
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_provider_fetch_certifications')) {
    function ems_provider_fetch_certifications($conn, $providerUserId, $limit = 100)
    {
        $providerId = (int)$providerUserId;
        if ($providerId <= 0 || !ems_provider_table_exists($conn, 'provider_certifications')) {
            return [];
        }

        $safeLimit = max(1, min(300, (int)$limit));
        return ems_provider_fetch_rows_prepared(
            $conn,
            "SELECT id, certificate_name, issued_by, issue_date, expiry_date, credential_id, credential_url, created_at
             FROM provider_certifications
             WHERE provider_user_id = ?
             ORDER BY COALESCE(issue_date, '0000-00-00') DESC, id DESC
             LIMIT {$safeLimit}",
            'i',
            [$providerId]
        );
    }
}

if (!function_exists('ems_provider_fetch_approval_request')) {
    function ems_provider_fetch_approval_request($conn, $providerUserId, $userStatus = 'active')
    {
        $providerId = (int)$providerUserId;
        $fallbackStatus = strtolower(trim((string)$userStatus)) === 'active' ? 'approved' : 'draft';
        $default = [
            'id' => 0,
            'provider_user_id' => $providerId,
            'request_status' => $fallbackStatus,
            'submitted_at' => null,
            'reviewed_at' => null,
            'reviewed_by_user_id' => 0,
            'completion_score' => 0,
            'review_note' => '',
        ];

        if ($providerId <= 0 || !ems_provider_table_exists($conn, 'provider_approval_requests')) {
            return $default;
        }

        $row = ems_provider_fetch_row_prepared(
            $conn,
            'SELECT id, provider_user_id, request_status, submitted_at, reviewed_at, reviewed_by_user_id, completion_score, review_note
             FROM provider_approval_requests
             WHERE provider_user_id = ?
             LIMIT 1',
            'i',
            [$providerId]
        );

        if (!$row) {
            return $default;
        }

        $default = array_merge($default, $row);
        $default['request_status'] = strtolower(trim((string)($default['request_status'] ?? 'draft')));
        if (!in_array($default['request_status'], ['draft', 'pending', 'approved', 'rejected'], true)) {
            $default['request_status'] = $fallbackStatus;
        }

        return $default;
    }
}

if (!function_exists('ems_provider_course_creation_access')) {
    function ems_provider_course_creation_access($conn, $providerUserId, $userStatus = 'active')
    {
        $approval = ems_provider_fetch_approval_request($conn, $providerUserId, $userStatus);
        $status = strtolower(trim((string)($approval['request_status'] ?? 'draft')));
        if (!in_array($status, ['draft', 'pending', 'approved', 'rejected'], true)) {
            $status = 'draft';
        }

        $allowed = $status === 'approved';
        $message = 'Your account must be approved by admin before you can create courses.';
        if ($status === 'approved') {
            $message = 'Your account is approved. You can create and publish courses.';
        } elseif ($status === 'pending') {
            $message = 'Your account is pending admin approval. Course creation is disabled until approval.';
        } elseif ($status === 'rejected') {
            $message = 'Your account was rejected by admin. Update your profile and re-apply for approval to create courses.';
        }

        return [
            'allowed' => $allowed,
            'status' => $status,
            'message' => $message,
            'approval' => $approval,
        ];
    }
}

if (!function_exists('ems_provider_fetch_profile_summary')) {
    function ems_provider_fetch_profile_summary($conn, $providerUserId, array $portalUser = [])
    {
        $providerId = (int)$providerUserId;
        $metrics = function_exists('ems_provider_fetch_dashboard_metrics') && ems_provider_tables_ready($conn)
            ? ems_provider_fetch_dashboard_metrics($conn, $providerId)
            : [
                'total_courses' => 0,
                'total_students' => 0,
                'avg_rating' => 0,
                'total_revenue' => 0,
            ];

        $counts = ems_provider_fetch_profile_counts($conn, $providerId);
        $approval = ems_provider_fetch_approval_request($conn, $providerId, (string)($portalUser['status'] ?? 'active'));
        $completion = ems_provider_calculate_profile_completion($portalUser, $counts);

        return [
            'metrics' => $metrics,
            'counts' => $counts,
            'approval' => $approval,
            'completion_percent' => $completion,
            'educations' => ems_provider_fetch_educations($conn, $providerId, 6),
            'experiences' => ems_provider_fetch_experiences($conn, $providerId, 6),
            'certifications' => ems_provider_fetch_certifications($conn, $providerId, 6),
        ];
    }
}

if (!function_exists('ems_provider_update_basic_profile')) {
    function ems_provider_update_basic_profile($conn, $providerUserId, array $payload)
    {
        $providerId = (int)$providerUserId;
        if ($providerId <= 0) {
            return ['ok' => false, 'message' => 'Invalid provider account.'];
        }

        if (!ems_provider_table_exists($conn, 'provider_profiles')) {
            return ['ok' => false, 'message' => 'Provider profile table is unavailable.'];
        }

        $fullName = trim((string)($payload['full_name'] ?? ''));
        $professionalTitle = trim((string)($payload['professional_title'] ?? ''));
        $mobile = trim((string)($payload['mobile_number'] ?? ''));
        $skillCategory = trim((string)($payload['skill_category'] ?? ''));
        $teachingExperience = trim((string)($payload['teaching_experience'] ?? ''));
        $shortBio = trim((string)($payload['short_bio'] ?? ''));

        if ($fullName === '' || $professionalTitle === '' || $skillCategory === '') {
            return ['ok' => false, 'message' => 'Full name, professional title, and skill category are required.'];
        }

        $conn->begin_transaction();
        try {
            $profileSeed = ems_provider_exec_prepared_row(
                $conn,
                'INSERT INTO provider_profiles (user_id, professional_title, mobile_number, skill_category, teaching_experience, short_bio, accepted_terms)
                 VALUES (?, "", "", "", "", "", 1)
                 ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)',
                'i',
                [$providerId]
            );
            if (!$profileSeed['ok']) {
                throw new RuntimeException('Unable to initialize provider profile row.');
            }

            $userUpdate = ems_provider_exec_prepared_row(
                $conn,
                'UPDATE users SET full_name = ? WHERE id = ? LIMIT 1',
                'si',
                [$fullName, $providerId]
            );
            if (!$userUpdate['ok']) {
                throw new RuntimeException('Unable to update user profile.');
            }

            $profileUpdate = ems_provider_exec_prepared_row(
                $conn,
                'UPDATE provider_profiles
                 SET professional_title = ?, mobile_number = ?, skill_category = ?, teaching_experience = ?, short_bio = ?, updated_at = NOW()
                 WHERE user_id = ?
                 LIMIT 1',
                'sssssi',
                [$professionalTitle, $mobile, $skillCategory, $teachingExperience, $shortBio, $providerId]
            );
            if (!$profileUpdate['ok']) {
                throw new RuntimeException('Unable to update provider details.');
            }

            $conn->commit();
            return ['ok' => true, 'message' => 'Basic profile updated successfully.'];
        } catch (Throwable $throwable) {
            $conn->rollback();
            return ['ok' => false, 'message' => $throwable->getMessage()];
        }
    }
}

if (!function_exists('ems_provider_add_education')) {
    function ems_provider_add_education($conn, $providerUserId, array $payload)
    {
        $providerId = (int)$providerUserId;
        if ($providerId <= 0 || !ems_provider_table_exists($conn, 'provider_educations')) {
            return ['ok' => false, 'message' => 'Education table is unavailable.'];
        }

        $degree = trim((string)($payload['degree_title'] ?? ''));
        $institution = trim((string)($payload['institution_name'] ?? ''));
        $field = trim((string)($payload['field_of_study'] ?? ''));
        $startYear = (int)($payload['start_year'] ?? 0);
        $endYear = (int)($payload['end_year'] ?? 0);
        $isCurrent = !empty($payload['is_current']) ? 1 : 0;
        $description = trim((string)($payload['description'] ?? ''));

        if ($degree === '' || $institution === '') {
            return ['ok' => false, 'message' => 'Degree and institution are required.'];
        }

        if ($startYear > 0 && $endYear > 0 && $endYear < $startYear) {
            return ['ok' => false, 'message' => 'End year must be greater than or equal to start year.'];
        }

        if ($isCurrent === 1) {
            $endYear = 0;
        }

        $result = ems_provider_exec_prepared_row(
            $conn,
            'INSERT INTO provider_educations (provider_user_id, degree_title, institution_name, field_of_study, start_year, end_year, is_current, description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            'isssiiis',
            [
                $providerId,
                $degree,
                $institution,
                $field !== '' ? $field : null,
                $startYear > 0 ? $startYear : null,
                $endYear > 0 ? $endYear : null,
                $isCurrent,
                $description !== '' ? $description : null,
            ]
        );

        if (!$result['ok']) {
            return ['ok' => false, 'message' => 'Unable to add education record.'];
        }

        return ['ok' => true, 'message' => 'Education added successfully.'];
    }
}

if (!function_exists('ems_provider_add_experience')) {
    function ems_provider_add_experience($conn, $providerUserId, array $payload)
    {
        $providerId = (int)$providerUserId;
        if ($providerId <= 0 || !ems_provider_table_exists($conn, 'provider_experiences')) {
            return ['ok' => false, 'message' => 'Experience table is unavailable.'];
        }

        $jobTitle = trim((string)($payload['job_title'] ?? ''));
        $company = trim((string)($payload['company_name'] ?? ''));
        $employmentType = trim((string)($payload['employment_type'] ?? ''));
        $startDate = trim((string)($payload['start_date'] ?? ''));
        $endDate = trim((string)($payload['end_date'] ?? ''));
        $isCurrent = !empty($payload['is_current']) ? 1 : 0;
        $description = trim((string)($payload['description'] ?? ''));

        if ($jobTitle === '' || $company === '') {
            return ['ok' => false, 'message' => 'Job title and company are required.'];
        }

        if ($isCurrent === 1) {
            $endDate = '';
        }

        if ($startDate !== '' && $endDate !== '' && strtotime($endDate) < strtotime($startDate)) {
            return ['ok' => false, 'message' => 'End date must be after start date.'];
        }

        $result = ems_provider_exec_prepared_row(
            $conn,
            'INSERT INTO provider_experiences (provider_user_id, job_title, company_name, employment_type, start_date, end_date, is_current, description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            'isssssis',
            [
                $providerId,
                $jobTitle,
                $company,
                $employmentType !== '' ? $employmentType : null,
                $startDate !== '' ? $startDate : null,
                $endDate !== '' ? $endDate : null,
                $isCurrent,
                $description !== '' ? $description : null,
            ]
        );

        if (!$result['ok']) {
            return ['ok' => false, 'message' => 'Unable to add experience record.'];
        }

        return ['ok' => true, 'message' => 'Experience added successfully.'];
    }
}

if (!function_exists('ems_provider_add_certification')) {
    function ems_provider_add_certification($conn, $providerUserId, array $payload)
    {
        $providerId = (int)$providerUserId;
        if ($providerId <= 0 || !ems_provider_table_exists($conn, 'provider_certifications')) {
            return ['ok' => false, 'message' => 'Certification table is unavailable.'];
        }

        $name = trim((string)($payload['certificate_name'] ?? ''));
        $issuer = trim((string)($payload['issued_by'] ?? ''));
        $issueDate = trim((string)($payload['issue_date'] ?? ''));
        $expiryDate = trim((string)($payload['expiry_date'] ?? ''));
        $credentialId = trim((string)($payload['credential_id'] ?? ''));
        $credentialUrl = trim((string)($payload['credential_url'] ?? ''));

        if ($name === '' || $issuer === '') {
            return ['ok' => false, 'message' => 'Certificate name and issuing organization are required.'];
        }

        if ($issueDate !== '' && $expiryDate !== '' && strtotime($expiryDate) < strtotime($issueDate)) {
            return ['ok' => false, 'message' => 'Expiry date must be after issue date.'];
        }

        $result = ems_provider_exec_prepared_row(
            $conn,
            'INSERT INTO provider_certifications (provider_user_id, certificate_name, issued_by, issue_date, expiry_date, credential_id, credential_url)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            'issssss',
            [
                $providerId,
                $name,
                $issuer,
                $issueDate !== '' ? $issueDate : null,
                $expiryDate !== '' ? $expiryDate : null,
                $credentialId !== '' ? $credentialId : null,
                $credentialUrl !== '' ? $credentialUrl : null,
            ]
        );

        if (!$result['ok']) {
            return ['ok' => false, 'message' => 'Unable to add certification record.'];
        }

        return ['ok' => true, 'message' => 'Certification added successfully.'];
    }
}

if (!function_exists('ems_provider_delete_education')) {
    function ems_provider_delete_education($conn, $providerUserId, $educationId)
    {
        $result = ems_provider_exec_prepared_row(
            $conn,
            'DELETE FROM provider_educations WHERE id = ? AND provider_user_id = ? LIMIT 1',
            'ii',
            [(int)$educationId, (int)$providerUserId]
        );

        return ['ok' => $result['ok'], 'removed' => (int)($result['affected'] ?? 0) > 0];
    }
}

if (!function_exists('ems_provider_delete_experience')) {
    function ems_provider_delete_experience($conn, $providerUserId, $experienceId)
    {
        $result = ems_provider_exec_prepared_row(
            $conn,
            'DELETE FROM provider_experiences WHERE id = ? AND provider_user_id = ? LIMIT 1',
            'ii',
            [(int)$experienceId, (int)$providerUserId]
        );

        return ['ok' => $result['ok'], 'removed' => (int)($result['affected'] ?? 0) > 0];
    }
}

if (!function_exists('ems_provider_delete_certification')) {
    function ems_provider_delete_certification($conn, $providerUserId, $certificationId)
    {
        $result = ems_provider_exec_prepared_row(
            $conn,
            'DELETE FROM provider_certifications WHERE id = ? AND provider_user_id = ? LIMIT 1',
            'ii',
            [(int)$certificationId, (int)$providerUserId]
        );

        return ['ok' => $result['ok'], 'removed' => (int)($result['affected'] ?? 0) > 0];
    }
}

if (!function_exists('ems_provider_submit_approval_request')) {
    function ems_provider_submit_approval_request($conn, $providerUserId, array $portalUser = [])
    {
        $providerId = (int)$providerUserId;
        if ($providerId <= 0 || !ems_provider_table_exists($conn, 'provider_approval_requests')) {
            return ['ok' => false, 'message' => 'Approval request table is unavailable.'];
        }

        $counts = ems_provider_fetch_profile_counts($conn, $providerId);
        if ((int)$counts['educations'] <= 0 || (int)$counts['experiences'] <= 0 || (int)$counts['certifications'] <= 0) {
            return ['ok' => false, 'message' => 'Please add at least one education, one experience, and one certification before applying.'];
        }

        $score = ems_provider_calculate_profile_completion($portalUser, $counts);
        $snapshot = [
            'provider_user_id' => $providerId,
            'full_name' => trim((string)($portalUser['full_name'] ?? '')),
            'professional_title' => trim((string)($portalUser['professional_title'] ?? '')),
            'skill_category' => trim((string)($portalUser['skill_category'] ?? '')),
            'teaching_experience' => trim((string)($portalUser['teaching_experience'] ?? '')),
            'short_bio' => trim((string)($portalUser['short_bio'] ?? '')),
            'counts' => $counts,
            'completion_score' => $score,
        ];

        $result = ems_provider_exec_prepared_row(
            $conn,
            'INSERT INTO provider_approval_requests (provider_user_id, request_status, submitted_at, completion_score, snapshot_json, reviewed_at, reviewed_by_user_id, review_note)
             VALUES (?, "pending", NOW(), ?, ?, NULL, NULL, NULL)
             ON DUPLICATE KEY UPDATE
                 request_status = "pending",
                 submitted_at = NOW(),
                 completion_score = VALUES(completion_score),
                 snapshot_json = VALUES(snapshot_json),
                 reviewed_at = NULL,
                 reviewed_by_user_id = NULL,
                 review_note = NULL,
                 updated_at = NOW()',
            'ids',
            [$providerId, $score, json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]
        );

        if (!$result['ok']) {
            return ['ok' => false, 'message' => 'Unable to submit approval request.'];
        }

        return ['ok' => true, 'message' => 'Profile submitted for admin approval successfully.'];
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
    function ems_admin_provider_fetch_management_rows($conn, $filter = 'all', $limit = 250)
    {
        if (!ems_provider_table_exists($conn, 'users') || !ems_provider_table_exists($conn, 'provider_profiles')) {
            return [];
        }

        $safeLimit = max(1, min(500, (int)$limit));
        $providers = ems_provider_fetch_rows_prepared(
            $conn,
            "SELECT u.id AS provider_user_id, u.full_name, u.email, u.created_at,
                    pp.skill_category, pp.teaching_experience, pp.professional_title
             FROM users u
             LEFT JOIN provider_profiles pp ON pp.user_id = u.id
             WHERE u.role = 'provider'
             ORDER BY u.created_at DESC
             LIMIT {$safeLimit}"
        );

        $approvalMap = [];
        if (ems_provider_table_exists($conn, 'provider_approval_requests')) {
            $approvalRows = ems_provider_fetch_rows_prepared(
                $conn,
                'SELECT id, provider_user_id, request_status, submitted_at, reviewed_at, review_note, completion_score
                 FROM provider_approval_requests'
            );
            foreach ($approvalRows as $approvalRow) {
                $approvalMap[(int)($approvalRow['provider_user_id'] ?? 0)] = $approvalRow;
            }
        }

        $educationCount = [];
        if (ems_provider_table_exists($conn, 'provider_educations')) {
            $rows = ems_provider_fetch_rows_prepared($conn, 'SELECT provider_user_id, COUNT(*) AS total FROM provider_educations GROUP BY provider_user_id');
            foreach ($rows as $row) {
                $educationCount[(int)($row['provider_user_id'] ?? 0)] = (int)($row['total'] ?? 0);
            }
        }

        $experienceCount = [];
        if (ems_provider_table_exists($conn, 'provider_experiences')) {
            $rows = ems_provider_fetch_rows_prepared($conn, 'SELECT provider_user_id, COUNT(*) AS total FROM provider_experiences GROUP BY provider_user_id');
            foreach ($rows as $row) {
                $experienceCount[(int)($row['provider_user_id'] ?? 0)] = (int)($row['total'] ?? 0);
            }
        }

        $certificationCount = [];
        if (ems_provider_table_exists($conn, 'provider_certifications')) {
            $rows = ems_provider_fetch_rows_prepared($conn, 'SELECT provider_user_id, COUNT(*) AS total FROM provider_certifications GROUP BY provider_user_id');
            foreach ($rows as $row) {
                $certificationCount[(int)($row['provider_user_id'] ?? 0)] = (int)($row['total'] ?? 0);
            }
        }

        $safeFilter = strtolower(trim((string)$filter));
        if (!in_array($safeFilter, ['all', 'approved', 'rejected', 'applications'], true)) {
            $safeFilter = 'all';
        }

        $result = [];
        foreach ($providers as $providerRow) {
            $providerId = (int)($providerRow['provider_user_id'] ?? 0);
            if ($providerId <= 0) {
                continue;
            }

            $approval = $approvalMap[$providerId] ?? null;
            $status = $approval ? strtolower(trim((string)($approval['request_status'] ?? 'draft'))) : 'approved';
            if (!in_array($status, ['draft', 'pending', 'approved', 'rejected'], true)) {
                $status = 'draft';
            }

            if ($safeFilter === 'applications' && $status !== 'pending') {
                continue;
            }
            if ($safeFilter === 'rejected' && $status !== 'rejected') {
                continue;
            }
            if ($safeFilter === 'approved' && $status !== 'approved') {
                continue;
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
                'provider_name' => (string)($providerRow['full_name'] ?? 'Provider'),
                'email' => (string)($providerRow['email'] ?? ''),
                'specialization' => (string)($providerRow['skill_category'] ?? 'General'),
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

        if (!ems_provider_table_exists($conn, 'provider_approval_requests')) {
            return ['ok' => false, 'message' => 'Approval request table is unavailable.'];
        }

        $row = ems_provider_fetch_row_prepared(
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
            $update = ems_provider_exec_prepared_row(
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

            if (ems_provider_table_exists($conn, 'users')) {
                ems_provider_exec_prepared_row(
                    $conn,
                    'UPDATE users SET status = "active" WHERE id = ? AND role = "provider" LIMIT 1',
                    'i',
                    [(int)($row['provider_user_id'] ?? 0)]
                );
            }

            $conn->commit();
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

