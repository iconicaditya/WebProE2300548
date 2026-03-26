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

