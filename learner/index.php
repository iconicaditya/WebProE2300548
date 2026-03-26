<?php
require_once(__DIR__ . '/../config/config.php');
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../includes/auth.php');
require_once(__DIR__ . '/includes/learner_data.php');

ems_require_login(['learner']);

$portalUser = ems_load_portal_user($conn);
if (!$portalUser || ($portalUser['role'] ?? '') !== 'learner') {
    ems_logout_user();
    ems_set_flash('danger', 'Unable to load your learner profile. Please log in again.');
    ems_redirect('auth/login.php');
}

$learnerDisplayName = ems_profile_text($portalUser['full_name'], 'Learner');
$learnerInitials = ems_user_initials($learnerDisplayName);
$learnerUserId = (int)($portalUser['id'] ?? 0);

$learnerTablesReady = ems_learner_tables_ready($conn);

$learnerUi = ems_learner_fetch_profile_card_data($conn, $learnerUserId);
$cartCount = (int)($learnerUi['cart_count'] ?? 0);
$wishlistCount = (int)($learnerUi['wishlist_count'] ?? 0);
$notificationUnreadCount = (int)($learnerUi['notifications_unread'] ?? 0);
$messageUnreadCount = (int)($learnerUi['messages_unread'] ?? 0);

$topNotifications = ems_learner_fetch_recent_notifications($conn, $learnerUserId, 5);
$topMessages = ems_learner_fetch_recent_messages($conn, $learnerUserId, 5);

if (empty($topNotifications)) {
    $topNotifications[] = [
        'title' => 'Welcome to EduSkill',
        'message_text' => 'Start learning by opening your enrolled courses.',
        'time_ago' => 'just now',
        'is_read' => true,
    ];
}

if (empty($topMessages)) {
    $topMessages[] = [
        'provider_name' => 'Support Team',
        'provider_initials' => 'ST',
        'message_text' => 'No new messages yet. Keep learning and stay connected.',
        'time_ago' => 'just now',
        'is_read' => true,
    ];
}

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

$GLOBALS['ems_learner_tables_ready'] = $learnerTablesReady;
$GLOBALS['ems_learner_user_id'] = $learnerUserId;
$GLOBALS['ems_learner_ui'] = $learnerUi;

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
                    <?php if ($cartCount > 0): ?>
                        <span class="messages-badge"><?php echo (int)$cartCount; ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="provider-navbar-item">
                <a href="<?php echo BASE_URL; ?>learner/?page=wishlist" class="provider-navbar-btn" title="Wishlist" aria-label="Wishlist">
                    <i class="bi bi-heart"></i>
                    <?php if ($wishlistCount > 0): ?>
                        <span class="messages-badge"><?php echo (int)$wishlistCount; ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="provider-navbar-item notifications-dropdown">
                <button class="provider-navbar-btn notifications-btn" title="Notifications" aria-label="Notifications">
                    <span class="notification-icon">🔔</span>
                    <?php if ($notificationUnreadCount > 0): ?>
                        <span class="notification-badge"><?php echo (int)$notificationUnreadCount; ?></span>
                    <?php endif; ?>
                </button>
                <div class="notifications-menu">
                    <div class="notifications-header">
                        <h4>Notifications</h4>
                        <a href="<?php echo BASE_URL; ?>learner/?page=settings" class="mark-all-read" data-action="notifications-mark-all-read">Mark all as read</a>
                    </div>
                    <ul class="notifications-list">
                        <?php foreach ($topNotifications as $notification): ?>
                            <?php
                            $notificationTitle = trim((string)($notification['title'] ?? 'Notification'));
                            $notificationText = trim((string)($notification['message_text'] ?? 'You have a new update.'));
                            $notificationTime = trim((string)($notification['time_ago'] ?? 'just now'));
                            $notificationUnread = !empty($notification['is_read']) ? false : true;
                            ?>
                            <li class="notification-item<?php echo $notificationUnread ? ' unread' : ''; ?>" data-action="notification-mark-read" data-notification-id="<?php echo (int)($notification['id'] ?? 0); ?>">
                                <span class="notification-avatar">🔔</span>
                                <div class="notification-content">
                                    <p class="notification-text"><strong><?php echo ems_e($notificationTitle); ?></strong> — <?php echo ems_e($notificationText); ?></p>
                                    <span class="notification-time"><?php echo ems_e($notificationTime); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="notifications-footer">
                        <a href="<?php echo BASE_URL; ?>learner/?page=settings" class="view-all-notifications">View all notifications</a>
                    </div>
                </div>
            </div>

            <div class="provider-navbar-item messages-dropdown">
                <button class="provider-navbar-btn messages-btn" title="Messages" aria-label="Messages">
                    <span class="messages-icon">💬</span>
                    <?php if ($messageUnreadCount > 0): ?>
                        <span class="messages-badge"><?php echo (int)$messageUnreadCount; ?></span>
                    <?php endif; ?>
                </button>
                <div class="messages-menu">
                    <div class="messages-header">
                        <h4>Messages</h4>
                        <a href="<?php echo BASE_URL; ?>learner/?page=messages" class="new-message-btn">+ New</a>
                    </div>
                    <ul class="messages-list">
                        <?php foreach ($topMessages as $message): ?>
                            <?php
                            $messageUnread = !empty($message['is_read']) ? false : true;
                            $messageInitials = trim((string)($message['provider_initials'] ?? ems_user_initials((string)($message['provider_name'] ?? 'Instructor'))));
                            $messageName = trim((string)($message['provider_name'] ?? 'Instructor'));
                            $messageText = trim((string)($message['message_text'] ?? ''));
                            $messageTime = trim((string)($message['time_ago'] ?? 'just now'));
                            ?>
                            <li class="message-item<?php echo $messageUnread ? ' unread' : ''; ?>" data-action="message-mark-read" data-message-id="<?php echo (int)($message['id'] ?? 0); ?>">
                                <div class="message-avatar"><?php echo ems_e($messageInitials); ?></div>
                                <div class="message-content">
                                    <p class="message-name"><?php echo ems_e($messageName); ?></p>
                                    <p class="message-text"><?php echo ems_e($messageText); ?></p>
                                    <span class="message-time"><?php echo ems_e($messageTime); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
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

<script>
window.eduSkillLearnerContext = {
    apiUrl: <?php echo json_encode((string)(BASE_URL . 'learner/api.php'), JSON_UNESCAPED_UNICODE); ?>,
    csrfToken: <?php echo json_encode((string)ems_csrf_token(), JSON_UNESCAPED_UNICODE); ?>,
    learnerUserId: <?php echo (int)$learnerUserId; ?>,
    loginUrl: <?php echo json_encode((string)(BASE_URL . 'auth/login.php'), JSON_UNESCAPED_UNICODE); ?>
};
</script>

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
