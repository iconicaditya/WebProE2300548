<?php
// Usage: php scripts/create_admin.php [email] [full_name]
// Generates a secure password, inserts or updates an admin user, and prints credentials.

require __DIR__ . '/../config/db.php';

$email = $argv[1] ?? 'admin@example.com';
$fullName = $argv[2] ?? 'System Administrator';

// Generate a 12-character hex password (6 bytes -> 12 hex chars)
$password = bin2hex(random_bytes(6));
$hash = password_hash($password, PASSWORD_DEFAULT);

// Check if a user with this email exists
$stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $userId = $row['id'];
    $up = $conn->prepare('UPDATE users SET full_name = ?, password_hash = ?, role = ?, status = ? WHERE id = ? LIMIT 1');
    $role = 'officer';
    $status = 'active';
    $up->bind_param('ssssi', $fullName, $hash, $role, $status, $userId);
    $ok = $up->execute();
    if ($ok) {
        echo "Updated existing admin ($email)\n";
        echo "Password: $password\n";
        exit(0);
    }
    echo "Failed to update admin user\n";
    exit(2);
} else {
    $ins = $conn->prepare('INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)');
    $role = 'admin';
    $status = 'active';
    $ins->bind_param('sssss', $fullName, $email, $hash, $role, $status);
    $ok = $ins->execute();
    if ($ok) {
        echo "Created admin user: $email\n";
        echo "Password: $password\n";
        exit(0);
    }
    echo "Failed to create admin user\n";
    exit(2);
}
