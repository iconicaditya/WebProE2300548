<?php

if (!function_exists('ems_learner_table_exists')) {
    function ems_learner_table_exists($conn, $tableName)
    {
        static $cache = [];

        if (!is_object($conn)) {
            return false;
        }

        $name = trim((string)$tableName);
        if ($name === '') {
            return false;
        }

        $cacheKey = spl_object_hash($conn) . ':' . strtolower($name);
        if (array_key_exists($cacheKey, $cache)) {
            return $cache[$cacheKey];
        }

        $stmt = $conn->prepare('SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1');
        if (!$stmt) {
            $cache[$cacheKey] = false;
            return false;
        }

        $stmt->bind_param('s', $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result && $result->num_rows > 0;
        $stmt->close();

        $cache[$cacheKey] = $exists;
        return $exists;
    }
}

if (!function_exists('ems_learner_core_tables_ready')) {
    function ems_learner_core_tables_ready($conn)
    {
        $required = ['users', 'courses', 'course_sections', 'course_lessons', 'enrollments', 'reviews'];
        foreach ($required as $table) {
            if (!ems_learner_table_exists($conn, $table)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('ems_learner_domain_tables_ready')) {
    function ems_learner_domain_tables_ready($conn)
    {
        $required = [
            'learner_wishlist',
            'learner_cart_items',
            'learner_orders',
            'learner_order_items',
            'learner_payments',
            'learner_lesson_progress',
            'learner_quiz_attempts',
            'learner_certificates',
            'learner_messages',
            'learner_notifications',
            'learner_settings',
            'learner_security_logs',
            'learner_trusted_devices',
        ];

        foreach ($required as $table) {
            if (!ems_learner_table_exists($conn, $table)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('ems_learner_tables_ready')) {
    function ems_learner_tables_ready($conn)
    {
        return ems_learner_core_tables_ready($conn) && ems_learner_domain_tables_ready($conn);
    }
}

if (!function_exists('ems_learner_bind_and_execute')) {
    function ems_learner_bind_and_execute($stmt, $types, array &$params)
    {
        $bindTypes = (string)$types;
        if ($bindTypes !== '' && !empty($params)) {
            $bindArgs = [];
            $bindArgs[] = $bindTypes;
            foreach ($params as $index => $value) {
                $bindArgs[] = &$params[$index];
            }
            if (!call_user_func_array([$stmt, 'bind_param'], $bindArgs)) {
                return false;
            }
        }

        return $stmt->execute();
    }
}

if (!function_exists('ems_learner_fetch_rows')) {
    function ems_learner_fetch_rows($conn, $sql, $types = '', array $params = [])
    {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if (!ems_learner_bind_and_execute($stmt, $types, $params)) {
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return is_array($rows) ? $rows : [];
    }
}

if (!function_exists('ems_learner_fetch_row')) {
    function ems_learner_fetch_row($conn, $sql, $types = '', array $params = [])
    {
        $rows = ems_learner_fetch_rows($conn, $sql, $types, $params);
        return !empty($rows) ? $rows[0] : null;
    }
}

if (!function_exists('ems_learner_exec')) {
    function ems_learner_exec($conn, $sql, $types = '', array $params = [])
    {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return ['ok' => false, 'affected' => 0, 'insert_id' => 0];
        }

        if (!ems_learner_bind_and_execute($stmt, $types, $params)) {
            $stmt->close();
            return ['ok' => false, 'affected' => 0, 'insert_id' => 0];
        }

        $payload = [
            'ok' => true,
            'affected' => (int)$stmt->affected_rows,
            'insert_id' => (int)$stmt->insert_id,
        ];

        $stmt->close();
        return $payload;
    }
}

if (!function_exists('ems_learner_currency_format')) {
    function ems_learner_currency_format($amount, $currencyCode = 'USD')
    {
        $currency = strtoupper(trim((string)$currencyCode));
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

if (!function_exists('ems_learner_relative_time')) {
    function ems_learner_relative_time($dateTime)
    {
        $ts = strtotime((string)$dateTime);
        if (!$ts) {
            return 'just now';
        }

        $diff = time() - $ts;
        if ($diff < 60) {
            return max(1, $diff) . ' sec ago';
        }
        if ($diff < 3600) {
            return floor($diff / 60) . ' min ago';
        }
        if ($diff < 86400) {
            return floor($diff / 3600) . ' hour' . (floor($diff / 3600) > 1 ? 's' : '') . ' ago';
        }
        if ($diff < 604800) {
            return floor($diff / 86400) . ' day' . (floor($diff / 86400) > 1 ? 's' : '') . ' ago';
        }

        return date('d M Y', $ts);
    }
}

if (!function_exists('ems_learner_seconds_to_duration')) {
    function ems_learner_seconds_to_duration($seconds)
    {
        $value = max(0, (int)$seconds);
        if ($value <= 0) {
            return '';
        }

        if ($value >= 3600) {
            $h = floor($value / 3600);
            $m = floor(($value % 3600) / 60);
            return $h . 'h ' . ($m > 0 ? $m . 'm' : '');
        }

        if ($value >= 60) {
            return floor($value / 60) . 'm ' . str_pad((string)($value % 60), 2, '0', STR_PAD_LEFT) . 's';
        }

        return $value . 's';
    }
}

if (!function_exists('ems_learner_media_url')) {
    function ems_learner_media_url($path, $fallback = '')
    {
        $value = trim((string)$path);
        if ($value === '') {
            return $fallback;
        }

        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        return BASE_URL . ltrim($value, '/');
    }
}

if (!function_exists('ems_learner_normalize_theme')) {
    function ems_learner_normalize_theme($theme)
    {
        $value = strtolower(trim((string)$theme));
        if (!in_array($value, ['light', 'dark', 'system'], true)) {
            return 'light';
        }
        return $value;
    }
}

if (!function_exists('ems_learner_ensure_settings_row')) {
    function ems_learner_ensure_settings_row($conn, $learnerUserId)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_settings')) {
            return;
        }

        ems_learner_exec(
            $conn,
            'INSERT INTO learner_settings (learner_user_id) VALUES (?) ON DUPLICATE KEY UPDATE learner_user_id = VALUES(learner_user_id)',
            'i',
            [$userId]
        );
    }
}

if (!function_exists('ems_learner_fetch_settings')) {
    function ems_learner_fetch_settings($conn, $learnerUserId)
    {
        $defaults = [
            'language_code' => 'en',
            'timezone' => 'Asia/Kolkata',
            'notification_email_enabled' => 1,
            'theme_preference' => 'light',
            'two_factor_enabled' => 0,
        ];

        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_settings')) {
            return $defaults;
        }

        ems_learner_ensure_settings_row($conn, $userId);

        $row = ems_learner_fetch_row(
            $conn,
            'SELECT language_code, timezone, notification_email_enabled, theme_preference, two_factor_enabled FROM learner_settings WHERE learner_user_id = ? LIMIT 1',
            'i',
            [$userId]
        );

        if (!$row) {
            return $defaults;
        }

        $defaults['language_code'] = trim((string)($row['language_code'] ?? 'en')) ?: 'en';
        $defaults['timezone'] = trim((string)($row['timezone'] ?? 'Asia/Kolkata')) ?: 'Asia/Kolkata';
        $defaults['notification_email_enabled'] = (int)($row['notification_email_enabled'] ?? 1) === 1 ? 1 : 0;
        $defaults['theme_preference'] = ems_learner_normalize_theme($row['theme_preference'] ?? 'light');
        $defaults['two_factor_enabled'] = (int)($row['two_factor_enabled'] ?? 0) === 1 ? 1 : 0;

        return $defaults;
    }
}

if (!function_exists('ems_learner_count_cart_items')) {
    function ems_learner_count_cart_items($conn, $learnerUserId)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_cart_items')) {
            return 0;
        }

        $row = ems_learner_fetch_row(
            $conn,
            'SELECT COUNT(*) AS total FROM learner_cart_items WHERE learner_user_id = ?',
            'i',
            [$userId]
        );

        return (int)($row['total'] ?? 0);
    }
}

if (!function_exists('ems_learner_count_wishlist_items')) {
    function ems_learner_count_wishlist_items($conn, $learnerUserId)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_wishlist')) {
            return 0;
        }

        $row = ems_learner_fetch_row(
            $conn,
            'SELECT COUNT(*) AS total FROM learner_wishlist WHERE learner_user_id = ?',
            'i',
            [$userId]
        );

        return (int)($row['total'] ?? 0);
    }
}

if (!function_exists('ems_learner_fetch_recent_notifications')) {
    function ems_learner_fetch_recent_notifications($conn, $learnerUserId, $limit = 8)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_notifications')) {
            return [];
        }

        $safeLimit = max(1, min(30, (int)$limit));
        $sql = "
            SELECT id, notification_type, title, message_text, related_url, is_read, created_at
            FROM learner_notifications
            WHERE learner_user_id = ?
            ORDER BY created_at DESC
            LIMIT {$safeLimit}
        ";

        $rows = ems_learner_fetch_rows($conn, $sql, 'i', [$userId]);
        foreach ($rows as &$row) {
            $row['is_read'] = (int)($row['is_read'] ?? 0) === 1;
            $row['time_ago'] = ems_learner_relative_time($row['created_at'] ?? null);
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_learner_fetch_recent_messages')) {
    function ems_learner_fetch_recent_messages($conn, $learnerUserId, $limit = 8)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_messages')) {
            return [];
        }

        $safeLimit = max(1, min(30, (int)$limit));
        $sql = "
            SELECT
                m.id,
                m.subject,
                m.message_text,
                m.is_read,
                m.sent_at,
                COALESCE(p.full_name, 'Instructor') AS provider_name,
                c.title AS course_title
            FROM learner_messages m
            LEFT JOIN users p ON p.id = m.provider_user_id
            LEFT JOIN courses c ON c.id = m.course_id
            WHERE m.learner_user_id = ?
            ORDER BY m.sent_at DESC
            LIMIT {$safeLimit}
        ";

        $rows = ems_learner_fetch_rows($conn, $sql, 'i', [$userId]);
        foreach ($rows as &$row) {
            $row['is_read'] = (int)($row['is_read'] ?? 0) === 1;
            $row['time_ago'] = ems_learner_relative_time($row['sent_at'] ?? null);
            $row['provider_initials'] = ems_user_initials($row['provider_name'] ?? 'Instructor');
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_learner_count_unread_notifications')) {
    function ems_learner_count_unread_notifications($conn, $learnerUserId)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_notifications')) {
            return 0;
        }

        $row = ems_learner_fetch_row(
            $conn,
            'SELECT COUNT(*) AS total FROM learner_notifications WHERE learner_user_id = ? AND is_read = 0',
            'i',
            [$userId]
        );

        return (int)($row['total'] ?? 0);
    }
}

