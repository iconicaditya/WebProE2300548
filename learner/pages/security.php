<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$security = ems_learner_fetch_security_snapshot($conn, $learnerUserId);

$passwordUpdated = !empty($security['password_updated_at'])
    ? date('d M Y', strtotime((string)$security['password_updated_at']))
    : 'Not updated yet';

$recentLogin = !empty($security['recent_login_at'])
    ? date('d M Y, h:i A', strtotime((string)$security['recent_login_at']))
    : 'No login activity';
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Security Settings</h1>
        <p class="dashboard-subtitle">Keep your account secure and up to date.</p>
    </div>

    <section class="dashboard-section">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <tbody>
                    <tr><th>Password Updated</th><td><?php echo ems_e($passwordUpdated); ?></td></tr>
                    <tr><th>Two-Factor Authentication</th><td><?php echo !empty($security['two_factor_enabled']) ? 'Enabled' : 'Disabled'; ?></td></tr>
                    <tr><th>Recent Login</th><td><?php echo ems_e($recentLogin); ?></td></tr>
                    <tr><th>Trusted Device</th><td><?php echo ems_e($security['trusted_device_name'] ?? 'No trusted device'); ?></td></tr>
                </tbody>
            </table>
        </div>
    </section>
</main>
