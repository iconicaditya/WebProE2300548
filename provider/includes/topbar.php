<!-- PROVIDER CUSTOM NAVBAR -->
<?php
$topbarProfilePhotoUrl = trim((string)($portalUser['profile_photo_url'] ?? ''));
$topbarProfilePhotoSrc = $topbarProfilePhotoUrl !== ''
    ? (preg_match('#^https?://#i', $topbarProfilePhotoUrl) ? $topbarProfilePhotoUrl : BASE_URL . ltrim($topbarProfilePhotoUrl, '/'))
    : '';
?>
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

            <!-- Profile Link (No Dropdown) -->
            <div class="provider-navbar-item">
                <a href="<?php echo BASE_URL; ?>provider/?page=profile" class="provider-navbar-btn" title="My Profile" aria-label="My Profile">
                    <span class="topbar-profile-avatar">
                        <?php if ($topbarProfilePhotoSrc !== ''): ?>
                            <img src="<?php echo ems_e($topbarProfilePhotoSrc); ?>" alt="Profile" class="topbar-profile-avatar-img">
                        <?php else: ?>
                            <span class="topbar-profile-avatar-fallback"><?php echo ems_e($providerInitials); ?></span>
                        <?php endif; ?>
                    </span>
                    <span class="topbar-profile-name"><?php echo ems_e($providerDisplayName); ?></span>
                </a>
            </div>
        </div>
    </div>
</nav>