if (!function_exists('ems_learner_count_unread_messages')) {
    function ems_learner_count_unread_messages($conn, $learnerUserId)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_messages')) {
            return 0;
        }

        $row = ems_learner_fetch_row(
            $conn,
            'SELECT COUNT(*) AS total FROM learner_messages WHERE learner_user_id = ? AND is_read = 0',
            'i',
            [$userId]
        );

        return (int)($row['total'] ?? 0);
    }
}

if (!function_exists('ems_learner_fetch_overview_metrics')) {
    function ems_learner_fetch_overview_metrics($conn, $learnerUserId)
    {
        $metrics = [
            'enrolled_courses' => 0,
            'completed_courses' => 0,
            'certificates' => 0,
            'quiz_average' => 0.0,
            'monthly_enrollments' => 0,
            'monthly_completed' => 0,
            'ready_certificates' => 0,
        ];

        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_core_tables_ready($conn)) {
            return $metrics;
        }

        $summary = ems_learner_fetch_row(
            $conn,
            "SELECT
                COUNT(*) AS enrolled_courses,
                SUM(CASE WHEN e.enrollment_status = 'completed' OR e.progress_percent >= 100 THEN 1 ELSE 0 END) AS completed_courses,
                SUM(CASE WHEN e.enrolled_at >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 ELSE 0 END) AS monthly_enrollments,
                SUM(CASE WHEN (e.enrollment_status = 'completed' OR e.progress_percent >= 100) AND COALESCE(e.completed_at, e.enrolled_at) >= DATE_FORMAT(NOW(), '%Y-%m-01') THEN 1 ELSE 0 END) AS monthly_completed
            FROM enrollments e
            INNER JOIN courses c ON c.id = e.course_id
            WHERE e.learner_user_id = ?
                AND e.enrollment_status IN ('active', 'completed')",
            'i',
            [$userId]
        );

        if ($summary) {
            $metrics['enrolled_courses'] = (int)($summary['enrolled_courses'] ?? 0);
            $metrics['completed_courses'] = (int)($summary['completed_courses'] ?? 0);
            $metrics['monthly_enrollments'] = (int)($summary['monthly_enrollments'] ?? 0);
            $metrics['monthly_completed'] = (int)($summary['monthly_completed'] ?? 0);
        }

        if (ems_learner_table_exists($conn, 'learner_certificates')) {
            $certRow = ems_learner_fetch_row(
                $conn,
                'SELECT COUNT(*) AS total FROM learner_certificates WHERE learner_user_id = ? AND status = "ready"',
                'i',
                [$userId]
            );
            $metrics['certificates'] = (int)($certRow['total'] ?? 0);
            $metrics['ready_certificates'] = $metrics['certificates'];
        } else {
            $metrics['certificates'] = $metrics['completed_courses'];
            $metrics['ready_certificates'] = $metrics['completed_courses'];
        }

        if (ems_learner_table_exists($conn, 'learner_quiz_attempts')) {
            $quizRow = ems_learner_fetch_row(
                $conn,
                'SELECT COALESCE(ROUND(AVG(score_percent), 2), 0) AS avg_score FROM learner_quiz_attempts WHERE learner_user_id = ?',
                'i',
                [$userId]
            );
            $metrics['quiz_average'] = (float)($quizRow['avg_score'] ?? 0);
        }

        return $metrics;
    }
}

if (!function_exists('ems_learner_fetch_next_lesson_label')) {
    function ems_learner_fetch_next_lesson_label($conn, $learnerUserId, $courseId)
    {
        $userId = (int)$learnerUserId;
        $courseIdValue = (int)$courseId;
        if ($userId <= 0 || $courseIdValue <= 0 || !ems_learner_core_tables_ready($conn)) {
            return 'Start learning';
        }

        $hasLessonProgress = ems_learner_table_exists($conn, 'learner_lesson_progress');
        if ($hasLessonProgress) {
            $next = ems_learner_fetch_row(
                $conn,
                "SELECT l.title
                FROM course_lessons l
                LEFT JOIN learner_lesson_progress lp
                    ON lp.lesson_id = l.id
                    AND lp.course_id = l.course_id
                    AND lp.learner_user_id = ?
                WHERE l.course_id = ?
                    AND (lp.id IS NULL OR lp.is_completed = 0)
                ORDER BY l.lesson_order ASC
                LIMIT 1",
                'ii',
                [$userId, $courseIdValue]
            );

            if (!empty($next['title'])) {
                return (string)$next['title'];
            }
        }

        $first = ems_learner_fetch_row(
            $conn,
            'SELECT title FROM course_lessons WHERE course_id = ? ORDER BY lesson_order ASC LIMIT 1',
            'i',
            [$courseIdValue]
        );

        if (!empty($first['title'])) {
            return (string)$first['title'];
        }

        return 'Course complete';
    }
}

if (!function_exists('ems_learner_fetch_continue_learning')) {
    function ems_learner_fetch_continue_learning($conn, $learnerUserId, $limit = 8)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_core_tables_ready($conn)) {
            return [];
        }

        $safeLimit = max(1, min(50, (int)$limit));
        $hasLessonProgress = ems_learner_table_exists($conn, 'learner_lesson_progress');

        $sql = "
            SELECT
                e.id AS enrollment_id,
                e.course_id,
                e.progress_percent,
                e.enrolled_at,
                e.completed_at,
                e.enrollment_status,
                c.title AS course_title,
                COALESCE(p.full_name, 'Instructor') AS provider_name,
                " . ($hasLessonProgress ? 'MAX(lp.last_activity_at)' : 'NULL') . " AS last_activity_at
            FROM enrollments e
            INNER JOIN courses c ON c.id = e.course_id
            LEFT JOIN users p ON p.id = c.provider_user_id
            " . ($hasLessonProgress ? 'LEFT JOIN learner_lesson_progress lp ON lp.course_id = e.course_id AND lp.learner_user_id = e.learner_user_id' : '') . "
            WHERE e.learner_user_id = ?
                AND e.enrollment_status IN ('active', 'completed')
            GROUP BY e.id
            ORDER BY COALESCE(" . ($hasLessonProgress ? 'MAX(lp.last_activity_at)' : 'NULL') . ", e.enrolled_at) DESC
            LIMIT {$safeLimit}
        ";

        $rows = ems_learner_fetch_rows($conn, $sql, 'i', [$userId]);

        foreach ($rows as &$row) {
            $courseId = (int)($row['course_id'] ?? 0);
            $lastActivity = $row['last_activity_at'] ?? $row['enrolled_at'] ?? null;
            $row['next_lesson'] = ems_learner_fetch_next_lesson_label($conn, $userId, $courseId);
            $row['last_activity_human'] = ems_learner_relative_time($lastActivity);
            $row['progress_percent'] = (float)($row['progress_percent'] ?? 0);
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_learner_fetch_enrolled_courses')) {
    function ems_learner_fetch_enrolled_courses($conn, $learnerUserId, $limit = 100)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_core_tables_ready($conn)) {
            return [];
        }

        $safeLimit = max(1, min(200, (int)$limit));

        $sql = "
            SELECT
                e.id AS enrollment_id,
                e.course_id,
                e.progress_percent,
                e.enrollment_status,
                e.enrolled_at,
                e.completed_at,
                c.title,
                c.short_description,
                c.description,
                c.thumbnail_path,
                c.language,
                c.level,
                c.duration_label,
                c.lesson_count_estimate,
                c.status,
                COALESCE(p.full_name, 'Instructor') AS provider_name,
                COALESCE(ROUND(AVG(CASE WHEN r.is_visible = 1 THEN r.rating END), 1), 0) AS avg_rating,
                COUNT(CASE WHEN r.is_visible = 1 THEN r.id END) AS review_count
            FROM enrollments e
            INNER JOIN courses c ON c.id = e.course_id
            LEFT JOIN users p ON p.id = c.provider_user_id
            LEFT JOIN reviews r ON r.course_id = c.id
            WHERE e.learner_user_id = ?
                AND e.enrollment_status IN ('active', 'completed')
            GROUP BY e.id
            ORDER BY COALESCE(e.completed_at, e.enrolled_at) DESC
            LIMIT {$safeLimit}
        ";

        $rows = ems_learner_fetch_rows($conn, $sql, 'i', [$userId]);
        foreach ($rows as &$row) {
            $row['progress_percent'] = (float)($row['progress_percent'] ?? 0);
            $row['avg_rating'] = (float)($row['avg_rating'] ?? 0);
            $row['review_count'] = (int)($row['review_count'] ?? 0);
            $row['thumbnail_url'] = ems_learner_media_url($row['thumbnail_path'] ?? '', BASE_URL . 'assets/images/cources/web-dev.jpg');
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_learner_parse_quiz_json')) {
    function ems_learner_parse_quiz_json($quizJson, $fallbackTitle = 'Quiz')
    {
        $quiz = [
            'title' => (string)$fallbackTitle,
            'qs' => [],
        ];

        if (!is_string($quizJson) || trim($quizJson) === '') {
            return $quiz;
        }

        $decoded = json_decode($quizJson, true);
        if (!is_array($decoded)) {
            return $quiz;
        }

        if (isset($decoded['title']) && trim((string)$decoded['title']) !== '') {
            $quiz['title'] = trim((string)$decoded['title']);
        }

        if (isset($decoded['qs']) && is_array($decoded['qs'])) {
            foreach ($decoded['qs'] as $questionRow) {
                $q = trim((string)($questionRow['q'] ?? ''));
                $opts = [];
                if (isset($questionRow['opts']) && is_array($questionRow['opts'])) {
                    foreach ($questionRow['opts'] as $opt) {
                        $text = trim((string)$opt);
                        if ($text !== '') {
                            $opts[] = $text;
                        }
                    }
                }
                if ($q !== '' && !empty($opts)) {
                    $quiz['qs'][] = ['q' => $q, 'opts' => $opts];
                }
            }
            return $quiz;
        }

        foreach ($decoded as $questionRow) {
            if (!is_array($questionRow)) {
                continue;
            }

            $q = trim((string)($questionRow['text'] ?? $questionRow['q'] ?? ''));
            $options = [];
            if (isset($questionRow['options']) && is_array($questionRow['options'])) {
                foreach ($questionRow['options'] as $opt) {
                    $text = trim((string)$opt);
                    if ($text !== '') {
                        $options[] = $text;
                    }
                }
            }

            if ($q !== '' && !empty($options)) {
                $quiz['qs'][] = ['q' => $q, 'opts' => $options];
            }
        }

        return $quiz;
    }
}

