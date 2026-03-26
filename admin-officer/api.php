<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/includes/admin_data.php');

ems_require_login(['officer']);

$portalUser = ems_load_portal_user($conn);
if (!$portalUser || ($portalUser['role'] ?? '') !== 'officer') {
    ems_admin_api_fail('FORBIDDEN', 'Admin officer account is required.', 403);
}

$officerUserId = (int)($portalUser['id'] ?? 0);
if ($officerUserId <= 0) {
    ems_admin_api_fail('FORBIDDEN', 'Invalid admin officer account.', 403);
}

$action = trim((string)($_REQUEST['action'] ?? ''));
if ($action === '') {
    ems_admin_api_fail('BAD_REQUEST', 'Missing action.', 400);
}

$writeActions = [
    'provider_review',
    'course_review',
    'user_status_update',
    'settings_update',
    'profile_update',
    'notification_mark_read',
    'report_export_request',
];

if (in_array($action, $writeActions, true)) {
    ems_admin_api_require_post();
    ems_admin_api_verify_csrf();
}

switch ($action) {
    case 'dashboard_summary':
        ems_admin_api_ok([
            'metrics' => ems_admin_fetch_dashboard_metrics($conn),
            'activity_trend' => ems_admin_fetch_activity_trend($conn, 4),
            'revenue_breakdown' => ems_admin_fetch_revenue_breakdown($conn),
        ]);
        break;

    case 'activity_feed':
        $limit = ems_admin_api_int($_GET, 'limit', 10);
        ems_admin_api_ok([
            'items' => ems_admin_fetch_recent_activity($conn, $limit),
        ]);
        break;

    case 'providers_list':
        $status = ems_admin_api_text($_GET, 'status', 'all');
        $query = ems_admin_api_text($_GET, 'q', '');
        $limit = ems_admin_api_int($_GET, 'limit', 100);
        ems_admin_api_ok([
            'items' => ems_admin_provider_fetch_management_rows($conn, $status, $limit, $query),
            'filters' => ['status' => $status, 'q' => $query],
        ]);
        break;

    case 'learners_list':
        $status = ems_admin_api_text($_GET, 'status', 'all');
        $query = ems_admin_api_text($_GET, 'q', '');
        $limit = ems_admin_api_int($_GET, 'limit', 100);
        ems_admin_api_ok([
            'items' => ems_admin_fetch_learner_management_rows($conn, $status, $query, $limit),
            'filters' => ['status' => $status, 'q' => $query],
        ]);
        break;

    case 'courses_list':
        $status = ems_admin_api_text($_GET, 'status', 'all');
        $query = ems_admin_api_text($_GET, 'q', '');
        $limit = ems_admin_api_int($_GET, 'limit', 100);
        ems_admin_api_ok([
            'items' => ems_admin_fetch_course_management_rows($conn, $status, $query, $limit),
            'filters' => ['status' => $status, 'q' => $query],
        ]);
        break;

    case 'users_list':
        $role = ems_admin_api_text($_GET, 'role', 'all');
        $status = ems_admin_api_text($_GET, 'status', 'all');
        $query = ems_admin_api_text($_GET, 'q', '');
        $limit = ems_admin_api_int($_GET, 'limit', 100);
        ems_admin_api_ok([
            'items' => ems_admin_fetch_user_management_rows($conn, $role, $status, $query, $limit),
            'filters' => ['role' => $role, 'status' => $status, 'q' => $query],
        ]);
        break;

    case 'reports_summary':
        ems_admin_api_ok([
            'overview' => ems_admin_fetch_reports_overview($conn),
            'dashboard' => ems_admin_fetch_dashboard_metrics($conn),
        ]);
        break;

    case 'settings_get':
        ems_admin_api_ok([
            'settings' => ems_admin_fetch_platform_settings($conn),
        ]);
        break;

    case 'profile_get':
        ems_admin_api_ok([
            'profile' => ems_admin_fetch_officer_profile($conn, $officerUserId, $portalUser),
        ]);
        break;

    case 'report_export_status':
        ems_admin_api_ok([
            'items' => ems_admin_api_fetch_report_export_status($conn, $officerUserId),
        ]);
        break;

    case 'provider_review':
        $approvalRequestId = ems_admin_api_int($_POST, 'approval_request_id', 0);
        $decision = ems_admin_api_text($_POST, 'decision', '');
        $note = ems_admin_api_text($_POST, 'review_note', '');

        $review = ems_admin_provider_review_application($conn, $approvalRequestId, $officerUserId, $decision, $note);
        if (empty($review['ok'])) {
            ems_admin_api_fail('VALIDATION_ERROR', (string)($review['message'] ?? 'Unable to update provider review.'), 422);
        }

        ems_admin_api_ok([
            'message' => (string)($review['message'] ?? 'Provider review updated.'),
            'status' => (string)($review['status'] ?? ''),
            'status_label' => (string)($review['status_label'] ?? ''),
            'status_class' => (string)($review['status_class'] ?? ''),
        ]);
        break;

    case 'course_review':
        $courseId = ems_admin_api_int($_POST, 'course_id', 0);
        $decision = ems_admin_api_text($_POST, 'decision', '');
        $note = ems_admin_api_text($_POST, 'review_note', '');

        $review = ems_admin_course_review($conn, $courseId, $officerUserId, $decision, $note);
        if (empty($review['ok'])) {
            ems_admin_api_fail('VALIDATION_ERROR', (string)($review['message'] ?? 'Unable to update course review.'), 422);
        }

        ems_admin_api_ok([
            'message' => (string)($review['message'] ?? 'Course status updated.'),
            'status' => (string)($review['status'] ?? ''),
        ]);
        break;

    case 'user_status_update':
        $userId = ems_admin_api_int($_POST, 'user_id', 0);
        $status = ems_admin_api_text($_POST, 'status', '');

        $update = ems_admin_user_update_status($conn, $userId, $officerUserId, $status);
        if (empty($update['ok'])) {
            ems_admin_api_fail('VALIDATION_ERROR', (string)($update['message'] ?? 'Unable to update user status.'), 422);
        }

        ems_admin_api_ok([
            'message' => (string)($update['message'] ?? 'User status updated.'),
        ]);
        break;

    case 'settings_update':
        $save = ems_admin_save_platform_settings($conn, $officerUserId, [
            'platform_name' => ems_admin_api_text($_POST, 'platform_name', ''),
            'platform_email' => ems_admin_api_text($_POST, 'platform_email', ''),
            'support_phone' => ems_admin_api_text($_POST, 'support_phone', ''),
            'platform_commission' => ems_admin_api_text($_POST, 'platform_commission', ''),
            'minimum_payout_amount' => ems_admin_api_text($_POST, 'minimum_payout_amount', ''),
            'auto_approve_verified_instructors' => ems_admin_api_bool($_POST, 'auto_approve_verified_instructors'),
            'require_content_review' => ems_admin_api_bool($_POST, 'require_content_review'),
        ]);

        if (empty($save['ok'])) {
            ems_admin_api_fail('VALIDATION_ERROR', (string)($save['message'] ?? 'Unable to update settings.'), 422, [
                'errors' => (array)($save['errors'] ?? []),
            ]);
        }

        ems_admin_api_ok([
            'message' => (string)($save['message'] ?? 'Settings updated.'),
            'settings' => ems_admin_fetch_platform_settings($conn),
        ]);
        break;

    case 'profile_update':
        $profileUpdate = ems_admin_update_officer_profile($conn, $officerUserId, [
            'full_name' => ems_admin_api_text($_POST, 'full_name', ''),
            'email' => ems_admin_api_text($_POST, 'email', ''),
            'designation' => ems_admin_api_text($_POST, 'designation', ''),
            'phone' => ems_admin_api_text($_POST, 'phone', ''),
            'employee_id' => ems_admin_api_text($_POST, 'employee_id', ''),
            'department' => ems_admin_api_text($_POST, 'department', ''),
            'location' => ems_admin_api_text($_POST, 'location', ''),
            'timezone' => ems_admin_api_text($_POST, 'timezone', ''),
            'language' => ems_admin_api_text($_POST, 'language', ''),
            'responsibilities' => ems_admin_api_text($_POST, 'responsibilities', ''),
        ]);

        if (empty($profileUpdate['ok'])) {
            ems_admin_api_fail('VALIDATION_ERROR', (string)($profileUpdate['message'] ?? 'Unable to update profile.'), 422, [
                'errors' => (array)($profileUpdate['errors'] ?? []),
            ]);
        }

        $prefsUpdate = ems_admin_update_officer_preferences($conn, $officerUserId, [
            'pref_email_alerts' => ems_admin_api_bool($_POST, 'pref_email_alerts'),
            'pref_daily_digest' => ems_admin_api_bool($_POST, 'pref_daily_digest'),
            'pref_auto_archive' => ems_admin_api_bool($_POST, 'pref_auto_archive'),
            'two_factor_enabled' => ems_admin_api_bool($_POST, 'two_factor_enabled'),
        ]);

        if (empty($prefsUpdate['ok'])) {
            ems_admin_api_fail('SERVER_ERROR', (string)($prefsUpdate['message'] ?? 'Profile saved but preferences update failed.'), 500);
        }

        ems_admin_api_ok([
            'message' => 'Profile and preferences updated successfully.',
            'profile' => ems_admin_fetch_officer_profile($conn, $officerUserId, $portalUser),
        ]);
        break;

    case 'notification_mark_read':
        $notificationId = ems_admin_api_int($_POST, 'notification_id', 0);
        if ($notificationId <= 0) {
            ems_admin_api_fail('VALIDATION_ERROR', 'Notification id is required.', 422);
        }

        if (!ems_admin_mark_notification_read($conn, $officerUserId, $notificationId)) {
            ems_admin_api_fail('SERVER_ERROR', 'Unable to mark notification as read.', 500);
        }

        ems_admin_api_ok([
            'unread_count' => ems_admin_count_unread_notifications($conn, $officerUserId),
        ]);
        break;

    case 'report_export_request':
        $reportType = ems_admin_api_text($_POST, 'report_type', 'monthly-performance');
        $result = ems_admin_api_create_report_export_job($conn, $officerUserId, $reportType);
        if (empty($result['ok'])) {
            ems_admin_api_fail('SERVER_ERROR', (string)($result['message'] ?? 'Unable to create report export request.'), 500);
        }

        ems_admin_api_ok([
            'message' => 'Report export prepared successfully.',
            'download_url' => BASE_URL . 'admin-officer/api.php?action=report_export_download&type=' . rawurlencode($reportType),
            'report_type' => $reportType,
        ]);
        break;

    case 'report_export_download':
        $reportType = ems_admin_api_text($_GET, 'type', 'monthly-performance');
        $payload = ems_admin_report_export_payload($conn, $reportType);
        ems_admin_api_stream_csv((string)($payload['filename'] ?? 'admin-report.csv'), (array)($payload['headers'] ?? []), (array)($payload['rows'] ?? []));
        break;

    default:
        ems_admin_api_fail('BAD_REQUEST', 'Unsupported action.', 400);
}

