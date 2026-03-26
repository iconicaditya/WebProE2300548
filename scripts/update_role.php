<?php
require __DIR__ . '/../config/db.php';
$email = 'admin@example.com';
$role = 'officer';
$stmt = $conn->prepare('UPDATE users SET role = ? WHERE email = ? LIMIT 1');
$stmt->bind_param('ss', $role, $email);
$stmt->execute();
echo "affected:" . $stmt->affected_rows . PHP_EOL;
