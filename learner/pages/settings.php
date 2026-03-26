<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$settings = ems_learner_fetch_settings($conn, $learnerUserId);

$languageLabelMap = [
    'en' => 'English',
    'ne' => 'Nepali',
    'hi' => 'Hindi',
];

$themeLabelMap = [
    'light' => 'Light',
    'dark' => 'Dark',
    'system' => 'System',
];

$languageCode = strtolower(trim((string)($settings['language_code'] ?? 'en')));
$themePreference = strtolower(trim((string)($settings['theme_preference'] ?? 'light')));
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Account Settings</h1>
        <p class="dashboard-subtitle">Manage your account preferences.</p>
    </div>

    <section class="dashboard-section">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <tbody>
                    <tr><th>Language</th><td><?php echo ems_e($languageLabelMap[$languageCode] ?? strtoupper($languageCode)); ?></td></tr>
                    <tr><th>Timezone</th><td><?php echo ems_e($settings['timezone'] ?? 'Asia/Kolkata'); ?></td></tr>
                    <tr><th>Notification Email</th><td><?php echo !empty($settings['notification_email_enabled']) ? 'Enabled' : 'Disabled'; ?></td></tr>
                    <tr><th>Theme</th><td><?php echo ems_e($themeLabelMap[$themePreference] ?? ucfirst($themePreference)); ?></td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>