function ems_admin_api_require_post()
{
    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
        ems_admin_api_fail('METHOD_NOT_ALLOWED', 'POST method is required.', 405);
    }
}

function ems_admin_api_verify_csrf()
{
    $token = (string)($_POST['csrf_token'] ?? '');
    if (!ems_verify_csrf_token($token)) {
        ems_admin_api_fail('CSRF_FAILED', 'Security token is invalid or expired.', 403);
    }
}

function ems_admin_api_ok(array $data = [], array $meta = [])
{
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => true,
        'data' => $data,
        'meta' => $meta,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function ems_admin_api_fail($code, $message, $status = 400, array $extra = [])
{
    http_response_code((int)$status);
    header('Content-Type: application/json; charset=utf-8');

    $payload = [
        'ok' => false,
        'code' => (string)$code,
        'message' => (string)$message,
    ];

    foreach ($extra as $key => $value) {
        $payload[$key] = $value;
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function ems_admin_api_validation_fail(array $errors)
{
    ems_admin_api_fail('VALIDATION_ERROR', 'Please correct the highlighted fields.', 422, ['errors' => $errors]);
}

function ems_admin_api_int(array $source, $key, $default = 0)
{
    if (!isset($source[$key])) {
        return (int)$default;
    }
    return (int)$source[$key];
}

function ems_admin_api_text(array $source, $key, $default = '')
{
    if (!isset($source[$key])) {
        return (string)$default;
    }
    return trim((string)$source[$key]);
}

function ems_admin_api_bool(array $source, $key)
{
    return !empty($source[$key]);
}

function ems_admin_api_fetch_report_export_status($conn, $officerUserId)
{
    if (!ems_admin_table_exists($conn, 'report_exports')) {
        return [];
    }

    $rows = ems_admin_fetch_rows_prepared(
        $conn,
        'SELECT id, report_type, file_name, status, created_at, completed_at
         FROM report_exports
         WHERE requested_by_user_id = ?
         ORDER BY created_at DESC
         LIMIT 20',
        'i',
        [(int)$officerUserId]
    );

    foreach ($rows as &$row) {
        $row['download_url'] = ($row['status'] ?? '') === 'ready'
            ? BASE_URL . 'admin-officer/api.php?action=report_export_download&type=' . rawurlencode((string)($row['report_type'] ?? 'monthly-performance'))
            : '';
    }
    unset($row);

    return $rows;
}

function ems_admin_api_create_report_export_job($conn, $officerUserId, $reportType)
{
    if (!ems_admin_table_exists($conn, 'report_exports')) {
        return ['ok' => true, 'message' => 'Report export generated on demand.'];
    }

    $payload = ems_admin_report_export_payload($conn, $reportType);
    $filename = (string)($payload['filename'] ?? ('report-' . date('Ymd_His') . '.csv'));

    $result = ems_admin_exec_prepared_row(
        $conn,
        'INSERT INTO report_exports (report_type, file_name, status, requested_by_user_id, created_at, completed_at)
         VALUES (?, ?, "ready", ?, NOW(), NOW())',
        'ssi',
        [(string)$reportType, $filename, (int)$officerUserId]
    );

    if (empty($result['ok'])) {
        return ['ok' => false, 'message' => 'Unable to save report export status.'];
    }

    ems_admin_log_activity($conn, (int)$officerUserId, 'report_export_request', 'report_export', (int)($result['insert_id'] ?? 0), [
        'report_type' => (string)$reportType,
    ]);

    return ['ok' => true, 'message' => 'Report export request created.'];
}

function ems_admin_api_stream_csv($filename, array $headers, array $rows)
{
    $safeFilename = trim((string)$filename) !== '' ? trim((string)$filename) : 'report.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . str_replace('"', '', $safeFilename) . '"');

    $output = fopen('php://output', 'w');
    if ($output === false) {
        exit;
    }

    if (!empty($headers)) {
        fputcsv($output, $headers);
    }

    foreach ($rows as $row) {
        if (is_array($row)) {
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit;
}