if (!function_exists('ems_learner_fetch_learning_portal_courses')) {
    function ems_learner_fetch_learning_portal_courses($conn, $learnerUserId, $limit = 20)
    {
        $courses = ems_learner_fetch_enrolled_courses($conn, $learnerUserId, $limit);
        if (empty($courses)) {
            return [];
        }

        $courseIds = [];
        foreach ($courses as $course) {
            $courseIds[] = (int)($course['course_id'] ?? 0);
        }
        $courseIds = array_values(array_unique(array_filter($courseIds)));
        if (empty($courseIds)) {
            return [];
        }

        $idListSql = implode(',', array_map('intval', $courseIds));

        $sectionsRows = ems_learner_fetch_rows(
            $conn,
            "SELECT id, course_id, section_order, title FROM course_sections WHERE course_id IN ({$idListSql}) ORDER BY course_id ASC, section_order ASC"
        );

        $lessonRows = ems_learner_fetch_rows(
            $conn,
            "SELECT id, course_id, section_id, lesson_order, lesson_type, title, video_path, pdf_path, quiz_json, duration_seconds
            FROM course_lessons
            WHERE course_id IN ({$idListSql})
            ORDER BY course_id ASC, lesson_order ASC"
        );

        $progressByLesson = [];
        if (ems_learner_table_exists($conn, 'learner_lesson_progress')) {
            $progressRows = ems_learner_fetch_rows(
                $conn,
                "SELECT course_id, lesson_id, progress_percent, is_completed
                FROM learner_lesson_progress
                WHERE learner_user_id = ?
                    AND course_id IN ({$idListSql})",
                'i',
                [(int)$learnerUserId]
            );

            foreach ($progressRows as $row) {
                $progressByLesson[(int)$row['lesson_id']] = [
                    'is_completed' => (int)($row['is_completed'] ?? 0) === 1,
                    'progress_percent' => (float)($row['progress_percent'] ?? 0),
                ];
            }
        }

        $sectionsByCourse = [];
        $sectionDuration = [];
        foreach ($sectionsRows as $section) {
            $courseId = (int)($section['course_id'] ?? 0);
            if (!isset($sectionsByCourse[$courseId])) {
                $sectionsByCourse[$courseId] = [];
            }
            $sectionsByCourse[$courseId][(int)$section['id']] = [
                'id' => (int)$section['id'],
                'title' => (string)($section['title'] ?? 'Section'),
                'order' => (int)($section['section_order'] ?? 1),
                'lessons' => [],
            ];
            $sectionDuration[(int)$section['id']] = 0;
        }

        $defaultVideo = BASE_URL . 'assets/courseportal/video/video.mp4';
        $defaultPdf = BASE_URL . 'assets/courseportal/pdf/Sample_website.pdf';

        foreach ($lessonRows as $lesson) {
            $courseId = (int)($lesson['course_id'] ?? 0);
            $sectionId = (int)($lesson['section_id'] ?? 0);

            if (!isset($sectionsByCourse[$courseId][$sectionId])) {
                if (!isset($sectionsByCourse[$courseId])) {
                    $sectionsByCourse[$courseId] = [];
                }

                $generatedId = -1 * ((int)$lesson['lesson_order'] + 1000);
                if (!isset($sectionsByCourse[$courseId][$generatedId])) {
                    $sectionsByCourse[$courseId][$generatedId] = [
                        'id' => $generatedId,
                        'title' => 'General',
                        'order' => 999,
                        'lessons' => [],
                    ];
                }
                $sectionId = $generatedId;
            }

            $lessonType = strtolower(trim((string)($lesson['lesson_type'] ?? 'video')));
            if (!in_array($lessonType, ['video', 'pdf', 'quiz', 'project'], true)) {
                $lessonType = 'video';
            }

            $durationSeconds = (int)($lesson['duration_seconds'] ?? 0);
            $durationLabel = ems_learner_seconds_to_duration($durationSeconds);
            if ($durationSeconds > 0) {
                $sectionDuration[$sectionId] = (int)($sectionDuration[$sectionId] ?? 0) + $durationSeconds;
            }

            $mapped = [
                'id' => (int)$lesson['id'],
                'name' => (string)($lesson['lesson_order'] . '. ' . ($lesson['title'] ?: ucfirst($lessonType) . ' lesson')),
                'type' => $lessonType,
                'dur' => $durationLabel,
            ];

            if ($lessonType === 'video') {
                $mapped['src'] = ems_learner_media_url($lesson['video_path'] ?? '', $defaultVideo);
            } elseif ($lessonType === 'pdf') {
                $mapped['src'] = ems_learner_media_url($lesson['pdf_path'] ?? '', $defaultPdf);
            } elseif ($lessonType === 'quiz') {
                $mapped['quiz'] = ems_learner_parse_quiz_json($lesson['quiz_json'] ?? null, 'Quiz: ' . ($lesson['title'] ?? 'Quiz'));
            }

            $lessonProgress = $progressByLesson[(int)$lesson['id']] ?? null;
            if ($lessonProgress) {
                $mapped['is_completed'] = $lessonProgress['is_completed'];
                $mapped['progress_percent'] = $lessonProgress['progress_percent'];
            }

            $sectionsByCourse[$courseId][$sectionId]['lessons'][] = $mapped;
        }

        $payload = [];
        foreach ($courses as $course) {
            $courseId = (int)($course['course_id'] ?? 0);
            $sectionRows = $sectionsByCourse[$courseId] ?? [];

            uasort($sectionRows, static function ($a, $b) {
                return (int)($a['order'] ?? 0) <=> (int)($b['order'] ?? 0);
            });

            $sections = [];
            foreach ($sectionRows as $sectionId => $section) {
                $duration = ems_learner_seconds_to_duration((int)($sectionDuration[(int)$sectionId] ?? 0));
                $sections[] = [
                    'title' => (string)($section['title'] ?? 'Section'),
                    'dur' => $duration !== '' ? $duration : 'Self paced',
                    'lessons' => $section['lessons'] ?? [],
                ];
            }

            $payload[] = [
                'id' => (int)$courseId,
                'title' => (string)($course['title'] ?? 'Untitled course'),
                'instructor' => (string)($course['provider_name'] ?? 'Instructor'),
                'category' => ucfirst(str_replace('_', ' ', (string)($course['level'] ?? 'General'))),
                'progress' => (float)($course['progress_percent'] ?? 0),
                'image' => (string)($course['thumbnail_url'] ?? BASE_URL . 'assets/images/cources/web-dev.jpg'),
                'desc' => trim((string)($course['short_description'] ?? '')) ?: 'Continue your learning journey.',
                'sections' => $sections,
            ];
        }

        return $payload;
    }
}

if (!function_exists('ems_learner_fetch_weekly_study_data')) {
    function ems_learner_fetch_weekly_study_data($conn, $learnerUserId, $days = 6)
    {
        $safeDays = max(3, min(14, (int)$days));
        $labels = [];
        $values = [];

        $today = strtotime(date('Y-m-d'));
        $byDate = [];
        for ($i = $safeDays - 1; $i >= 0; $i--) {
            $dateKey = date('Y-m-d', strtotime('-' . $i . ' days', $today));
            $labels[] = date('D', strtotime($dateKey));
            $byDate[$dateKey] = 0;
        }

        if ((int)$learnerUserId > 0 && ems_learner_table_exists($conn, 'learner_lesson_progress')) {
            $startDate = array_key_first($byDate);
            $rows = ems_learner_fetch_rows(
                $conn,
                'SELECT DATE(last_activity_at) AS activity_date, SUM(minutes_spent) AS total_minutes
                 FROM learner_lesson_progress
                 WHERE learner_user_id = ? AND last_activity_at >= ?
                 GROUP BY DATE(last_activity_at)',
                'is',
                [(int)$learnerUserId, $startDate . ' 00:00:00']
            );

            foreach ($rows as $row) {
                $key = (string)($row['activity_date'] ?? '');
                if (array_key_exists($key, $byDate)) {
                    $byDate[$key] = (int)($row['total_minutes'] ?? 0);
                }
            }
        }

        foreach ($byDate as $minutes) {
            $values[] = (int)$minutes;
        }

        return [
            'labels' => $labels,
            'minutes' => $values,
        ];
    }
}

