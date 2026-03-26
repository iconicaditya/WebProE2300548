<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$payments = ems_learner_fetch_payments($conn, $learnerUserId, 120);

$statusBadge = static function ($status) {
    $value = strtolower(trim((string)$status));
    if ($value === 'paid') {
        return ['label' => 'Paid', 'class' => 'payment-badge payment-paid'];
    }
    if ($value === 'refunded') {
        return ['label' => 'Refunded', 'class' => 'status-badge status-inactive'];
    }
    if ($value === 'failed' || $value === 'cancelled') {
        return ['label' => ucfirst($value), 'class' => 'status-badge status-inactive'];
    }
    return ['label' => 'Pending', 'class' => 'payment-badge payment-pending'];
};
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Payment History</h1>
        <p class="dashboard-subtitle">Track your purchases and transaction status.</p>
    </div>

    <section class="dashboard-section">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No payment records found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $payment): ?>
                            <?php
                            $dateValue = (string)($payment['paid_at'] ?? $payment['created_at'] ?? date('Y-m-d H:i:s'));
                            $status = $statusBadge($payment['payment_status'] ?? 'pending');
                            $method = ucfirst(strtolower(trim((string)($payment['payment_method'] ?? 'card'))));
                            ?>
                            <tr>
                                <td><?php echo ems_e(date('d M Y', strtotime($dateValue))); ?></td>
                                <td><?php echo ems_e($payment['course_title'] ?? 'Course purchase'); ?></td>
                                <td><?php echo ems_e($payment['amount_text'] ?? '$0.00'); ?></td>
                                <td><?php echo ems_e($method); ?></td>
                                <td><?php echo ems_e($payment['transaction_ref'] ?? '-'); ?></td>
                                <td><span class="<?php echo ems_e($status['class']); ?>"><?php echo ems_e($status['label']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
