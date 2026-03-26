<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/includes/learner_data.php');

ems_require_login(['learner']);

$portalUser = ems_load_portal_user($conn);
if (!$portalUser || ($portalUser['role'] ?? '') !== 'learner') {
    ems_learner_api_fail('FORBIDDEN', 'Learner account is required.', 403);
}

$learnerUserId = (int)($portalUser['id'] ?? 0);
if ($learnerUserId <= 0) {
    ems_learner_api_fail('FORBIDDEN', 'Invalid learner account.', 403);
}

$action = trim((string)($_REQUEST['action'] ?? ''));
if ($action === '') {
    ems_learner_api_fail('BAD_REQUEST', 'Missing action.', 400);
}

$writeActions = [
    'wishlist_toggle',
    'cart_add',
    'cart_remove',
    'checkout',
    'progress_update',
    'quiz_submit',
    'notification_mark_read',
    'notification_mark_all_read',
    'message_mark_read',
    'message_send',
    'settings_update',
    'password_change',
];

if (in_array($action, $writeActions, true)) {
    ems_learner_api_require_post();
    ems_learner_api_verify_csrf();
}

switch ($action) {
    case 'summary':
        ems_learner_api_ok([
            'ui' => ems_learner_fetch_profile_card_data($conn, $learnerUserId),
            'metrics' => ems_learner_fetch_overview_metrics($conn, $learnerUserId),
        ]);
        break;

    case 'notifications_list':
        ems_learner_api_ok([
            'items' => ems_learner_fetch_recent_notifications($conn, $learnerUserId, 30),
            'unread_count' => ems_learner_count_unread_notifications($conn, $learnerUserId),
        ]);
        break;

    case 'messages_list':
        ems_learner_api_ok([
            'items' => ems_learner_fetch_messages($conn, $learnerUserId, 50),
            'unread_count' => ems_learner_count_unread_messages($conn, $learnerUserId),
        ]);
        break;

    case 'wishlist_toggle':
        $courseId = (int)($_POST['course_id'] ?? 0);
        if ($courseId <= 0) {
            ems_learner_api_fail('VALIDATION_ERROR', 'Course id is required.', 422);
        }

        $toggle = ems_learner_toggle_wishlist($conn, $learnerUserId, $courseId);
        if (empty($toggle['ok'])) {
            ems_learner_api_fail('VALIDATION_ERROR', (string)($toggle['message'] ?? 'Unable to update wishlist.'), 422);
        }

        ems_learner_api_ok([
            'state' => $toggle['state'] ?? 'added',
            'wishlist_count' => ems_learner_count_wishlist_items($conn, $learnerUserId),
        ]);
        break;

    case 'cart_add':
        $courseId = (int)($_POST['course_id'] ?? 0);
        if ($courseId <= 0) {
            ems_learner_api_fail('VALIDATION_ERROR', 'Course id is required.', 422);
        }

        $add = ems_learner_add_to_cart($conn, $learnerUserId, $courseId);
        if (empty($add['ok'])) {
            ems_learner_api_fail('VALIDATION_ERROR', (string)($add['message'] ?? 'Unable to add course to cart.'), 422);
        }

        ems_learner_api_ok([
            'message' => (string)($add['message'] ?? 'Added to cart.'),
            'cart_count' => ems_learner_count_cart_items($conn, $learnerUserId),
        ]);
        break;

    case 'cart_remove':
        $courseId = (int)($_POST['course_id'] ?? 0);
        if ($courseId <= 0) {
            ems_learner_api_fail('VALIDATION_ERROR', 'Course id is required.', 422);
        }

        $remove = ems_learner_remove_from_cart($conn, $learnerUserId, $courseId);
        if (empty($remove['ok'])) {
            ems_learner_api_fail('SERVER_ERROR', 'Unable to remove cart item.', 500);
        }

        ems_learner_api_ok([
            'removed' => !empty($remove['removed']),
            'cart_count' => ems_learner_count_cart_items($conn, $learnerUserId),
        ]);
        break;

    case 'checkout':
        $method = trim((string)($_POST['payment_method'] ?? 'card'));
        $singleCourseId = (int)($_POST['course_id'] ?? 0);

        $checkout = ems_learner_checkout($conn, $learnerUserId, $method, $singleCourseId);
        if (empty($checkout['ok'])) {
            ems_learner_api_fail('VALIDATION_ERROR', (string)($checkout['message'] ?? 'Checkout failed.'), 422);
        }

        ems_learner_api_ok($checkout);
        break;

    case 'progress_update':
        $courseId = (int)($_POST['course_id'] ?? 0);
        $lessonId = (int)($_POST['lesson_id'] ?? 0);
        $progressPercent = (float)($_POST['progress_percent'] ?? 0);
        $minutesSpent = (int)($_POST['minutes_spent'] ?? 0);
        $lastPosition = (int)($_POST['last_position_seconds'] ?? 0);
        $isCompleted = !empty($_POST['is_completed']);

        $progress = ems_learner_update_lesson_progress(
            $conn,
            $learnerUserId,
            $courseId,
            $lessonId,
            $progressPercent,
            $minutesSpent,
            $lastPosition,
            $isCompleted
        );

        if (empty($progress['ok'])) {
            ems_learner_api_fail('VALIDATION_ERROR', (string)($progress['message'] ?? 'Unable to update lesson progress.'), 422);
        }

        ems_learner_api_ok($progress);
        break;

    case 'quiz_submit':
        $courseId = (int)($_POST['course_id'] ?? 0);
        $lessonId = (int)($_POST['lesson_id'] ?? 0);
        $scorePercent = (float)($_POST['score_percent'] ?? 0);

        $answers = $_POST['answers'] ?? '[]';
        if (is_string($answers)) {
            $decoded = json_decode($answers, true);
            $answers = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($answers)) {
            $answers = [];
        }

        $quiz = ems_learner_submit_quiz_attempt($conn, $learnerUserId, $courseId, $lessonId, $scorePercent, $answers);
        if (empty($quiz['ok'])) {
            ems_learner_api_fail('VALIDATION_ERROR', (string)($quiz['message'] ?? 'Unable to submit quiz attempt.'), 422);
        }

        ems_learner_api_ok($quiz);
        break;

    case 'notification_mark_read':
        $notificationId = (int)($_POST['notification_id'] ?? 0);
        if ($notificationId <= 0) {
            ems_learner_api_fail('VALIDATION_ERROR', 'Notification id is required.', 422);
        }

        if (!ems_learner_mark_notification_read($conn, $learnerUserId, $notificationId)) {
            ems_learner_api_fail('SERVER_ERROR', 'Unable to mark notification as read.', 500);
        }

        ems_learner_api_ok([
            'unread_count' => ems_learner_count_unread_notifications($conn, $learnerUserId),
        ]);
        break;

    case 'notification_mark_all_read':
        if (!ems_learner_mark_all_notifications_read($conn, $learnerUserId)) {
            ems_learner_api_fail('SERVER_ERROR', 'Unable to mark notifications as read.', 500);
        }

        ems_learner_api_ok([
            'unread_count' => ems_learner_count_unread_notifications($conn, $learnerUserId),
        ]);
        break;

    case 'message_mark_read':
        $messageId = (int)($_POST['message_id'] ?? 0);
        if ($messageId <= 0) {
            ems_learner_api_fail('VALIDATION_ERROR', 'Message id is required.', 422);
        }

        if (!ems_learner_mark_message_read($conn, $learnerUserId, $messageId)) {
            ems_learner_api_fail('SERVER_ERROR', 'Unable to mark message as read.', 500);
        }

        ems_learner_api_ok([
            'unread_count' => ems_learner_count_unread_messages($conn, $learnerUserId),
        ]);
        break;

    case 'message_send':
        $providerUserId = (int)($_POST['provider_user_id'] ?? 0);
        $courseId = (int)($_POST['course_id'] ?? 0);
        $subject = trim((string)($_POST['subject'] ?? ''));
        $messageText = trim((string)($_POST['message_text'] ?? ''));

        $send = ems_learner_send_message($conn, $learnerUserId, $providerUserId, $courseId, $subject, $messageText);
        if (empty($send['ok'])) {
            ems_learner_api_fail('VALIDATION_ERROR', (string)($send['message'] ?? 'Unable to send message.'), 422);
        }

        ems_learner_api_ok([
            'message_id' => (int)($send['message_id'] ?? 0),
        ]);
        break;

    case 'settings_update':
        $payload = [
            'language_code' => trim((string)($_POST['language_code'] ?? 'en')),
            'timezone' => trim((string)($_POST['timezone'] ?? 'Asia/Kolkata')),
            'notification_email_enabled' => !empty($_POST['notification_email_enabled']),
            'theme_preference' => trim((string)($_POST['theme_preference'] ?? 'light')),
            'two_factor_enabled' => !empty($_POST['two_factor_enabled']),
        ];

        $settings = ems_learner_update_settings($conn, $learnerUserId, $payload);
        if (empty($settings['ok'])) {
            ems_learner_api_fail('VALIDATION_ERROR', (string)($settings['message'] ?? 'Unable to update settings.'), 422);
        }

        ems_learner_api_ok([
            'settings' => ems_learner_fetch_settings($conn, $learnerUserId),
        ]);
        break;

    case 'password_change':
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword = (string)($_POST['new_password'] ?? '');
        $confirmPassword = (string)($_POST['confirm_password'] ?? '');

        $change = ems_learner_change_password($conn, $learnerUserId, $currentPassword, $newPassword, $confirmPassword);
        if (empty($change['ok'])) {
            ems_learner_api_fail('VALIDATION_ERROR', (string)($change['message'] ?? 'Unable to change password.'), 422);
        }

        ems_learner_api_ok([
            'message' => (string)($change['message'] ?? 'Password updated.'),
        ]);
        break;

    default:
        ems_learner_api_fail('BAD_REQUEST', 'Unsupported action.', 400);
}

function ems_learner_api_require_post()
{
    if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'POST') {
        ems_learner_api_fail('METHOD_NOT_ALLOWED', 'POST method is required.', 405);
    }
}

function ems_learner_api_verify_csrf()
{
    $token = (string)($_POST['csrf_token'] ?? '');
    if (!ems_verify_csrf_token($token)) {
        ems_learner_api_fail('CSRF_FAILED', 'Security token is invalid or expired.', 403);
    }
}

function ems_learner_api_ok(array $data = [])
{
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'ok' => true,
        'data' => $data,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function ems_learner_api_fail($code, $message, $status = 400, array $extra = [])
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

