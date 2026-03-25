<main class="provider-main-content">
    <?php
    $providerSettingsEmail = ems_profile_text($portalUser['email'] ?? '', '');
    ?>
    <!-- HEADER SECTION -->
    <div class="dashboard-header">
        <h1 class="dashboard-title">Account Settings</h1>
        <p class="dashboard-subtitle">Manage your account preferences and security.</p>
    </div>

    <!-- ACCOUNT PREFERENCES SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Account Preferences</h2>
        </div>

        <div class="settings-form">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" value="<?php echo ems_e($providerSettingsEmail); ?>" placeholder="your.email@example.com">
                <small>We'll use this for your account notifications</small>
            </div>

            <div class="form-group">
                <label>Language</label>
                <select>
                    <option selected>English</option>
                    <option>Spanish</option>
                    <option>French</option>
                    <option>German</option>
                </select>
            </div>

            <div class="form-group">
                <label>Timezone</label>
                <select>
                    <option selected>UTC-8 (Pacific Time)</option>
                    <option>UTC-5 (Eastern Time)</option>
                    <option>UTC (GMT)</option>
                    <option>UTC+1 (Central European Time)</option>
                </select>
            </div>
        </div>
    </section>

    <!-- NOTIFICATION PREFERENCES SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Notification Preferences</h2>
        </div>

        <div class="notification-settings">
            <div class="setting-item">
                <div class="setting-info">
                    <h4>New Student Enrollment</h4>
                    <p>Get notified when a student enrolls in your course</p>
                </div>
                <input type="checkbox" checked>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <h4>Student Reviews & Ratings</h4>
                    <p>Get notified when you receive a new review or rating</p>
                </div>
                <input type="checkbox" checked>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <h4>Payment Updates</h4>
                    <p>Get notified about payment status and payouts</p>
                </div>
                <input type="checkbox" checked>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <h4>Course Announcements</h4>
                    <p>Get notified about platform updates and new features</p>
                </div>
                <input type="checkbox">
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <h4>Weekly Digest</h4>
                    <p>Receive a weekly summary of your course performance</p>
                </div>
                <input type="checkbox" checked>
            </div>

            <div class="setting-item">
                <div class="setting-info">
                    <h4>Email Messages from Students</h4>
                    <p>Receive email notifications when students send you messages</p>
                </div>
                <input type="checkbox" checked>
            </div>
        </div>
    </section>

    <!-- SECURITY SETTINGS SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Security Settings</h2>
        </div>

        <div class="security-settings">
            <div class="security-item">
                <div class="security-info">
                    <h4>Change Password</h4>
                    <p>Update your password regularly to keep your account secure</p>
                </div>
                <button class="btn btn-secondary">Change Password</button>
            </div>

            <div class="security-item">
                <div class="security-info">
                    <h4>Two-Factor Authentication</h4>
                    <p>Add an extra layer of security to your account</p>
                </div>
                <span class="status-badge status-inactive">Disabled</span>
                <button class="btn btn-secondary">Enable 2FA</button>
            </div>

            <div class="security-item">
                <div class="security-info">
                    <h4>Active Sessions</h4>
                    <p>View and manage all active sessions on your account</p>
                </div>
                <button class="btn btn-secondary">View Sessions</button>
            </div>

            <div class="security-item">
                <div class="security-info">
                    <h4>Connected Apps</h4>
                    <p>Manage third-party apps that have access to your account</p>
                </div>
                <button class="btn btn-secondary">Manage Apps</button>
            </div>
        </div>
    </section>

    <!-- DANGER ZONE SECTION -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title danger">Danger Zone</h2>
        </div>

        <div class="danger-settings">
            <div class="danger-item">
                <div class="danger-info">
                    <h4>Download Your Data</h4>
                    <p>Request a copy of all your data in a portable format</p>
                </div>
                <button class="btn btn-warning">Download Data</button>
            </div>

            <div class="danger-item">
                <div class="danger-info">
                    <h4>Deactivate Account</h4>
                    <p>Temporarily disable your account. You can reactivate anytime.</p>
                </div>
                <button class="btn btn-danger">Deactivate Account</button>
            </div>

            <div class="danger-item">
                <div class="danger-info">
                    <h4>Delete Account</h4>
                    <p>Permanently delete your account and all associated data (this cannot be undone)</p>
                </div>
                <button class="btn btn-danger-outline">Delete Account</button>
            </div>
        </div>
    </section>

    <!-- SAVE CHANGES BUTTON -->
    <section class="dashboard-section">
        <button class="btn btn-primary btn-large">Save Changes</button>
    </section>
</main>
