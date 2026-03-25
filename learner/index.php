<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');

ems_require_login(['learner']);

$portalUser = ems_load_portal_user($conn);
if (!$portalUser || ($portalUser['role'] ?? '') !== 'learner') {
    ems_logout_user();
    ems_set_flash('danger', 'Unable to load your learner profile. Please log in again.');
    ems_redirect('auth/login.php');
}

$learnerDisplayName = ems_profile_text($portalUser['full_name'], 'Learner');
$learnerInitials = ems_user_initials($learnerDisplayName);

$pageTitle = 'Learner Dashboard';
$bodyClass = 'learner-dashboard';
$skipSandhyaCss = true;
$pageStylesheet = 'learner-aaditya.css';
$assetVersion = 'learner.' . time();

$page = isset($_GET['page']) ? sanitize_input($_GET['page']) : 'dashboard';
$valid_pages = ['dashboard', 'courses', 'progress', 'certificates', 'payments', 'wishlist', 'cart', 'messages', 'profile', 'settings', 'security'];

if (!in_array($page, $valid_pages)) {
    $page = 'dashboard';
}

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
                <input type="text" placeholder="Search your courses..." class="provider-search-input">
                <span class="provider-search-icon">🔍</span>
            </div>

            <div class="provider-navbar-links">
                <a href="<?php echo BASE_URL; ?>" class="provider-nav-link">
                    <i class="bi bi-house"></i>
                    <span>Home</span>
                </a>
                <a href="<?php echo BASE_URL; ?>#courses" class="provider-nav-link">
                    <i class="bi bi-journal-text"></i>
                    <span>All Courses</span>
                </a>
            </div>

            <div class="provider-navbar-item">
                <a href="<?php echo BASE_URL; ?>learner/?page=cart" class="provider-navbar-btn" title="Cart" aria-label="Cart">
                    <i class="bi bi-cart3"></i>
                </a>
            </div>

            <div class="provider-navbar-item">
                <a href="<?php echo BASE_URL; ?>learner/?page=wishlist" class="provider-navbar-btn" title="Wishlist" aria-label="Wishlist">
                    <i class="bi bi-heart"></i>
                </a>
            </div>

            <div class="provider-navbar-item notifications-dropdown">
                <button class="provider-navbar-btn notifications-btn" title="Notifications" aria-label="Notifications">
                    <span class="notification-icon">🔔</span>
                    <span class="notification-badge">4</span>
                </button>
                <div class="notifications-menu">
                    <div class="notifications-header">
                        <h4>Notifications</h4>
                        <a href="#" class="mark-all-read">Mark all as read</a>
                    </div>
                    <ul class="notifications-list">
                        <li class="notification-item unread">
                            <span class="notification-avatar">✅</span>
                            <div class="notification-content">
                                <p class="notification-text">Lesson 5 in <strong>Web Development Bootcamp</strong> is now unlocked</p>
                                <span class="notification-time">30 min ago</span>
                            </div>
                        </li>
                        <li class="notification-item unread">
                            <span class="notification-avatar">🎓</span>
                            <div class="notification-content">
                                <p class="notification-text">Your certificate for <strong>Figma UI Design</strong> is ready</p>
                                <span class="notification-time">4 hours ago</span>
                            </div>
                        </li>
                    </ul>
                    <div class="notifications-footer">
                        <a href="#" class="view-all-notifications">View all notifications</a>
                    </div>
                </div>
            </div>

            <div class="provider-navbar-item messages-dropdown">
                <button class="provider-navbar-btn messages-btn" title="Messages" aria-label="Messages">
                    <span class="messages-icon">💬</span>
                    <span class="messages-badge">2</span>
                </button>
                <div class="messages-menu">
                    <div class="messages-header">
                        <h4>Messages</h4>
                        <a href="#" class="new-message-btn">+ New</a>
                    </div>
                    <ul class="messages-list">
                        <li class="message-item unread">
                            <div class="message-avatar">AS</div>
                            <div class="message-content">
                                <p class="message-name">Aaditya Sharma</p>
                                <p class="message-text">Great progress this week. Keep going.</p>
                                <span class="message-time">1 hour ago</span>
                            </div>
                        </li>
                    </ul>
                    <div class="messages-footer">
                        <a href="<?php echo BASE_URL; ?>learner/?page=messages" class="view-all-messages">View all messages</a>
                    </div>
                </div>
            </div>

            <div class="provider-navbar-item">
                <a href="<?php echo BASE_URL; ?>learner/?page=profile" class="provider-navbar-btn profile-btn profile-static-link" title="My Profile" aria-label="My Profile">
                    <span class="profile-avatar"><?php echo ems_e($learnerInitials); ?></span>
                    <span class="profile-name"><?php echo ems_e($learnerDisplayName); ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="provider-dashboard-wrapper">
    <?php require_once(__DIR__ . '/includes/sidebar.php'); ?>

    <?php
    $page_file = __DIR__ . '/pages/' . $page . '.php';
    if (file_exists($page_file)) {
        require_once($page_file);
    } else {
        require_once(__DIR__ . '/pages/dashboard.php');
    }
    ?>
</div>

<footer class="provider-dashboard-footer">
    <p class="footer-text">© 2026 EduSkill Marketplace. All rights reserved.</p>
</footer>

</div>

<script src="<?php echo BASE_URL; ?>assets/js/main.js?v=<?php echo $assetVersion; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/aaditya.js?v=<?php echo $assetVersion; ?>"></script>
</body>
</html>

<?php
function sanitize_input($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}
?>
