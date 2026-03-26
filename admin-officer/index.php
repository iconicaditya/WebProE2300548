<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/includes/admin_data.php');

ems_require_login(['officer']);

$portalUser = ems_load_portal_user($conn);
if (!$portalUser || ($portalUser['role'] ?? '') !== 'officer') {
    ems_logout_user();
    ems_set_flash('danger', 'Unable to load your admin profile. Please log in again.');
    ems_redirect('auth/login.php');
}

$officerDisplayName = ems_profile_text($portalUser['full_name'], 'Admin Officer');
$officerInitials = ems_user_initials($officerDisplayName);

$pageTitle = 'Admin Officer Dashboard';
$bodyClass = 'admin-dashboard';
$skipSandhyaCss = true;
$pageStylesheet = 'aakroshan.css';
$assetVersion = 'admin.' . time();

$page = isset($_GET['page']) ? strtolower(trim((string)$_GET['page'])) : 'dashboard';
$page = preg_replace('/[^a-z0-9\-]/', '', $page);
$valid_pages = ['dashboard', 'profile', 'providermanagement', 'learnermanagement', 'analytic-reports', 'settings', 'courses', 'reports', 'users'];

$extraStylesheets = [];

if ($page === 'profile') {
    $extraStylesheets[] = 'profile-aakroshan.css';
}

if ($page === 'providermanagement') {
    $extraStylesheets[] = 'providermanagement-aakroshan.css';
}

if (!in_array($page, $valid_pages)) {
    $page = 'dashboard';
}

$adminNotifications = function_exists('ems_admin_fetch_notifications')
    ? ems_admin_fetch_notifications($conn, (int)($portalUser['id'] ?? 0), 5)
    : [];
$adminUnreadNotifications = function_exists('ems_admin_count_unread_notifications')
    ? ems_admin_count_unread_notifications($conn, (int)($portalUser['id'] ?? 0))
    : 0;

require_once(__DIR__ . '/../includes/header.php');
?>

<nav class="provider-navbar">
    <div class="provider-navbar-container">
        <div class="provider-navbar-brand">
            <a href="<?php echo BASE_URL; ?>" class="provider-navbar-logo">
                <img src="<?php echo BASE_URL; ?>assets/images/logo-eduskill.png" alt="EduSkill" class="provider-navbar-logo-img">
            </a>
        </div>

        <div class="provider-navbar-actions">
            <div class="provider-navbar-search">
                <input type="text" placeholder="Search providers, learners, reports..." class="provider-search-input">
                <span class="provider-search-icon">🔍</span>
            </div>

            <div class="provider-navbar-item notifications-dropdown">
                <button class="provider-navbar-btn notifications-btn" title="Alerts" aria-label="Alerts">
                    <span class="notification-icon">🔔</span>
                    <span class="notification-badge"><?php echo (int)$adminUnreadNotifications; ?></span>
                </button>
                <div class="notifications-menu">
                    <div class="notifications-header">
                        <h4>System Alerts</h4>
                        <a href="<?php echo BASE_URL; ?>admin-officer/?page=reports" class="mark-all-read">View reports</a>
                    </div>
                    <ul class="notifications-list">
                        <?php if (empty($adminNotifications)): ?>
                        <li class="notification-item">
                            <span class="notification-avatar">ℹ️</span>
                            <div class="notification-content">
                                <p class="notification-text">No recent notifications.</p>
                                <span class="notification-time">Just now</span>
                            </div>
                        </li>
                        <?php else: ?>
                            <?php foreach ($adminNotifications as $notification): ?>
                            <li class="notification-item<?php echo !empty($notification['is_read']) ? '' : ' unread'; ?>">
                                <span class="notification-avatar"><?php echo !empty($notification['is_read']) ? '✅' : '⚠️'; ?></span>
                                <div class="notification-content">
                                    <p class="notification-text"><?php echo ems_e((string)($notification['title'] ?? 'Notification')); ?></p>
                                    <span class="notification-time"><?php echo ems_e((string)($notification['time_ago'] ?? 'Just now')); ?></span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="notifications-footer">
                        <a href="<?php echo BASE_URL; ?>admin-officer/?page=reports" class="view-all-notifications">View all alerts</a>
                    </div>
                </div>
            </div>

            <div class="provider-navbar-item">
                <a href="<?php echo BASE_URL; ?>admin-officer/?page=profile" class="provider-navbar-btn profile-btn profile-static-link" title="My Profile" aria-label="My Profile">
                    <span class="profile-avatar"><?php echo ems_e($officerInitials); ?></span>
                    <span class="profile-name"><?php echo ems_e($officerDisplayName); ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>


<div class="provider-layout">
    <?php include(__DIR__ . '/includes/sidebar.php'); ?>
    <div class="provider-main-content">
        <?php include(__DIR__ . '/../includes/flash.php'); ?>
        <?php
        $page_file = __DIR__ . '/pages/' . $page . '.php';
        if (file_exists($page_file)) {
            include($page_file);
        } else {
            include(__DIR__ . '/pages/dashboard.php');
        }
        ?>
    </div>
</div>

<footer class="provider-dashboard-footer">
    <p class="footer-text">© 2026 EduSkill Marketplace. All rights reserved.</p>
</footer>

<script src="<?php echo BASE_URL; ?>assets/js/main.js?v=<?php echo $assetVersion; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/aakroshan.js?v=<?php echo $assetVersion; ?>"></script>
<?php if ($page === 'profile'): ?>
<script src="<?php echo BASE_URL; ?>assets/js/profile-aakroshan.js?v=<?php echo $assetVersion; ?>"></script>
<?php endif; ?>
<?php if ($page === 'providermanagement'): ?>
<script src="<?php echo BASE_URL; ?>assets/js/providermanagement-aakroshan.js?v=<?php echo $assetVersion; ?>"></script>
<?php endif; ?>
</body>
</html>
