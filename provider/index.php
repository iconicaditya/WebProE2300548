<?php
/**
 * Provider Dashboard Router
 * 
 * Main entry point for the provider dashboard system.
 * Routes to different pages based on URL query parameters.
 * Contains navbar and footer layout.
 */

$pageTitle = 'Provider Dashboard';
$assetVersion = 'provider.' . time();

// Get page from query parameter (default to dashboard)
$page = isset($_GET['page']) ? sanitize_input($_GET['page']) : 'dashboard';

// List of valid pages
$valid_pages = ['dashboard', 'courses', 'students', 'analytics', 'reviews', 'payments', 'profile', 'settings', 'certificates'];

// Redirect to dashboard if page is invalid
if (!in_array($page, $valid_pages)) {
    $page = 'dashboard';
}

// Include header (contains meta tags, CSS)
require_once(__DIR__ . '/../includes/header.php');
require_once(__DIR__ . '/includes/topbar.php');

?>

<!-- DASHBOARD WRAPPER -->
<div class="provider-dashboard-wrapper">
    <!-- Include Sidebar -->
    <?php require_once(__DIR__ . '/includes/sidebar.php'); ?>

    <!-- Load Page Content -->
    <?php
    $page_file = __DIR__ . '/pages/' . $page . '.php';
    if (file_exists($page_file)) {
        require_once($page_file);
    } else {
        require_once(__DIR__ . '/pages/dashboard.php');
    }
    ?>
</div>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>

<?php
// Close HTML from header.php
?>
</div>

<!-- Load JavaScript Files -->
<script src="<?php echo BASE_URL; ?>assets/js/main.js?v=<?php echo APP_VERSION ?? '1.0'; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/aaditya.js?v=<?php echo APP_VERSION ?? '1.0'; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/sandhya.js?v=<?php echo APP_VERSION ?? '1.0'; ?>&m=<?php echo @filemtime(__DIR__ . '/../assets/js/sandhya.js'); ?>"></script>
</body>
</html>

<?php
/**
 * Helper function to sanitize user input
 */
function sanitize_input($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}
?>
