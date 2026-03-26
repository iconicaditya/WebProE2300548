<?php
$settingsData = function_exists('ems_admin_fetch_platform_settings')
    ? ems_admin_fetch_platform_settings($conn)
    : [
        'platform_name' => 'EduSkill Marketplace',
        'platform_email' => 'support@eduskill.com',
        'support_phone' => '+977-01-0000000',
        'platform_commission' => '20',
        'minimum_payout_amount' => '100',
        'auto_approve_verified_instructors' => '1',
        'require_content_review' => '0',
    ];

$settingsFeedback = null;
$settingsErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (string)($_POST['settings_action'] ?? '') === 'update_settings') {
    if (!ems_verify_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
        $settingsFeedback = ['type' => 'error', 'message' => 'Invalid security token.'];
    } else {
        $result = function_exists('ems_admin_save_platform_settings')
            ? ems_admin_save_platform_settings($conn, (int)($portalUser['id'] ?? 0), [
                'platform_name' => trim((string)($_POST['platform_name'] ?? '')),
                'platform_email' => trim((string)($_POST['platform_email'] ?? '')),
                'support_phone' => trim((string)($_POST['support_phone'] ?? '')),
                'platform_commission' => trim((string)($_POST['platform_commission'] ?? '')),
                'minimum_payout_amount' => trim((string)($_POST['minimum_payout_amount'] ?? '')),
                'auto_approve_verified_instructors' => !empty($_POST['auto_approve_verified_instructors']),
                'require_content_review' => !empty($_POST['require_content_review']),
            ])
            : ['ok' => false, 'message' => 'Settings handler unavailable.', 'errors' => []];

        $settingsFeedback = [
            'type' => !empty($result['ok']) ? 'success' : 'error',
            'message' => (string)($result['message'] ?? 'Unable to update settings.'),
        ];
        $settingsErrors = (array)($result['errors'] ?? []);
        $settingsData = function_exists('ems_admin_fetch_platform_settings')
            ? ems_admin_fetch_platform_settings($conn)
            : $settingsData;
    }
}

$csrfToken = ems_csrf_token();
$defaultSettings = function_exists('ems_admin_default_platform_settings')
    ? ems_admin_default_platform_settings()
    : [
        'platform_name' => 'EduSkill Marketplace',
        'platform_email' => 'support@eduskill.com',
        'support_phone' => '+977-01-0000000',
        'platform_commission' => '20',
        'minimum_payout_amount' => '100',
        'auto_approve_verified_instructors' => '1',
        'require_content_review' => '0',
    ];
?>

<div class="admin-page-header">
    <h1 class="page-title">Settings</h1>
    <p class="page-subtitle">Configure platform settings and preferences</p>
</div>

<?php if (is_array($settingsFeedback)): ?>
<div class="pm-inline-alert <?php echo $settingsFeedback['type'] === 'success' ? 'success' : 'error'; ?>">
    <?php echo ems_e((string)$settingsFeedback['message']); ?>
</div>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
    <input type="hidden" name="settings_action" value="update_settings">

    <div class="settings-grid">
        <div class="settings-section">
            <h3>General Settings</h3>
            <div class="settings-item">
                <label>Platform Name</label>
                <input type="text" name="platform_name" value="<?php echo ems_e((string)($settingsData['platform_name'] ?? '')); ?>" class="settings-input">
                <?php if (!empty($settingsErrors['platform_name'])): ?><small class="text-danger"><?php echo ems_e((string)$settingsErrors['platform_name']); ?></small><?php endif; ?>
            </div>
            <div class="settings-item">
                <label>Platform Email</label>
                <input type="email" name="platform_email" value="<?php echo ems_e((string)($settingsData['platform_email'] ?? '')); ?>" class="settings-input">
                <?php if (!empty($settingsErrors['platform_email'])): ?><small class="text-danger"><?php echo ems_e((string)$settingsErrors['platform_email']); ?></small><?php endif; ?>
            </div>
            <div class="settings-item">
                <label>Support Phone</label>
                <input type="tel" name="support_phone" value="<?php echo ems_e((string)($settingsData['support_phone'] ?? '')); ?>" class="settings-input">
            </div>
        </div>

        <div class="settings-section">
            <h3>Commission Settings</h3>
            <div class="settings-item">
                <label>Platform Commission (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="platform_commission" value="<?php echo ems_e((string)($settingsData['platform_commission'] ?? '20')); ?>" class="settings-input">
                <?php if (!empty($settingsErrors['platform_commission'])): ?><small class="text-danger"><?php echo ems_e((string)$settingsErrors['platform_commission']); ?></small><?php endif; ?>
            </div>
            <div class="settings-item">
                <label>Minimum Payout Amount</label>
                <input type="number" step="0.01" min="0" name="minimum_payout_amount" value="<?php echo ems_e((string)($settingsData['minimum_payout_amount'] ?? '100')); ?>" class="settings-input">
                <?php if (!empty($settingsErrors['minimum_payout_amount'])): ?><small class="text-danger"><?php echo ems_e((string)$settingsErrors['minimum_payout_amount']); ?></small><?php endif; ?>
            </div>
        </div>

        <div class="settings-section">
            <h3>Course Approval</h3>
            <div class="settings-item">
                <label>
                    <input type="checkbox" name="auto_approve_verified_instructors" value="1" <?php echo !empty($settingsData['auto_approve_verified_instructors']) && (string)$settingsData['auto_approve_verified_instructors'] !== '0' ? 'checked' : ''; ?>>
                    Auto-approve courses from verified instructors
                </label>
            </div>
            <div class="settings-item">
                <label>
                    <input type="checkbox" name="require_content_review" value="1" <?php echo !empty($settingsData['require_content_review']) && (string)$settingsData['require_content_review'] !== '0' ? 'checked' : ''; ?>>
                    Require content review before publishing
                </label>
            </div>
        </div>
    </div>

    <div class="settings-actions">
        <button type="submit" class="save-btn">💾 Save Settings</button>
        <a class="reset-btn" href="<?php echo BASE_URL; ?>admin-officer/?page=settings">↺ Reload</a>
        <a class="reset-btn" href="<?php echo BASE_URL; ?>admin-officer/api.php?action=settings_get" target="_blank" rel="noopener">View API JSON</a>
    </div>

    <div style="margin-top:12px;font-size:.84rem;color:#64748b;">
        Default baseline: commission <?php echo ems_e((string)($defaultSettings['platform_commission'] ?? '20')); ?>%, minimum payout <?php echo ems_e((string)($defaultSettings['minimum_payout_amount'] ?? '100')); ?>.
    </div>
</form>