if (!function_exists('ems_learner_fetch_completion_trend')) {
    function ems_learner_fetch_completion_trend($conn, $learnerUserId, $months = 8)
    {
        $safeMonths = max(3, min(12, (int)$months));
        $trend = [];

        for ($i = $safeMonths - 1; $i >= 0; $i--) {
            $key = date('Y-m', strtotime('-' . $i . ' months'));
            $trend[$key] = 0.0;
        }

        if ((int)$learnerUserId <= 0 || !ems_learner_table_exists($conn, 'enrollments')) {
            return $trend;
        }

        $startDate = date('Y-m-01', strtotime('-' . ($safeMonths - 1) . ' months'));
        $rows = ems_learner_fetch_rows(
            $conn,
            "SELECT DATE_FORMAT(COALESCE(completed_at, enrolled_at), '%Y-%m') AS ym,
                    COALESCE(AVG(progress_percent), 0) AS avg_progress
             FROM enrollments
             WHERE learner_user_id = ? AND COALESCE(completed_at, enrolled_at) >= ?
             GROUP BY ym
             ORDER BY ym ASC",
            'is',
            [(int)$learnerUserId, $startDate . ' 00:00:00']
        );

        foreach ($rows as $row) {
            $key = (string)($row['ym'] ?? '');
            if (array_key_exists($key, $trend)) {
                $trend[$key] = (float)($row['avg_progress'] ?? 0);
            }
        }

        return $trend;
    }
}

if (!function_exists('ems_learner_fetch_certificates')) {
    function ems_learner_fetch_certificates($conn, $learnerUserId, $limit = 100)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_core_tables_ready($conn)) {
            return [];
        }

        $safeLimit = max(1, min(200, (int)$limit));

        if (ems_learner_table_exists($conn, 'learner_certificates')) {
            $sql = "
                SELECT
                    lc.id,
                    lc.course_id,
                    lc.certificate_code,
                    lc.grade_label,
                    lc.file_path,
                    lc.status,
                    lc.issued_at,
                    c.title AS course_title
                FROM learner_certificates lc
                INNER JOIN courses c ON c.id = lc.course_id
                WHERE lc.learner_user_id = ?
                    AND lc.status = 'ready'
                ORDER BY lc.issued_at DESC
                LIMIT {$safeLimit}
            ";
            $rows = ems_learner_fetch_rows($conn, $sql, 'i', [$userId]);
            foreach ($rows as &$row) {
                $row['download_url'] = ems_learner_media_url($row['file_path'] ?? '', '');
                $row['grade_label'] = trim((string)($row['grade_label'] ?? 'A')) ?: 'A';
            }
            unset($row);
            return $rows;
        }

        $fallbackSql = "
            SELECT
                e.course_id,
                c.title AS course_title,
                COALESCE(e.completed_at, e.enrolled_at) AS issued_at,
                e.progress_percent
            FROM enrollments e
            INNER JOIN courses c ON c.id = e.course_id
            WHERE e.learner_user_id = ?
                AND (e.enrollment_status = 'completed' OR e.progress_percent >= 100)
            ORDER BY COALESCE(e.completed_at, e.enrolled_at) DESC
            LIMIT {$safeLimit}
        ";
        $rows = ems_learner_fetch_rows($conn, $fallbackSql, 'i', [$userId]);

        $synthetic = [];
        foreach ($rows as $row) {
            $courseId = (int)($row['course_id'] ?? 0);
            $synthetic[] = [
                'id' => 0,
                'course_id' => $courseId,
                'course_title' => (string)($row['course_title'] ?? 'Course'),
                'certificate_code' => 'EDU-' . str_pad((string)$courseId, 4, '0', STR_PAD_LEFT) . '-' . str_pad((string)$userId, 4, '0', STR_PAD_LEFT),
                'grade_label' => 'A',
                'file_path' => '',
                'download_url' => '',
                'status' => 'ready',
                'issued_at' => (string)($row['issued_at'] ?? date('Y-m-d H:i:s')),
            ];
        }

        return $synthetic;
    }
}

if (!function_exists('ems_learner_fetch_payments')) {
    function ems_learner_fetch_payments($conn, $learnerUserId, $limit = 100)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_payments')) {
            return [];
        }

        $safeLimit = max(1, min(200, (int)$limit));
        $sql = "
            SELECT
                p.id,
                p.order_id,
                p.transaction_ref,
                p.payment_method,
                p.payment_status,
                p.amount,
                p.currency_code,
                p.paid_at,
                p.created_at,
                oi.course_title_snapshot AS course_title
            FROM learner_payments p
            LEFT JOIN learner_order_items oi ON oi.order_id = p.order_id
            WHERE p.learner_user_id = ?
            ORDER BY COALESCE(p.paid_at, p.created_at) DESC, p.id DESC
            LIMIT {$safeLimit}
        ";

        $rows = ems_learner_fetch_rows($conn, $sql, 'i', [$userId]);
        foreach ($rows as &$row) {
            $row['amount_text'] = ems_learner_currency_format((float)($row['amount'] ?? 0), (string)($row['currency_code'] ?? 'USD'));
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_learner_fetch_wishlist_items')) {
    function ems_learner_fetch_wishlist_items($conn, $learnerUserId, $limit = 100)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_wishlist')) {
            return [];
        }

        $safeLimit = max(1, min(200, (int)$limit));
        $sql = "
            SELECT
                w.id,
                w.course_id,
                w.created_at,
                c.title,
                c.access_type,
                c.price_amount,
                c.currency_code,
                COALESCE(p.full_name, 'Instructor') AS provider_name,
                COALESCE(ROUND(AVG(CASE WHEN r.is_visible = 1 THEN r.rating END), 1), 0) AS avg_rating
            FROM learner_wishlist w
            INNER JOIN courses c ON c.id = w.course_id
            LEFT JOIN users p ON p.id = c.provider_user_id
            LEFT JOIN reviews r ON r.course_id = c.id
            WHERE w.learner_user_id = ?
            GROUP BY w.id
            ORDER BY w.created_at DESC
            LIMIT {$safeLimit}
        ";

        $rows = ems_learner_fetch_rows($conn, $sql, 'i', [$userId]);
        foreach ($rows as &$row) {
            $isPaid = strtolower((string)($row['access_type'] ?? 'free')) === 'paid';
            $row['price_text'] = $isPaid
                ? ems_learner_currency_format((float)($row['price_amount'] ?? 0), (string)($row['currency_code'] ?? 'USD'))
                : 'Free';
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_learner_fetch_cart_items')) {
    function ems_learner_fetch_cart_items($conn, $learnerUserId, $limit = 100)
    {
        $userId = (int)$learnerUserId;
        $empty = [
            'items' => [],
            'summary' => [
                'subtotal' => 0.0,
                'discount' => 0.0,
                'total' => 0.0,
                'currency_code' => 'USD',
            ],
        ];

        if ($userId <= 0 || !ems_learner_table_exists($conn, 'learner_cart_items')) {
            return $empty;
        }

        $safeLimit = max(1, min(200, (int)$limit));
        $sql = "
            SELECT
                ci.id,
                ci.course_id,
                ci.unit_price,
                ci.discount_amount,
                ci.currency_code,
                ci.coupon_code,
                ci.created_at,
                c.title,
                COALESCE(p.full_name, 'Instructor') AS provider_name
            FROM learner_cart_items ci
            INNER JOIN courses c ON c.id = ci.course_id
            LEFT JOIN users p ON p.id = c.provider_user_id
            WHERE ci.learner_user_id = ?
            ORDER BY ci.created_at DESC
            LIMIT {$safeLimit}
        ";

        $rows = ems_learner_fetch_rows($conn, $sql, 'i', [$userId]);

        $subtotal = 0.0;
        $discount = 0.0;
        $currency = 'USD';
        foreach ($rows as &$row) {
            $unit = (float)($row['unit_price'] ?? 0);
            $off = max(0.0, (float)($row['discount_amount'] ?? 0));
            $line = max(0.0, $unit - $off);
            $row['line_total'] = $line;
            $row['line_total_text'] = ems_learner_currency_format($line, (string)($row['currency_code'] ?? 'USD'));
            $row['unit_price_text'] = ems_learner_currency_format($unit, (string)($row['currency_code'] ?? 'USD'));
            $row['discount_text'] = ems_learner_currency_format($off, (string)($row['currency_code'] ?? 'USD'));

            $subtotal += $unit;
            $discount += $off;
            $currency = (string)($row['currency_code'] ?? $currency);
        }
        unset($row);

        $empty['items'] = $rows;
        $empty['summary'] = [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max(0.0, $subtotal - $discount),
            'currency_code' => $currency,
        ];

        return $empty;
    }
}

if (!function_exists('ems_learner_fetch_messages')) {
    function ems_learner_fetch_messages($conn, $learnerUserId, $limit = 100)
    {
        return ems_learner_fetch_recent_messages($conn, $learnerUserId, $limit);
    }
}

if (!function_exists('ems_learner_fetch_security_snapshot')) {
    function ems_learner_fetch_security_snapshot($conn, $learnerUserId)
    {
        $snapshot = [
            'password_updated_at' => null,
            'two_factor_enabled' => 0,
            'recent_login_at' => null,
            'trusted_device_name' => 'No trusted device',
        ];

        $userId = (int)$learnerUserId;
        if ($userId <= 0) {
            return $snapshot;
        }

        $settings = ems_learner_fetch_settings($conn, $userId);
        $snapshot['two_factor_enabled'] = (int)($settings['two_factor_enabled'] ?? 0) === 1 ? 1 : 0;

        if (ems_learner_table_exists($conn, 'learner_security_logs')) {
            $passwordLog = ems_learner_fetch_row(
                $conn,
                "SELECT occurred_at FROM learner_security_logs
                 WHERE learner_user_id = ? AND event_type = 'password_change'
                 ORDER BY occurred_at DESC
                 LIMIT 1",
                'i',
                [$userId]
            );
            if (!empty($passwordLog['occurred_at'])) {
                $snapshot['password_updated_at'] = (string)$passwordLog['occurred_at'];
            }

            $loginLog = ems_learner_fetch_row(
                $conn,
                "SELECT occurred_at FROM learner_security_logs
                 WHERE learner_user_id = ? AND event_type = 'login'
                 ORDER BY occurred_at DESC
                 LIMIT 1",
                'i',
                [$userId]
            );
            if (!empty($loginLog['occurred_at'])) {
                $snapshot['recent_login_at'] = (string)$loginLog['occurred_at'];
            }
        }

        if (ems_learner_table_exists($conn, 'learner_trusted_devices')) {
            $device = ems_learner_fetch_row(
                $conn,
                'SELECT device_name FROM learner_trusted_devices WHERE learner_user_id = ? AND is_active = 1 ORDER BY last_used_at DESC LIMIT 1',
                'i',
                [$userId]
            );
            if (!empty($device['device_name'])) {
                $snapshot['trusted_device_name'] = (string)$device['device_name'];
            }
        }

        return $snapshot;
    }
}

