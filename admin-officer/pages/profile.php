<section class="ao-profile-page" id="aoProfilePage">
    <header class="ao-profile-header-card">
        <div class="ao-profile-head-left">
            <div class="ao-profile-avatar-wrap">
                <div class="ao-profile-avatar" id="aoProfileAvatar">AO</div>
                <button type="button" class="ao-avatar-edit-btn" aria-label="Change profile photo">
                    <i class="bi bi-camera"></i>
                </button>
            </div>
            <div>
                <h1 class="ao-profile-title">Admin Officer Profile</h1>
                <p class="ao-profile-subtitle">Manage your account, preferences, and security settings</p>
                <div class="ao-profile-badges">
                    <span class="ao-badge ao-badge-role"><i class="bi bi-shield-check"></i> System Administrator</span>
                    <span class="ao-badge ao-badge-status"><i class="bi bi-circle-fill"></i> Active</span>
                </div>
            </div>
        </div>
    </header>

    <div class="ao-profile-stats-grid">
        <article class="ao-stat-card">
            <div class="ao-stat-icon"><i class="bi bi-clock-history"></i></div>
            <div>
                <p class="ao-stat-label">Last Login</p>
                <p class="ao-stat-value">Today, 10:30 AM</p>
            </div>
        </article>
        <article class="ao-stat-card">
            <div class="ao-stat-icon"><i class="bi bi-calendar-event"></i></div>
            <div>
                <p class="ao-stat-label">Joined</p>
                <p class="ao-stat-value">January 15, 2024</p>
            </div>
        </article>
        <article class="ao-stat-card">
            <div class="ao-stat-icon"><i class="bi bi-person-check"></i></div>
            <div>
                <p class="ao-stat-label">Managed Providers</p>
                <p class="ao-stat-value">248</p>
            </div>
        </article>
    </div>

    <div class="ao-profile-layout">
        <section class="ao-panel ao-panel-main">
            <div class="ao-panel-header">
                <h2>Personal Information</h2>
                <p>Update your admin profile details. All fields below are directly editable.</p>
            </div>

            <form class="ao-profile-form" id="aoProfileForm" novalidate>
                <div class="ao-form-grid">
                    <div class="ao-field">
                        <label for="aoFullName">Full Name</label>
                        <input id="aoFullName" name="fullName" type="text" value="Admin Officer">
                    </div>
                    <div class="ao-field">
                        <label for="aoDesignation">Designation</label>
                        <input id="aoDesignation" name="designation" type="text" value="Admin Officer">
                    </div>
                    <div class="ao-field">
                        <label for="aoEmail">Email Address</label>
                        <input id="aoEmail" name="email" type="email" value="admin@eduskill.com">
                    </div>
                    <div class="ao-field">
                        <label for="aoPhone">Phone Number</label>
                        <input id="aoPhone" name="phone" type="tel" value="+977 9864062605">
                    </div>
                    <div class="ao-field">
                        <label for="aoEmployeeId">Employee ID</label>
                        <input id="aoEmployeeId" name="employeeId" type="text" value="AO-2024-015">
                    </div>
                    <div class="ao-field">
                        <label for="aoDepartment">Department</label>
                        <input id="aoDepartment" name="department" type="text" value="Platform Operations">
                    </div>
                    <div class="ao-field ao-field-full">
                        <label for="aoLocation">Office Location</label>
                        <input id="aoLocation" name="location" type="text" value="EduSkill Marketplace Office, Kathmandu, Nepal">
                    </div>
                    <div class="ao-field">
                        <label for="aoTimezone">Timezone</label>
                        <input id="aoTimezone" name="timezone" type="text" value="Asia/Kathmandu (UTC+05:45)">
                    </div>
                    <div class="ao-field">
                        <label for="aoLanguage">Preferred Language</label>
                        <input id="aoLanguage" name="language" type="text" value="English">
                    </div>
                    <div class="ao-field ao-field-full">
                        <label for="aoResponsibilities">Core Responsibilities</label>
                        <textarea id="aoResponsibilities" name="responsibilities" rows="4">Provider approval workflow, learner complaint resolution, course quality monitoring, and monthly compliance reporting.</textarea>
                    </div>
                </div>

                <div class="ao-form-actions" id="aoFormActions">
                    <button type="submit" class="ao-btn ao-btn-primary" id="aoSaveBtn">
                        <i class="bi bi-check2-circle"></i>
                        Save Changes
                    </button>
                    <button type="button" class="ao-btn ao-btn-muted" id="aoCancelBtn">
                        <i class="bi bi-x-circle"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </section>

        <aside class="ao-panel ao-panel-side">
            <div class="ao-panel-header">
                <h3>Security</h3>
                <p>Quick account protection controls</p>
            </div>

            <div class="ao-security-list">
                <div class="ao-security-item">
                    <div>
                        <p class="ao-security-title">Password</p>
                        <p class="ao-security-desc" id="aoPasswordMeta">Last changed 28 days ago</p>
                    </div>
                    <button type="button" class="ao-btn ao-btn-outline-sm" id="aoChangePasswordBtn">Change</button>
                </div>

                <div class="ao-security-item">
                    <div>
                        <p class="ao-security-title">Two-factor Authentication</p>
                        <p class="ao-security-desc" id="aoTwoFaMeta">Currently enabled</p>
                    </div>
                    <button type="button" class="ao-chip-success" id="aoTwoFaToggleBtn" aria-pressed="true">Enabled</button>
                </div>

                <div class="ao-security-item">
                    <div>
                        <p class="ao-security-title">Active Sessions</p>
                        <p class="ao-security-desc" id="aoSessionMeta">2 devices connected</p>
                    </div>
                    <button type="button" class="ao-btn ao-btn-outline-sm" id="aoManageSessionsBtn">Manage</button>
                </div>
            </div>

            <div class="ao-panel-divider"></div>

            <div class="ao-panel-header">
                <h3>Preferences</h3>
                <p>Control admin dashboard behavior</p>
            </div>

            <div class="ao-switch-list">
                <label class="ao-switch-row">
                    <span>Email alerts for new providers</span>
                    <input type="checkbox" id="aoPrefEmailAlerts" name="prefEmailAlerts" checked>
                </label>
                <label class="ao-switch-row">
                    <span>Daily analytics digest</span>
                    <input type="checkbox" id="aoPrefDailyDigest" name="prefDailyDigest" checked>
                </label>
                <label class="ao-switch-row">
                    <span>Auto-archive resolved alerts</span>
                    <input type="checkbox" id="aoPrefAutoArchive" name="prefAutoArchive">
                </label>
            </div>
        </aside>
    </div>
</section>
