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
        return 'officer/index.php';
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