if (!function_exists('ems_learner_fetch_profile_card_data')) {
    function ems_learner_fetch_profile_card_data($conn, $learnerUserId)
    {
        return [
            'settings' => ems_learner_fetch_settings($conn, $learnerUserId),
            'security' => ems_learner_fetch_security_snapshot($conn, $learnerUserId),
            'notifications_unread' => ems_learner_count_unread_notifications($conn, $learnerUserId),
            'messages_unread' => ems_learner_count_unread_messages($conn, $learnerUserId),
            'cart_count' => ems_learner_count_cart_items($conn, $learnerUserId),
            'wishlist_count' => ems_learner_count_wishlist_items($conn, $learnerUserId),
        ];
    }
}

if (!function_exists('ems_learner_get_course_by_title')) {
    function ems_learner_get_course_by_title($conn, $title)
    {
        $titleValue = trim((string)$title);
        if ($titleValue === '' || !ems_learner_table_exists($conn, 'courses')) {
            return null;
        }

        $row = ems_learner_fetch_row(
            $conn,
            'SELECT id, provider_user_id, title, status, access_type, price_amount, currency_code
             FROM courses
             WHERE title = ?
             LIMIT 1',
            's',
            [$titleValue]
        );

        if ($row) {
            return $row;
        }

        $row = ems_learner_fetch_row(
            $conn,
            'SELECT id, provider_user_id, title, status, access_type, price_amount, currency_code
             FROM courses
             WHERE title LIKE ?
             ORDER BY FIELD(status, "published", "draft", "archived"), id DESC
             LIMIT 1',
            's',
            ['%' . $titleValue . '%']
        );

        return $row ?: null;
    }
}

if (!function_exists('ems_learner_course_is_eligible')) {
    function ems_learner_course_is_eligible($conn, $courseId)
    {
        $id = (int)$courseId;
        if ($id <= 0 || !ems_learner_table_exists($conn, 'courses')) {
            return null;
        }

        $row = ems_learner_fetch_row(
            $conn,
            "SELECT id, provider_user_id, title, status, access_type, price_amount, currency_code
             FROM courses
             WHERE id = ?
             LIMIT 1",
            'i',
            [$id]
        );

        if (!$row) {
            return null;
        }

        if (!in_array(strtolower((string)($row['status'] ?? 'draft')), ['published', 'archived'], true)) {
            return null;
        }

        return $row;
    }
}

if (!function_exists('ems_learner_is_enrolled_in_course')) {
    function ems_learner_is_enrolled_in_course($conn, $learnerUserId, $courseId)
    {
        $row = ems_learner_fetch_row(
            $conn,
            'SELECT id FROM enrollments WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
            'ii',
            [(int)$learnerUserId, (int)$courseId]
        );
        return !empty($row['id']);
    }
}

if (!function_exists('ems_learner_create_notification')) {
    function ems_learner_create_notification($conn, $learnerUserId, $type, $title, $message, $relatedUrl = null)
    {
        if (!ems_learner_table_exists($conn, 'learner_notifications')) {
            return;
        }

        ems_learner_exec(
            $conn,
            'INSERT INTO learner_notifications (learner_user_id, notification_type, title, message_text, related_url) VALUES (?, ?, ?, ?, ?)',
            'issss',
            [
                (int)$learnerUserId,
                trim((string)$type) ?: 'general',
                trim((string)$title) ?: 'Notification',
                trim((string)$message) ?: 'You have a new update.',
                $relatedUrl !== null ? trim((string)$relatedUrl) : null,
            ]
        );
    }
}

if (!function_exists('ems_learner_toggle_wishlist')) {
    function ems_learner_toggle_wishlist($conn, $learnerUserId, $courseId)
    {
        if (!ems_learner_table_exists($conn, 'learner_wishlist')) {
            return ['ok' => false, 'message' => 'Wishlist table is not available.'];
        }

        $userId = (int)$learnerUserId;
        $courseIdValue = (int)$courseId;
        if ($userId <= 0 || $courseIdValue <= 0) {
            return ['ok' => false, 'message' => 'Invalid learner or course.'];
        }

        $course = ems_learner_course_is_eligible($conn, $courseIdValue);
        if (!$course) {
            return ['ok' => false, 'message' => 'Course is not available.'];
        }

        $existing = ems_learner_fetch_row(
            $conn,
            'SELECT id FROM learner_wishlist WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
            'ii',
            [$userId, $courseIdValue]
        );

        if ($existing) {
            ems_learner_exec(
                $conn,
                'DELETE FROM learner_wishlist WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
                'ii',
                [$userId, $courseIdValue]
            );
            return ['ok' => true, 'state' => 'removed'];
        }

        ems_learner_exec(
            $conn,
            'INSERT INTO learner_wishlist (learner_user_id, course_id) VALUES (?, ?)',
            'ii',
            [$userId, $courseIdValue]
        );

        ems_learner_create_notification(
            $conn,
            $userId,
            'wishlist',
            'Course saved to wishlist',
            'You saved "' . (string)($course['title'] ?? 'course') . '" for later.',
            BASE_URL . 'learner/?page=wishlist'
        );

        return ['ok' => true, 'state' => 'added'];
    }
}

if (!function_exists('ems_learner_add_to_cart')) {
    function ems_learner_add_to_cart($conn, $learnerUserId, $courseId)
    {
        if (!ems_learner_table_exists($conn, 'learner_cart_items')) {
            return ['ok' => false, 'message' => 'Cart table is not available.'];
        }

        $userId = (int)$learnerUserId;
        $courseIdValue = (int)$courseId;
        if ($userId <= 0 || $courseIdValue <= 0) {
            return ['ok' => false, 'message' => 'Invalid learner or course.'];
        }

        if (ems_learner_is_enrolled_in_course($conn, $userId, $courseIdValue)) {
            return ['ok' => false, 'message' => 'You are already enrolled in this course.'];
        }

        $course = ems_learner_course_is_eligible($conn, $courseIdValue);
        if (!$course) {
            return ['ok' => false, 'message' => 'Course is not available for enrollment.'];
        }

        $accessType = strtolower((string)($course['access_type'] ?? 'free'));
        $price = $accessType === 'paid' ? (float)($course['price_amount'] ?? 0) : 0.0;
        $currency = strtoupper(trim((string)($course['currency_code'] ?? 'USD')));
        if (!in_array($currency, ['USD', 'NPR', 'EUR'], true)) {
            $currency = 'USD';
        }

        ems_learner_exec(
            $conn,
            'INSERT INTO learner_cart_items (learner_user_id, course_id, unit_price, discount_amount, currency_code, created_at, updated_at)
             VALUES (?, ?, ?, 0, ?, NOW(), NOW())
             ON DUPLICATE KEY UPDATE unit_price = VALUES(unit_price), currency_code = VALUES(currency_code), updated_at = NOW()',
            'iids',
            [$userId, $courseIdValue, $price, $currency]
        );

        ems_learner_create_notification(
            $conn,
            $userId,
            'cart',
            'Course added to cart',
            '"' . (string)($course['title'] ?? 'Course') . '" was added to your cart.',
            BASE_URL . 'learner/?page=cart'
        );

        return ['ok' => true, 'message' => 'Course added to cart.'];
    }
}

if (!function_exists('ems_learner_remove_from_cart')) {
    function ems_learner_remove_from_cart($conn, $learnerUserId, $courseId)
    {
        if (!ems_learner_table_exists($conn, 'learner_cart_items')) {
            return ['ok' => false, 'message' => 'Cart table is not available.'];
        }

        $result = ems_learner_exec(
            $conn,
            'DELETE FROM learner_cart_items WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
            'ii',
            [(int)$learnerUserId, (int)$courseId]
        );

        return [
            'ok' => $result['ok'],
            'removed' => (int)($result['affected'] ?? 0) > 0,
        ];
    }
}

if (!function_exists('ems_learner_generate_reference')) {
    function ems_learner_generate_reference($prefix)
    {
        $safePrefix = strtoupper(trim((string)$prefix));
        if ($safePrefix === '') {
            $safePrefix = 'REF';
        }

        try {
            $random = strtoupper(bin2hex(random_bytes(4)));
        } catch (Throwable $throwable) {
            $random = strtoupper(substr(md5(uniqid('', true)), 0, 8));
        }

        return $safePrefix . '-' . date('Ymd') . '-' . $random;
    }
}

if (!function_exists('ems_learner_issue_certificate')) {
    function ems_learner_issue_certificate($conn, $learnerUserId, $courseId, $enrollmentId = null)
    {
        if (!ems_learner_table_exists($conn, 'learner_certificates')) {
            return null;
        }

        $userId = (int)$learnerUserId;
        $courseIdValue = (int)$courseId;
        if ($userId <= 0 || $courseIdValue <= 0) {
            return null;
        }

        $existing = ems_learner_fetch_row(
            $conn,
            'SELECT id, certificate_code FROM learner_certificates WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
            'ii',
            [$userId, $courseIdValue]
        );
        if ($existing) {
            return $existing;
        }

        $code = ems_learner_generate_reference('EDU');
        ems_learner_exec(
            $conn,
            'INSERT INTO learner_certificates (learner_user_id, course_id, enrollment_id, certificate_code, grade_label, status, issued_at)
             VALUES (?, ?, ?, ?, "A", "ready", NOW())',
            'iiis',
            [$userId, $courseIdValue, $enrollmentId !== null ? (int)$enrollmentId : null, $code]
        );

        ems_learner_create_notification(
            $conn,
            $userId,
            'certificate',
            'Certificate available',
            'Your certificate is ready for download.',
            BASE_URL . 'learner/?page=certificates'
        );

        return ems_learner_fetch_row(
            $conn,
            'SELECT id, certificate_code FROM learner_certificates WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
            'ii',
            [$userId, $courseIdValue]
        );
    }
}

