<?php
/**
 * Authentication and Session Helpers
 * EduSkill Marketplace System (EMS)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function ems_e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function ems_redirect($path)
{
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

function ems_set_flash($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function ems_get_flash()
{
    if (empty($_SESSION['flash_message'])) {
        return null;
    }

    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);

    return $flash;
}

function ems_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function ems_verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function ems_login_user(array $user)
{
    session_regenerate_id(true);

    $_SESSION['auth_user'] = [
        'id' => (int)$user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

function ems_logout_user()
{
    unset($_SESSION['auth_user']);
    session_regenerate_id(true);
}

function ems_is_logged_in()
{
    return !empty($_SESSION['auth_user']);
}

function ems_current_user()
{
    return $_SESSION['auth_user'] ?? null;
}

function ems_current_role()
{
    return $_SESSION['auth_user']['role'] ?? null;
}

function ems_dashboard_path_for_role($role)
{
    if ($role === 'provider') {
        return 'provider/index.php';
    }

    if ($role === 'officer') {
        return 'admin-officer/index.php';
    }

    return 'learner/index.php';
}

function ems_require_login(array $allowedRoles = [])
{
    if (!ems_is_logged_in()) {
        ems_set_flash('warning', 'Please log in to continue.');
        ems_redirect('auth/login.php');
    }

    if (!empty($allowedRoles) && !in_array(ems_current_role(), $allowedRoles, true)) {
        ems_set_flash('danger', 'You are not authorized to access that page.');
        ems_redirect(ems_dashboard_path_for_role(ems_current_role()));
    }
}

function ems_user_initials($fullName)
{
    $name = trim((string)$fullName);
    if ($name === '') {
        return 'U';
    }

    $parts = preg_split('/\s+/', $name) ?: [];
    $firstPart = $parts[0] ?? '';
    $lastPart = $parts[count($parts) - 1] ?? '';

    $takeCharacter = static function ($text) {
        $text = trim((string)$text);
        if ($text === '') {
            return '';
        }

        if (function_exists('mb_substr')) {
            return mb_substr($text, 0, 1, 'UTF-8');
        }

        return substr($text, 0, 1);
    };

    $initials = strtoupper((string)$takeCharacter($firstPart));
    if ($lastPart !== '' && strtolower($lastPart) !== strtolower($firstPart)) {
        $initials .= strtoupper((string)$takeCharacter($lastPart));
    }

    return $initials !== '' ? $initials : 'U';
}

function ems_profile_text($value, $fallback = 'Not provided')
{
    $text = trim((string)$value);
    return $text !== '' ? $text : $fallback;
}

function ems_load_portal_user($conn)
{
    $authUser = ems_current_user();
    if (empty($authUser['id'])) {
        return null;
    }

    $userId = (int)$authUser['id'];
    if ($userId <= 0) {
        return null;
    }

    $baseStmt = $conn->prepare('SELECT id, full_name, email, role, status, created_at FROM users WHERE id = ? LIMIT 1');
    if (!$baseStmt) {
        return null;
    }

    $baseStmt->bind_param('i', $userId);
    $baseStmt->execute();
    $baseResult = $baseStmt->get_result();
    $baseUser = $baseResult ? $baseResult->fetch_assoc() : null;
    $baseStmt->close();

    if (!$baseUser || ($baseUser['status'] ?? 'inactive') !== 'active') {
        return null;
    }

    $_SESSION['auth_user'] = [
        'id' => (int)$baseUser['id'],
        'full_name' => $baseUser['full_name'],
        'email' => $baseUser['email'],
        'role' => $baseUser['role'],
    ];

    $profile = [
        'id' => (int)$baseUser['id'],
        'role' => (string)$baseUser['role'],
        'status' => (string)$baseUser['status'],
        'created_at' => $baseUser['created_at'] ?? null,
        'full_name' => (string)$baseUser['full_name'],
        'email' => (string)$baseUser['email'],
        'mobile_number' => '',
        'profile_photo_url' => '',
        'current_role' => '',
        'learning_interest' => '',
        'experience_level' => '',
        'learning_goal' => '',
        'professional_title' => '',
        'skill_category' => '',
        'teaching_experience' => '',
        'short_bio' => '',
    ];

    if ($profile['role'] === 'learner') {
        $learnerStmt = $conn->prepare('SELECT `current_role`, mobile_number, learning_interest, experience_level, learning_goal, profile_photo_url FROM learner_profiles WHERE user_id = ? LIMIT 1');
        if ($learnerStmt) {
            $learnerStmt->bind_param('i', $userId);
            $learnerStmt->execute();
            $learnerResult = $learnerStmt->get_result();
            $learnerData = $learnerResult ? $learnerResult->fetch_assoc() : null;
            $learnerStmt->close();

            if ($learnerData) {
                $profile = array_merge($profile, $learnerData);
            }
        }
    }

    if ($profile['role'] === 'provider') {
        $providerStmt = $conn->prepare('SELECT professional_title, mobile_number, skill_category, teaching_experience, short_bio, profile_photo_url FROM provider_profiles WHERE user_id = ? LIMIT 1');
        if ($providerStmt) {
            $providerStmt->bind_param('i', $userId);
            $providerStmt->execute();
            $providerResult = $providerStmt->get_result();
            $providerData = $providerResult ? $providerResult->fetch_assoc() : null;
            $providerStmt->close();

            if ($providerData) {
                $profile = array_merge($profile, $providerData);
            }
        }
    }

    return $profile;
}
