<?php
/**
 * Database Connection Configuration
 * EduSkill Marketplace System (EMS)
 * 
 * This file establishes a reusable MySQLi connection to the database.
 * No queries should be executed here - connection only.
 */

// Database credentials for XAMPP localhost
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'eduskill_marketplace');

// Create connection using MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection status
if ($conn->connect_error) {
    // In production, do not display error details to user
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8mb4");

// Connection is ready to use in other files
// This connection object ($conn) should be included in pages that need database access
?>