if (!function_exists('ems_learner_refresh_enrollment_progress')) {
    function ems_learner_refresh_enrollment_progress($conn, $learnerUserId, $courseId)
    {
        $userId = (int)$learnerUserId;
        $courseIdValue = (int)$courseId;
        if ($userId <= 0 || $courseIdValue <= 0) {
            return 0.0;
        }

        $totalLessonRow = ems_learner_fetch_row(
            $conn,
            'SELECT COUNT(*) AS total FROM course_lessons WHERE course_id = ?',
            'i',
            [$courseIdValue]
        );
        $totalLessons = (int)($totalLessonRow['total'] ?? 0);

        if ($totalLessons <= 0 || !ems_learner_table_exists($conn, 'learner_lesson_progress')) {
            return 0.0;
        }

        $completedRow = ems_learner_fetch_row(
            $conn,
            'SELECT COUNT(*) AS total FROM learner_lesson_progress WHERE learner_user_id = ? AND course_id = ? AND (is_completed = 1 OR progress_percent >= 100)',
            'ii',
            [$userId, $courseIdValue]
        );
        $completedLessons = (int)($completedRow['total'] ?? 0);
        $courseProgress = min(100.0, ($completedLessons / $totalLessons) * 100);

        ems_learner_exec(
            $conn,
            'UPDATE enrollments SET progress_percent = ?, completed_at = CASE WHEN ? >= 100 THEN NOW() ELSE completed_at END,
                enrollment_status = CASE WHEN ? >= 100 THEN "completed" ELSE enrollment_status END
             WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
            'dddii',
            [$courseProgress, $courseProgress, $courseProgress, $userId, $courseIdValue]
        );

        if ($courseProgress >= 100) {
            $enrollment = ems_learner_fetch_row(
                $conn,
                'SELECT id FROM enrollments WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
                'ii',
                [$userId, $courseIdValue]
            );
            ems_learner_issue_certificate($conn, $userId, $courseIdValue, (int)($enrollment['id'] ?? 0));
        }

        return $courseProgress;
    }
}

if (!function_exists('ems_learner_update_lesson_progress')) {
    function ems_learner_update_lesson_progress($conn, $learnerUserId, $courseId, $lessonId, $progressPercent, $minutesSpent = 0, $lastPositionSeconds = 0, $isCompleted = false)
    {
        if (!ems_learner_table_exists($conn, 'learner_lesson_progress')) {
            return ['ok' => false, 'message' => 'Lesson progress table is not available.'];
        }

        $userId = (int)$learnerUserId;
        $courseIdValue = (int)$courseId;
        $lessonIdValue = (int)$lessonId;
        if ($userId <= 0 || $courseIdValue <= 0 || $lessonIdValue <= 0) {
            return ['ok' => false, 'message' => 'Invalid progress payload.'];
        }

        $enrollment = ems_learner_fetch_row(
            $conn,
            'SELECT id FROM enrollments WHERE learner_user_id = ? AND course_id = ? LIMIT 1',
            'ii',
            [$userId, $courseIdValue]
        );
        if (!$enrollment) {
            return ['ok' => false, 'message' => 'Learner is not enrolled in this course.'];
        }

        $progress = max(0.0, min(100.0, (float)$progressPercent));
        $minutes = max(0, (int)$minutesSpent);
        $position = max(0, (int)$lastPositionSeconds);
        $completed = $isCompleted ? 1 : ($progress >= 100 ? 1 : 0);

        ems_learner_exec(
            $conn,
            'INSERT INTO learner_lesson_progress (learner_user_id, course_id, lesson_id, progress_percent, is_completed, minutes_spent, last_position_seconds, last_activity_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE
                 progress_percent = VALUES(progress_percent),
                 is_completed = VALUES(is_completed),
                 minutes_spent = minutes_spent + VALUES(minutes_spent),
                 last_position_seconds = VALUES(last_position_seconds),
                 last_activity_at = NOW()',
            'iiidiid',
            [$userId, $courseIdValue, $lessonIdValue, $progress, $completed, $minutes, $position]
        );

        $courseProgress = ems_learner_refresh_enrollment_progress($conn, $userId, $courseIdValue);

        return [
            'ok' => true,
            'course_progress_percent' => $courseProgress,
        ];
    }
}

if (!function_exists('ems_learner_submit_quiz_attempt')) {
    function ems_learner_submit_quiz_attempt($conn, $learnerUserId, $courseId, $lessonId, $scorePercent, array $answers = [])
    {
        if (!ems_learner_table_exists($conn, 'learner_quiz_attempts')) {
            return ['ok' => false, 'message' => 'Quiz attempt table is not available.'];
        }

        $userId = (int)$learnerUserId;
        $courseIdValue = (int)$courseId;
        $lessonIdValue = (int)$lessonId;
        if ($userId <= 0 || $courseIdValue <= 0 || $lessonIdValue <= 0) {
            return ['ok' => false, 'message' => 'Invalid quiz payload.'];
        }

        $score = max(0.0, min(100.0, (float)$scorePercent));
        $attemptNoRow = ems_learner_fetch_row(
            $conn,
            'SELECT COALESCE(MAX(attempt_no), 0) + 1 AS next_attempt FROM learner_quiz_attempts WHERE learner_user_id = ? AND course_id = ? AND lesson_id = ?',
            'iii',
            [$userId, $courseIdValue, $lessonIdValue]
        );
        $attemptNo = max(1, (int)($attemptNoRow['next_attempt'] ?? 1));

        ems_learner_exec(
            $conn,
            'INSERT INTO learner_quiz_attempts (learner_user_id, course_id, lesson_id, attempt_no, score_percent, answers_json, attempted_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())',
            'iiiids',
            [$userId, $courseIdValue, $lessonIdValue, $attemptNo, $score, json_encode($answers, JSON_UNESCAPED_UNICODE)]
        );

        if ($score >= 60) {
            ems_learner_update_lesson_progress($conn, $userId, $courseIdValue, $lessonIdValue, 100, 0, 0, true);
        }

        return [
            'ok' => true,
            'attempt_no' => $attemptNo,
            'score_percent' => $score,
        ];
    }
}

