<!-- SIDEBAR NAVIGATION -->
<aside class="provider-sidebar">
    <div class="sidebar-header">
        <button class="sidebar-toggle-btn" aria-label="Toggle sidebar" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <h3 class="sidebar-title">Learner</h3>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/">
                    <span class="sidebar-menu-icon">📊</span>
                    <span class="sidebar-menu-text">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'courses') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=courses">
                    <span class="sidebar-menu-icon">📚</span>
                    <span class="sidebar-menu-text">My Courses</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'progress') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=progress">
                    <span class="sidebar-menu-icon">📈</span>
                    <span class="sidebar-menu-text">Progress Tracker</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'certificates') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=certificates">
                    <span class="sidebar-menu-icon">🎓</span>
                    <span class="sidebar-menu-text">Certificates</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'cart') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=cart">
                    <span class="sidebar-menu-icon">🛒</span>
                    <span class="sidebar-menu-text">My Cart</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'payments') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=payments">
                    <span class="sidebar-menu-icon">💳</span>
                    <span class="sidebar-menu-text">Payment History</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'wishlist') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=wishlist">
                    <span class="sidebar-menu-icon">❤️</span>
                    <span class="sidebar-menu-text">Wishlist</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'messages') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=messages">
                    <span class="sidebar-menu-icon">💬</span>
                    <span class="sidebar-menu-text">Messages</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'profile') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=profile">
                    <span class="sidebar-menu-icon">👤</span>
                    <span class="sidebar-menu-text">My Profile</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'settings') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=settings">
                    <span class="sidebar-menu-icon">⚙️</span>
                    <span class="sidebar-menu-text">Account Settings</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'security') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>learner/?page=security">
                    <span class="sidebar-menu-icon">🔐</span>
                    <span class="sidebar-menu-text">Security Settings</span>
                </a>
            </li>
            <li class="sidebar-menu-item sidebar-logout-item">
                <a href="<?php echo BASE_URL; ?>auth/logout.php" class="sidebar-logout-link">
                    <span class="sidebar-menu-text">Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
