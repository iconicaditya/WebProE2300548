<!-- SIDEBAR NAVIGATION -->
<aside class="provider-sidebar">
    <div class="sidebar-header">
        <button class="sidebar-toggle-btn" aria-label="Toggle sidebar" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <h3 class="sidebar-title">Admin</h3>
    </div>

    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin-officer/">
                    <span class="sidebar-menu-icon">📊</span>
                    <span class="sidebar-menu-text">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'profile') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin-officer/?page=profile">
                    <span class="sidebar-menu-icon">👤</span>
                    <span class="sidebar-menu-text">Profile</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'providermanagement') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin-officer/?page=providermanagement">
                    <span class="sidebar-menu-icon">🏢</span>
                    <span class="sidebar-menu-text">Provider Management</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'learnermanagement') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin-officer/?page=learnermanagement">
                    <span class="sidebar-menu-icon">🎓</span>
                    <span class="sidebar-menu-text">Learner Management</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'analytic-reports') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin-officer/?page=analytic-reports">
                    <span class="sidebar-menu-icon">📈</span>
                    <span class="sidebar-menu-text">Analytic Reports</span>
                </a>
            </li>
            <li class="sidebar-menu-item <?php echo ($page === 'settings') ? 'active' : ''; ?>">
                <a href="<?php echo BASE_URL; ?>admin-officer/?page=settings">
                    <span class="sidebar-menu-icon">⚙️</span>
                    <span class="sidebar-menu-text">System Settings</span>
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