if (!function_exists('ems_learner_checkout')) {
    function ems_learner_checkout($conn, $learnerUserId, $paymentMethod = 'card', $singleCourseId = 0)
    {
        if (!ems_learner_table_exists($conn, 'learner_orders') || !ems_learner_table_exists($conn, 'learner_order_items') || !ems_learner_table_exists($conn, 'learner_payments')) {
            return ['ok' => false, 'message' => 'Checkout tables are not available.'];
        }

        $userId = (int)$learnerUserId;
        if ($userId <= 0) {
            return ['ok' => false, 'message' => 'Invalid learner account.'];
        }

        $method = strtolower(trim((string)$paymentMethod));
        if (!in_array($method, ['card', 'paypal', 'manual', 'wallet'], true)) {
            $method = 'card';
        }

        $items = [];
        $singleCourse = (int)$singleCourseId;
        if ($singleCourse > 0) {
            $course = ems_learner_course_is_eligible($conn, $singleCourse);
            if (!$course) {
                return ['ok' => false, 'message' => 'Selected course is not available.'];
            }
            if (!ems_learner_is_enrolled_in_course($conn, $userId, $singleCourse)) {
                $items[] = [
                    'course_id' => (int)$course['id'],
                    'provider_user_id' => (int)($course['provider_user_id'] ?? 0),
                    'title' => (string)($course['title'] ?? 'Course'),
                    'unit_price' => strtolower((string)($course['access_type'] ?? 'free')) === 'paid' ? (float)($course['price_amount'] ?? 0) : 0.0,
                    'discount_amount' => 0.0,
                    'currency_code' => (string)($course['currency_code'] ?? 'USD'),
                ];
            }
        } else {
            $cartPayload = ems_learner_fetch_cart_items($conn, $userId, 500);
            foreach ($cartPayload['items'] as $cartItem) {
                $courseId = (int)($cartItem['course_id'] ?? 0);
                if ($courseId <= 0 || ems_learner_is_enrolled_in_course($conn, $userId, $courseId)) {
                    continue;
                }
                $items[] = [
                    'course_id' => $courseId,
                    'provider_user_id' => 0,
                    'title' => (string)($cartItem['title'] ?? 'Course'),
                    'unit_price' => (float)($cartItem['unit_price'] ?? 0),
                    'discount_amount' => (float)($cartItem['discount_amount'] ?? 0),
                    'currency_code' => (string)($cartItem['currency_code'] ?? 'USD'),
                ];
            }
        }

        if (empty($items)) {
            return ['ok' => false, 'message' => 'No eligible course found for checkout.'];
        }

        $subtotal = 0.0;
        $discount = 0.0;
        $currencyCode = 'USD';
        foreach ($items as $item) {
            $subtotal += max(0.0, (float)$item['unit_price']);
            $discount += max(0.0, (float)$item['discount_amount']);
            $currencyCode = strtoupper(trim((string)($item['currency_code'] ?? $currencyCode))) ?: $currencyCode;
        }
        $total = max(0.0, $subtotal - $discount);

        $orderRef = ems_learner_generate_reference('ORD');
        $transactionRef = ems_learner_generate_reference('TXN');

        $conn->begin_transaction();
        try {
            $orderInsert = ems_learner_exec(
                $conn,
                'INSERT INTO learner_orders (learner_user_id, order_ref, order_status, subtotal_amount, discount_amount, total_amount, currency_code, payment_method, placed_at, paid_at)
                 VALUES (?, ?, "paid", ?, ?, ?, ?, ?, NOW(), NOW())',
                'isdddss',
                [$userId, $orderRef, $subtotal, $discount, $total, $currencyCode, $method]
            );

            if (!$orderInsert['ok']) {
                throw new RuntimeException('Failed to create learner order.');
            }
            $orderId = (int)$orderInsert['insert_id'];
            if ($orderId <= 0) {
                throw new RuntimeException('Invalid order id.');
            }

            foreach ($items as $item) {
                ems_learner_exec(
                    $conn,
                    'INSERT INTO learner_order_items (order_id, course_id, provider_user_id, course_title_snapshot, unit_price, discount_amount, total_amount)
                     VALUES (?, ?, ?, ?, ?, ?, ?)',
                    'iiisddd',
                    [
                        $orderId,
                        (int)$item['course_id'],
                        (int)$item['provider_user_id'],
                        (string)$item['title'],
                        (float)$item['unit_price'],
                        (float)$item['discount_amount'],
                        max(0.0, (float)$item['unit_price'] - (float)$item['discount_amount']),
                    ]
                );

                ems_learner_exec(
                    $conn,
                    'INSERT INTO enrollments (course_id, learner_user_id, enrollment_status, enrolled_at, progress_percent)
                     VALUES (?, ?, "active", NOW(), 0)
                     ON DUPLICATE KEY UPDATE enrollment_status = VALUES(enrollment_status), enrolled_at = COALESCE(enrolled_at, VALUES(enrolled_at))',
                    'ii',
                    [(int)$item['course_id'], $userId]
                );

                if (ems_learner_table_exists($conn, 'learner_cart_items')) {
                    ems_learner_exec(
                        $conn,
                        'DELETE FROM learner_cart_items WHERE learner_user_id = ? AND course_id = ?',
                        'ii',
                        [$userId, (int)$item['course_id']]
                    );
                }
            }

            $paymentInsert = ems_learner_exec(
                $conn,
                'INSERT INTO learner_payments (order_id, learner_user_id, transaction_ref, payment_gateway, payment_method, payment_status, amount, currency_code, paid_at)
                 VALUES (?, ?, ?, "internal", ?, "paid", ?, ?, NOW())',
                'iissds',
                [$orderId, $userId, $transactionRef, $method, $total, $currencyCode]
            );
            if (!$paymentInsert['ok']) {
                throw new RuntimeException('Failed to persist payment record.');
            }

            ems_learner_create_notification(
                $conn,
                $userId,
                'payment',
                'Payment successful',
                'Your payment was successful and courses were enrolled.',
                BASE_URL . 'learner/?page=payments'
            );

            $conn->commit();

            return [
                'ok' => true,
                'order_id' => $orderId,
                'order_ref' => $orderRef,
                'transaction_ref' => $transactionRef,
                'total_amount' => $total,
                'currency_code' => $currencyCode,
                'redirect_url' => BASE_URL . 'learner/?page=courses',
            ];
        } catch (Throwable $throwable) {
            $conn->rollback();
            return ['ok' => false, 'message' => $throwable->getMessage()];
        }
    }
}

if (!function_exists('ems_learner_mark_notification_read')) {
    function ems_learner_mark_notification_read($conn, $learnerUserId, $notificationId)
    {
        if (!ems_learner_table_exists($conn, 'learner_notifications')) {
            return false;
        }

        $result = ems_learner_exec(
            $conn,
            'UPDATE learner_notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND learner_user_id = ? LIMIT 1',
            'ii',
            [(int)$notificationId, (int)$learnerUserId]
        );

        return $result['ok'] && (int)$result['affected'] >= 0;
    }
}

if (!function_exists('ems_learner_mark_all_notifications_read')) {
    function ems_learner_mark_all_notifications_read($conn, $learnerUserId)
    {
        if (!ems_learner_table_exists($conn, 'learner_notifications')) {
            return false;
        }

        $result = ems_learner_exec(
            $conn,
            'UPDATE learner_notifications SET is_read = 1, read_at = NOW() WHERE learner_user_id = ? AND is_read = 0',
            'i',
            [(int)$learnerUserId]
        );

        return $result['ok'];
    }
}

if (!function_exists('ems_learner_mark_message_read')) {
    function ems_learner_mark_message_read($conn, $learnerUserId, $messageId)
    {
        if (!ems_learner_table_exists($conn, 'learner_messages')) {
            return false;
        }

        $result = ems_learner_exec(
            $conn,
            'UPDATE learner_messages SET is_read = 1 WHERE id = ? AND learner_user_id = ? LIMIT 1',
            'ii',
            [(int)$messageId, (int)$learnerUserId]
        );

        return $result['ok'];
    }
}

if (!function_exists('ems_learner_send_message')) {
    function ems_learner_send_message($conn, $learnerUserId, $providerUserId, $courseId, $subject, $messageText)
    {
        if (!ems_learner_table_exists($conn, 'learner_messages')) {
            return ['ok' => false, 'message' => 'Messages table is not available.'];
        }

        $subjectValue = trim((string)$subject);
        $bodyValue = trim((string)$messageText);
        if ($subjectValue === '' || $bodyValue === '') {
            return ['ok' => false, 'message' => 'Subject and message are required.'];
        }

        $result = ems_learner_exec(
            $conn,
            'INSERT INTO learner_messages (learner_user_id, provider_user_id, course_id, direction, subject, message_text, is_read, sent_at)
             VALUES (?, ?, ?, "outbound", ?, ?, 1, NOW())',
            'iiiss',
            [(int)$learnerUserId, (int)$providerUserId, (int)$courseId, $subjectValue, $bodyValue]
        );

        return ['ok' => $result['ok'], 'message_id' => (int)($result['insert_id'] ?? 0)];
    }
}

if (!function_exists('ems_learner_update_settings')) {
    function ems_learner_update_settings($conn, $learnerUserId, array $payload)
    {
        if (!ems_learner_table_exists($conn, 'learner_settings')) {
            return ['ok' => false, 'message' => 'Settings table is not available.'];
        }

        $userId = (int)$learnerUserId;
        if ($userId <= 0) {
            return ['ok' => false, 'message' => 'Invalid learner account.'];
        }

        ems_learner_ensure_settings_row($conn, $userId);

        $language = trim((string)($payload['language_code'] ?? 'en')) ?: 'en';
        $timezone = trim((string)($payload['timezone'] ?? 'Asia/Kolkata')) ?: 'Asia/Kolkata';
        $notifyEmail = !empty($payload['notification_email_enabled']) ? 1 : 0;
        $theme = ems_learner_normalize_theme($payload['theme_preference'] ?? 'light');
        $twoFactor = !empty($payload['two_factor_enabled']) ? 1 : 0;

        $result = ems_learner_exec(
            $conn,
            'UPDATE learner_settings
             SET language_code = ?, timezone = ?, notification_email_enabled = ?, theme_preference = ?, two_factor_enabled = ?, updated_at = NOW()
             WHERE learner_user_id = ?
             LIMIT 1',
            'ssisii',
            [$language, $timezone, $notifyEmail, $theme, $twoFactor, $userId]
        );

        if ($result['ok']) {
            ems_learner_create_notification(
                $conn,
                $userId,
                'settings',
                'Account settings updated',
                'Your learner account settings were updated successfully.',
                BASE_URL . 'learner/?page=settings'
            );
        }

        return ['ok' => $result['ok']];
    }
}

if (!function_exists('ems_learner_log_security_event')) {
    function ems_learner_log_security_event($conn, $learnerUserId, $eventType, $eventLabel, $deviceName = null)
    {
        if (!ems_learner_table_exists($conn, 'learner_security_logs')) {
            return;
        }

        $ipAddress = trim((string)($_SERVER['REMOTE_ADDR'] ?? ''));
        $userAgent = trim((string)($_SERVER['HTTP_USER_AGENT'] ?? ''));

        ems_learner_exec(
            $conn,
            'INSERT INTO learner_security_logs (learner_user_id, event_type, event_label, ip_address, user_agent, device_name, occurred_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW())',
            'isssss',
            [
                (int)$learnerUserId,
                trim((string)$eventType) ?: 'login',
                trim((string)$eventLabel) ?: 'Security event',
                $ipAddress !== '' ? $ipAddress : null,
                $userAgent !== '' ? substr($userAgent, 0, 255) : null,
                $deviceName !== null ? trim((string)$deviceName) : null,
            ]
        );
    }
}

