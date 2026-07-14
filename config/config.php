<?php
/**
 * Application Configuration
 * EduSkill Marketplace System (EMS)
 * 
 * Global constants and settings for the application
 */

// Dynamic Base URL for the application (production)
define("BASE_URL", "http://eduskill.kesug.com/");

// Application name
define("APP_NAME", "EduSkill Marketplace System");

// Application version
define("APP_VERSION", "1.2.4");

// Database information (matching db.php)
define("DB_NAME_CONFIG", "ifo_42408021_eduskill_database");

// File upload directories
define("UPLOAD_DIR", __DIR__ . "/../uploads/");
define("UPLOAD_REPORTS_DIR", __DIR__ . "/../reports/");

// Maximum file upload size (5MB)
define("MAX_UPLOAD_SIZE", 5242880);

// Allowed file extensions for uploads
define("ALLOWED_EXTENSIONS", ["pdf", "doc", "docx", "jpg", "jpeg", "png", "gif"]);

// Timezone
define("TIMEZONE", "Asia/Kolkata");

// Enable or disable debug mode (set to false in production)
define("DEBUG_MODE", false);

// Default items per page for pagination
define("ITEMS_PER_PAGE", 10);

// Set timezone for PHP
date_default_timezone_set(TIMEZONE);

?>
