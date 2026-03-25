<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../includes/auth.php');

if (ems_is_logged_in()) {
    ems_logout_user();
}

ems_set_flash('success', 'You have been logged out successfully.');
ems_redirect('auth/login.php');