if (!function_exists('ems_learner_guess_device_name')) {
    function ems_learner_guess_device_name()
    {
        $ua = strtolower((string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if ($ua === '') {
            return 'Unknown Device';
        }

        if (strpos($ua, 'windows') !== false) {
            return 'Windows Desktop';
        }
        if (strpos($ua, 'mac os') !== false || strpos($ua, 'macintosh') !== false) {
            return 'Mac Device';
        }
        if (strpos($ua, 'android') !== false) {
            return 'Android Device';
        }
        if (strpos($ua, 'iphone') !== false || strpos($ua, 'ipad') !== false) {
            return 'iOS Device';
        }

        return 'Browser Device';
    }
}

if (!function_exists('ems_learner_register_trusted_device')) {
    function ems_learner_register_trusted_device($conn, $learnerUserId)
    {
        if (!ems_learner_table_exists($conn, 'learner_trusted_devices')) {
            return;
        }

        $userId = (int)$learnerUserId;
        if ($userId <= 0) {
            return;
        }

        $ua = (string)($_SERVER['HTTP_USER_AGENT'] ?? 'unknown-agent');
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
        $hash = hash('sha256', $ua . '|' . $ip . '|ems-trusted-device');
        $deviceName = ems_learner_guess_device_name();

        ems_learner_exec(
            $conn,
            'INSERT INTO learner_trusted_devices (learner_user_id, device_hash, device_name, is_active, first_seen_at, last_used_at)
             VALUES (?, ?, ?, 1, NOW(), NOW())
             ON DUPLICATE KEY UPDATE device_name = VALUES(device_name), is_active = 1, last_used_at = NOW()',
            'iss',
            [$userId, $hash, $deviceName]
        );
    }
}

if (!function_exists('ems_learner_change_password')) {
    function ems_learner_change_password($conn, $learnerUserId, $currentPassword, $newPassword, $confirmPassword)
    {
        $userId = (int)$learnerUserId;
        if ($userId <= 0) {
            return ['ok' => false, 'message' => 'Invalid learner account.'];
        }

        $current = (string)$currentPassword;
        $new = (string)$newPassword;
        $confirm = (string)$confirmPassword;

        if ($new === '' || $confirm === '' || $current === '') {
            return ['ok' => false, 'message' => 'All password fields are required.'];
        }

        if ($new !== $confirm) {
            return ['ok' => false, 'message' => 'New password and confirmation do not match.'];
        }

        if (strlen($new) < 8 || !preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) {
            return ['ok' => false, 'message' => 'Password must be at least 8 characters and include letters and numbers.'];
        }

        $userRow = ems_learner_fetch_row(
            $conn,
            'SELECT password_hash FROM users WHERE id = ? LIMIT 1',
            'i',
            [$userId]
        );
        if (!$userRow || !password_verify($current, (string)($userRow['password_hash'] ?? ''))) {
            return ['ok' => false, 'message' => 'Current password is incorrect.'];
        }

        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $result = ems_learner_exec(
            $conn,
            'UPDATE users SET password_hash = ? WHERE id = ? LIMIT 1',
            'si',
            [$newHash, $userId]
        );

        if (!$result['ok']) {
            return ['ok' => false, 'message' => 'Unable to update password.'];
        }

        ems_learner_log_security_event($conn, $userId, 'password_change', 'Password updated', ems_learner_guess_device_name());
        ems_learner_create_notification(
            $conn,
            $userId,
            'security',
            'Password changed',
            'Your account password was updated successfully.',
            BASE_URL . 'learner/?page=security'
        );

        return ['ok' => true, 'message' => 'Password updated successfully.'];
    }
}

if (!function_exists('ems_learner_fetch_all_published_courses')) {
    function ems_learner_fetch_all_published_courses($conn, $limit = 1000, $offset = 0)
    {
        if (!ems_learner_table_exists($conn, 'courses')) {
            return [];
        }

        $safeLimit = max(1, min(1000, (int)$limit));
        $safeOffset = max(0, (int)$offset);

        $sql = "
            SELECT
                c.id,
                c.title,
                c.short_description,
                c.level,
                c.duration_label,
                c.student_count_estimate,
                c.price_amount,
                c.currency_code,
                c.access_type,
                c.thumbnail_path,
                u.full_name AS instructor_name,
                COUNT(r.id) AS review_count,
                COALESCE(AVG(r.rating), 0) AS avg_rating
            FROM courses c
            LEFT JOIN users u ON u.id = c.provider_user_id
            LEFT JOIN reviews r ON r.course_id = c.id AND r.is_visible = 1
            WHERE c.status = 'published' OR c.status = 'archived'
            GROUP BY c.id
            ORDER BY c.published_at DESC, c.id DESC
            LIMIT " . $safeLimit . " OFFSET " . $safeOffset . "
        ";

        $rows = ems_learner_fetch_rows($conn, $sql);
        
        foreach ($rows as &$row) {
            $row['id'] = (int)$row['id'];
            $row['price_amount'] = (float)($row['price_amount'] ?? 0);
            $row['student_count_estimate'] = (int)($row['student_count_estimate'] ?? 0);
            $row['review_count'] = (int)($row['review_count'] ?? 0);
            $row['avg_rating'] = round((float)($row['avg_rating'] ?? 0), 1);
            $row['thumbnail_url'] = ems_learner_media_url($row['thumbnail_path'], BASE_URL . 'assets/images/cources/web-dev.jpg');
            unset($row['thumbnail_path']);
        }
        unset($row);

        return $rows;
    }
}

if (!function_exists('ems_learner_fetch_course_by_id')) {
    function ems_learner_fetch_course_by_id($conn, $courseId)
    {
        $id = (int)$courseId;
        if ($id <= 0 || !ems_learner_table_exists($conn, 'courses')) {
            return null;
        }

        $row = ems_learner_fetch_row(
            $conn,
            "
            SELECT
                c.id,
                c.title,
                c.short_description,
                c.description,
                c.level,
                c.language,
                c.duration_label,
                c.lesson_count_estimate,
                c.student_count_estimate,
                c.certification_enabled,
                c.includes_json,
                c.outcomes_json,
                c.requirements_json,
                c.thumbnail_path,
                c.promo_video_url,
                c.gallery_json,
                c.access_type,
                c.price_amount,
                c.currency_code,
                c.status,
                c.published_at,
                u.id AS provider_user_id,
                u.full_name AS instructor_name,
                COUNT(r.id) AS review_count,
                COALESCE(AVG(r.rating), 0) AS avg_rating,
                COUNT(DISTINCT e.id) AS enrollment_count
            FROM courses c
            LEFT JOIN users u ON u.id = c.provider_user_id
            LEFT JOIN reviews r ON r.course_id = c.id AND r.is_visible = 1
            LEFT JOIN enrollments e ON e.course_id = c.id AND e.enrollment_status = 'active'
            WHERE c.id = ?
            GROUP BY c.id
            LIMIT 1
            ",
            'i',
            [$id]
        );

        if (!$row) {
            return null;
        }

        // Format the response
        $row['id'] = (int)$row['id'];
        $row['price_amount'] = (float)($row['price_amount'] ?? 0);
        $row['lesson_count_estimate'] = (int)($row['lesson_count_estimate'] ?? 0);
        $row['student_count_estimate'] = (int)($row['student_count_estimate'] ?? 0);
        $row['certification_enabled'] = (int)($row['certification_enabled'] ?? 1) === 1;
        $row['review_count'] = (int)($row['review_count'] ?? 0);
        $row['avg_rating'] = round((float)($row['avg_rating'] ?? 0), 1);
        $row['enrollment_count'] = (int)($row['enrollment_count'] ?? 0);
        $row['thumbnail_url'] = ems_learner_media_url($row['thumbnail_path'], BASE_URL . 'assets/images/cources/web-dev.jpg');
        
        // Parse JSON fields
        $row['includes'] = [];
        if ($row['includes_json']) {
            $decoded = json_decode($row['includes_json'], true);
            $row['includes'] = is_array($decoded) ? $decoded : [];
        }
        
        $row['outcomes'] = [];
        if ($row['outcomes_json']) {
            $decoded = json_decode($row['outcomes_json'], true);
            $row['outcomes'] = is_array($decoded) ? $decoded : [];
        }
        
        $row['requirements'] = [];
        if ($row['requirements_json']) {
            $decoded = json_decode($row['requirements_json'], true);
            $row['requirements'] = is_array($decoded) ? $decoded : [];
        }
        
        $row['gallery'] = [];
        if ($row['gallery_json']) {
            $decoded = json_decode($row['gallery_json'], true);
            $row['gallery'] = is_array($decoded) ? $decoded : [];
        }

        // Fetch sections and lessons
        $sections = ems_learner_fetch_rows(
            $conn,
            "
            SELECT
                cs.id,
                cs.section_order,
                cs.title AS section_title
            FROM course_sections cs
            WHERE cs.course_id = ?
            ORDER BY cs.section_order ASC
            ",
            'i',
            [$id]
        );

        $row['sections'] = [];
        foreach ($sections as $section) {
            $lessons = ems_learner_fetch_rows(
                $conn,
                "
                SELECT
                    cl.id,
                    cl.lesson_order,
                    cl.lesson_type,
                    cl.title AS lesson_title,
                    cl.duration_seconds,
                    cl.is_preview
                FROM course_lessons cl
                WHERE cl.course_id = ? AND (cl.section_id = ? OR cl.section_id IS NULL)
                ORDER BY cl.lesson_order ASC
                ",
                'ii',
                [$id, (int)$section['id']]
            );

            foreach ($lessons as &$lesson) {
                $lesson['id'] = (int)$lesson['id'];
                $lesson['lesson_order'] = (int)$lesson['lesson_order'];
                $lesson['duration_seconds'] = (int)($lesson['duration_seconds'] ?? 0);
                $lesson['is_preview'] = (int)$lesson['is_preview'] === 1;
                $lesson['duration_label'] = ems_learner_seconds_to_duration($lesson['duration_seconds']);
            }
            unset($lesson);

            $row['sections'][] = [
                'id' => (int)$section['id'],
                'section_order' => (int)$section['section_order'],
                'section_title' => $section['section_title'],
                'lessons' => $lessons,
            ];
        }

        // Fetch reviews
        $reviews = ems_learner_fetch_rows(
            $conn,
            "
            SELECT
                r.id,
                r.rating,
                r.review_text,
                r.created_at,
                u.full_name AS learner_name
            FROM reviews r
            LEFT JOIN users u ON u.id = r.learner_user_id
            WHERE r.course_id = ? AND r.is_visible = 1
            ORDER BY r.created_at DESC
            LIMIT 10
            ",
            'i',
            [$id]
        );

        foreach ($reviews as &$review) {
            $review['id'] = (int)$review['id'];
            $review['rating'] = (int)$review['rating'];
            $review['time_ago'] = ems_learner_relative_time($review['created_at']);
        }
        unset($review);

        $row['reviews'] = $reviews;
        
        // Remove JSON fields as they're now parsed
        unset($row['includes_json'], $row['outcomes_json'], $row['requirements_json'], $row['gallery_json'], $row['thumbnail_path']);

        return $row;
    }
}

