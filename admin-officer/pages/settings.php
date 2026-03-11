<div class="admin-page-header">
    <h1 class="page-title">Settings</h1>
    <p class="page-subtitle">Configure platform settings and preferences</p>
</div>

<div class="settings-grid">
    <div class="settings-section">
        <h3>General Settings</h3>
        <div class="settings-item">
            <label>Platform Name</label>
            <input type="text" value="EduSkill Marketplace" class="settings-input">
        </div>
        <div class="settings-item">
            <label>Platform Email</label>
            <input type="email" value="support@eduskill.com" class="settings-input">
        </div>
        <div class="settings-item">
            <label>Support Phone</label>
            <input type="tel" value="+1 (555) 123-4567" class="settings-input">
        </div>
    </div>

    <div class="settings-section">
        <h3>Commission Settings</h3>
        <div class="settings-item">
            <label>Platform Commission (%)</label>
            <input type="number" value="20" class="settings-input">
        </div>
        <div class="settings-item">
            <label>Minimum Payout Amount</label>
            <input type="number" value="100" class="settings-input">
        </div>
    </div>

    <div class="settings-section">
        <h3>Course Approval</h3>
        <div class="settings-item">
            <label>
                <input type="checkbox" checked> Auto-approve courses from verified instructors
            </label>
        </div>
        <div class="settings-item">
            <label>
                <input type="checkbox"> Require content review before publishing
            </label>
        </div>
    </div>
</div>

<div class="settings-actions">
    <button class="save-btn">💾 Save Settings</button>
    <button class="reset-btn">↺ Reset to Default</button>
</div>
