<!-- SIDEBAR NAVIGATION -->
<aside class="provider-sidebar">
    <div class="sidebar-header">
        <button class="sidebar-toggle-btn" aria-label="Toggle sidebar" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <h3 class="sidebar-title">Provider</h3>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/">
                    <span class="sidebar-menu-icon">📊</span>
                    <span class="sidebar-menu-text">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'profile') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=profile">
                    <span class="sidebar-menu-icon">👤</span>
                    <span class="sidebar-menu-text">My Profile</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'courses') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=courses">
                    <span class="sidebar-menu-icon">📚</span>
                    <span class="sidebar-menu-text">My Courses</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'students') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=students">
                    <span class="sidebar-menu-icon">👥</span>
                    <span class="sidebar-menu-text">Students / Enrollments</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'reviews') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=reviews">
                    <span class="sidebar-menu-icon">⭐</span>
                    <span class="sidebar-menu-text">Reviews & Ratings</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'payments') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=payments">
                    <span class="sidebar-menu-icon">💳</span>
                    <span class="sidebar-menu-text">Payments & Receipts</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'analytics') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=analytics">
                    <span class="sidebar-menu-icon">📈</span>
                    <span class="sidebar-menu-text">Analytics Reports</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'certificates') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=certificates">
                    <span class="sidebar-menu-icon">🎓</span>
                    <span class="sidebar-menu-text">Certificates</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'settings') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=settings">
                    <span class="sidebar-menu-icon">⚙️</span>
                    <span class="sidebar-menu-text">Settings</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'payments') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>provider/?page=payments">
                    <span class="sidebar-menu-icon">💰</span>
                    <span class="sidebar-menu-text">Earnings &amp; Payouts</span>
                </a>
            </li>
            <li class="sidebar-menu-item sidebar-logout-item">
                <a href="<?php echo BASE_URL; ?>auth/login.php">
                    <span class="sidebar-menu-text">Logout</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
