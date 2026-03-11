<?php
/**
 * Provider Dashboard Router
 * 
 * Main entry point for the provider dashboard system.
 * Routes to different pages based on URL query parameters.
 * Contains navbar and footer layout.
 */

$pageTitle = 'Provider Dashboard';

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
?>

<!-- PROVIDER CUSTOM NAVBAR -->
<nav class="provider-navbar">
    <div class="provider-navbar-container">
        <!-- Left: Logo Only -->
        <div class="provider-navbar-brand">
            <a href="<?php echo BASE_URL; ?>" class="provider-navbar-logo">
                <img src="<?php echo BASE_URL; ?>assets/images/logo-eduskill.png" alt="EduSkill" class="provider-navbar-logo-img">
            </a>
        </div>

        <!-- Right: Actions & Profile -->
        <div class="provider-navbar-actions">
            <!-- Search Bar (Optional) -->
            <div class="provider-navbar-search">
                <input type="text" placeholder="Search courses, students..." class="provider-search-input">
                <span class="provider-search-icon">🔍</span>
            </div>

            <!-- Notifications -->
            <div class="provider-navbar-item notifications-dropdown">
                <button class="provider-navbar-btn notifications-btn" title="Notifications" aria-label="Notifications">
                    <span class="notification-icon">🔔</span>
                    <span class="notification-badge">5</span>
                </button>
                <div class="notifications-menu">
                    <div class="notifications-header">
                        <h4>Notifications</h4>
                        <a href="#" class="mark-all-read">Mark all as read</a>
                    </div>
                    <ul class="notifications-list">
                        <li class="notification-item unread">
                            <span class="notification-avatar">👤</span>
                            <div class="notification-content">
                                <p class="notification-text">New student enrolled in <strong>Web Development</strong></p>
                                <span class="notification-time">5 min ago</span>
                            </div>
                        </li>
                        <li class="notification-item unread">
                            <span class="notification-avatar">⭐</span>
                            <div class="notification-content">
                                <p class="notification-text">You received a <strong>5-star review</strong> from Sara Wilson</p>
                                <span class="notification-time">2 hours ago</span>
                            </div>
                        </li>
                        <li class="notification-item unread">
                            <span class="notification-avatar">💳</span>
                            <div class="notification-content">
                                <p class="notification-text">Payment received: <strong>$150</strong> from Data Analytics course</p>
                                <span class="notification-time">1 day ago</span>
                            </div>
                        </li>
                        <li class="notification-item">
                            <span class="notification-avatar">📢</span>
                            <div class="notification-content">
                                <p class="notification-text">New feature: Analytics Dashboard is now available</p>
                                <span class="notification-time">3 days ago</span>
                            </div>
                        </li>
                        <li class="notification-item">
                            <span class="notification-avatar">✓</span>
                            <div class="notification-content">
                                <p class="notification-text">Course "Python Basics" was successfully published</p>
                                <span class="notification-time">5 days ago</span>
                            </div>
                        </li>
                    </ul>
                    <div class="notifications-footer">
                        <a href="#" class="view-all-notifications">View all notifications</a>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="provider-navbar-item messages-dropdown">
                <button class="provider-navbar-btn messages-btn" title="Messages" aria-label="Messages">
                    <span class="messages-icon">💬</span>
                    <span class="messages-badge">3</span>
                </button>
                <div class="messages-menu">
                    <div class="messages-header">
                        <h4>Messages</h4>
                        <a href="#" class="new-message-btn">+ New</a>
                    </div>
                    <ul class="messages-list">
                        <li class="message-item unread">
                            <div class="message-avatar">JD</div>
                            <div class="message-content">
                                <p class="message-name">John Davis</p>
                                <p class="message-text">Can you help me with the course material?</p>
                                <span class="message-time">2 min ago</span>
                            </div>
                        </li>
                        <li class="message-item unread">
                            <div class="message-avatar">AK</div>
                            <div class="message-content">
                                <p class="message-name">Anna Khan</p>
                                <p class="message-text">When is the next batch starting?</p>
                                <span class="message-time">15 min ago</span>
                            </div>
                        </li>
                        <li class="message-item unread">
                            <div class="message-avatar">SM</div>
                            <div class="message-content">
                                <p class="message-name">Sarah Martinez</p>
                                <p class="message-text">Thank you for the feedback on my assignment</p>
                                <span class="message-time">1 hour ago</span>
                            </div>
                        </li>
                    </ul>
                    <div class="messages-footer">
                        <a href="#" class="view-all-messages">View all messages</a>
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="provider-navbar-item profile-dropdown">
                <button class="provider-navbar-btn profile-btn" title="Profile" aria-label="Profile menu">
                    <span class="profile-avatar">👤</span>
                    <span class="profile-name">John Doe</span>
                    <span class="profile-arrow">▼</span>
                </button>
                <div class="profile-menu">
                    <div class="profile-menu-header">
                        <div class="profile-menu-avatar">JD</div>
                        <div class="profile-menu-info">
                            <p class="profile-menu-name">John Doe</p>
                            <p class="profile-menu-email">john@example.com</p>
                        </div>
                    </div>
                    <ul class="profile-menu-items">
                        <li><a href="<?php echo BASE_URL; ?>provider/?page=profile" class="profile-menu-link">👤 My Profile</a></li>
                        <li><a href="<?php echo BASE_URL; ?>provider/?page=settings" class="profile-menu-link">⚙️ Account Settings</a></li>
                        <li><a href="#" class="profile-menu-link">🔐 Security Settings</a></li>
                        <li><a href="<?php echo BASE_URL; ?>provider/?page=payments" class="profile-menu-link">📊 Earnings & Payouts</a></li>
                        <li><a href="#" class="profile-menu-link">📝 Course Drafts</a></li>
                    </ul>
                    <div class="profile-menu-footer">
                        <button class="logout-btn">🚪 Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

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

<!-- PROVIDER DASHBOARD FOOTER -->
<footer class="provider-dashboard-footer">
    <p class="footer-text">© 2026 EduSkill Marketplace. All rights reserved.</p>
</footer>

<?php
// Close HTML from header.php
?>
</div>

<!-- Load JavaScript Files -->
<script src="<?php echo BASE_URL; ?>assets/js/main.js?v=<?php echo APP_VERSION ?? '1.0'; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/aaditya.js?v=<?php echo APP_VERSION ?? '1.0'; ?>"></script>
<script src="<?php echo BASE_URL; ?>assets/js/sandhya.js?v=<?php echo APP_VERSION ?? '1.0'; ?>"></script>
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
