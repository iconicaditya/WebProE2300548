<?php
$providerUserId = (int)($portalUser['id'] ?? 0);
$schemaReady = function_exists('ems_provider_tables_ready') ? ems_provider_tables_ready($conn) : false;
$enrollments = $schemaReady ? ems_provider_fetch_recent_enrollments($conn, $providerUserId, 100) : [];

$statusFromEnrollment = static function ($status) {
    $value = strtolower(trim((string)$status));
    if ($value === 'completed' || $value === 'active') {
        return ['label' => 'Active', 'class' => 'status-badge status-active'];
    }
    if ($value === 'cancelled' || $value === 'refunded') {
        return ['label' => ucfirst($value), 'class' => 'status-badge status-inactive'];
    }
    return ['label' => ucfirst($value ?: 'Unknown'), 'class' => 'status-badge status-inactive'];
};

$paymentFromCourse = static function ($accessType, $enrollmentStatus) {
    $isPaidCourse = strtolower(trim((string)$accessType)) === 'paid';
    $isPaidEnrollment = in_array(strtolower(trim((string)$enrollmentStatus)), ['active', 'completed'], true);
    if ($isPaidCourse && $isPaidEnrollment) {
        return ['label' => 'Paid', 'class' => 'payment-badge payment-paid'];
    }
    if ($isPaidCourse) {
        return ['label' => 'Pending', 'class' => 'payment-badge payment-pending'];
    }
    return ['label' => 'Free', 'class' => 'payment-badge payment-paid'];
};
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Students & Enrollments</h1>
        <p class="dashboard-subtitle">View all students enrolled in your courses.</p>
    </div>

    <?php if (!$schemaReady): ?>
        <div class="alert alert-warning" role="alert">
            Course tables are not available yet. Apply <code>config/eduskill.sql</code> to enable student enrollment data.
        </div>
    <?php endif; ?>

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Student Enrollment List</h2>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Enroll Date</th>
                        <th>Payment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($enrollments)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No enrollments found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <?php
                            $payment = $paymentFromCourse($enrollment['access_type'] ?? 'free', $enrollment['enrollment_status'] ?? '');
                            $status = $statusFromEnrollment($enrollment['enrollment_status'] ?? '');
                            ?>
                            <tr>
                                <td><?php echo ems_e($enrollment['learner_name'] ?? 'Learner'); ?></td>
                                <td><?php echo ems_e($enrollment['learner_email'] ?? ''); ?></td>
                                <td><?php echo ems_e($enrollment['course_title'] ?? '-'); ?></td>
                                <td><?php echo ems_e(date('d M Y', strtotime((string)($enrollment['enrolled_at'] ?? 'now')))); ?></td>
                                <td><span class="<?php echo ems_e($payment['class']); ?>"><?php echo ems_e($payment['label']); ?></span></td>
                                <td><span class="<?php echo ems_e($status['class']); ?>"><?php echo ems_e($status['label']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
