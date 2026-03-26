<?php
$providerUserId = (int)($portalUser['id'] ?? 0);
$schemaReady = function_exists('ems_provider_tables_ready') ? ems_provider_tables_ready($conn) : false;

$metrics = $schemaReady
    ? ems_provider_fetch_dashboard_metrics($conn, $providerUserId)
    : ['total_revenue' => 0, 'monthly_revenue' => 0];

$transactions = $schemaReady ? ems_provider_fetch_recent_enrollments($conn, $providerUserId, 100) : [];

$providerShareRate = 0.80;
$grossTotal = (float)($metrics['total_revenue'] ?? 0);
$grossMonthly = (float)($metrics['monthly_revenue'] ?? 0);
$netTotal = $grossTotal * $providerShareRate;
$netMonthly = $grossMonthly * $providerShareRate;
$pendingPayout = max(0.0, $netTotal - $netMonthly);

$rowStatus = static function ($enrollmentStatus) {
    $status = strtolower(trim((string)$enrollmentStatus));
    if (in_array($status, ['active', 'completed'], true)) {
        return ['label' => 'Completed', 'class' => 'status-badge status-active'];
    }
    if ($status === 'cancelled' || $status === 'refunded') {
        return ['label' => ucfirst($status), 'class' => 'status-badge status-inactive'];
    }
    return ['label' => 'Pending', 'class' => 'payment-badge payment-pending'];
};
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Payments & Receipts</h1>
        <p class="dashboard-subtitle">Manage your earnings and payment history.</p>
    </div>

    <?php if (!$schemaReady): ?>
        <div class="alert alert-warning" role="alert">
            Course tables are not available yet. Apply <code>config/eduskill.sql</code> to enable payment analytics.
        </div>
    <?php endif; ?>

    <section class="dashboard-section">
        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💰</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Total Earnings</p>
                        <p class="overview-card-value"><?php echo ems_e(ems_provider_currency_format($netTotal, 'USD')); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Approx. provider share (80%)</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💸</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Pending Payout</p>
                        <p class="overview-card-value"><?php echo ems_e(ems_provider_currency_format($pendingPayout, 'USD')); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Estimated (placeholder payout model)</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">✓</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Paid Out</p>
                        <p class="overview-card-value"><?php echo ems_e(ems_provider_currency_format($netMonthly, 'USD')); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Current month estimated paid amount</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">📅</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">This Month</p>
                        <p class="overview-card-value"><?php echo ems_e(ems_provider_currency_format($grossMonthly, 'USD')); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Gross monthly revenue from enrollments</p>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Transaction History</h2>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No transactions found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <?php
                            $amount = ($transaction['access_type'] ?? 'free') === 'paid'
                                ? ems_provider_currency_format((float)($transaction['price_amount'] ?? 0), (string)($transaction['currency_code'] ?? 'USD'))
                                : 'Free';
                            $status = $rowStatus($transaction['enrollment_status'] ?? 'pending');
                            $txnId = '#ENR' . str_pad((string)($transaction['id'] ?? 0), 6, '0', STR_PAD_LEFT);
                            ?>
                            <tr>
                                <td><?php echo ems_e($txnId); ?></td>
                                <td><?php echo ems_e($transaction['learner_name'] ?? 'Learner'); ?></td>
                                <td><?php echo ems_e($transaction['course_title'] ?? '-'); ?></td>
                                <td><?php echo ems_e($amount); ?></td>
                                <td><?php echo ems_e(date('d M Y', strtotime((string)($transaction['enrolled_at'] ?? 'now')))); ?></td>
                                <td><span class="<?php echo ems_e($status['class']); ?>"><?php echo ems_e($status['label']); ?></span></td>
                                <td>
                                    <?php if (($transaction['access_type'] ?? 'free') === 'paid'): ?>
                                        <span class="receipt-link text-muted">📄 Receipt pending ledger integration</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Withdraw Earnings</h2>
        </div>

        <div class="dashboard-table-wrapper p-3">
            <p class="mb-3">You have <strong><?php echo ems_e(ems_provider_currency_format($pendingPayout, 'USD')); ?></strong> available for withdrawal (estimated).</p>
            <button class="btn btn-primary" type="button" disabled>Request Payout (coming soon)</button>
        </div>
    </section>
</main>
