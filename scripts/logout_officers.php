<?php
// Logout all sessions where the user role is 'officer'
// Usage: php scripts/logout_officers.php

// Determine session save path
$path = ini_get('session.save_path');
if (!$path) {
    $path = sys_get_temp_dir();
}

if (!is_dir($path)) {
    echo "Session save path not found: $path\n";
    exit(1);
}

$files = glob(rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'sess_*');
if (!$files) {
    echo "No session files found in $path\n";
    exit(0);
}

$destroyed = 0;
foreach ($files as $file) {
    $sid = substr(basename($file), 5);
    if (!$sid) continue;

    // Use a fresh session environment per id
    session_write_close();
    session_id($sid);
    @session_start();

    $role = $_SESSION['auth_user']['role'] ?? null;
    if ($role === 'officer' || $role === 'admin') {
        // destroy this session
        @session_destroy();
        // remove file if still exists
        if (file_exists($file)) {
            @unlink($file);
        }
        echo "Destroyed session: $sid (role=$role)\n";
        $destroyed++;
    } else {
        // close and continue
        @session_write_close();
    }
}

echo "Total sessions destroyed: $destroyed\n";
