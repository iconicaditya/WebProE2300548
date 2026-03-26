<?php
$providerUserId = (int)($portalUser['id'] ?? 0);
$schemaReady = function_exists('ems_provider_tables_ready') ? ems_provider_tables_ready($conn) : false;

$metrics = $schemaReady
    ? ems_provider_fetch_dashboard_metrics($conn, $providerUserId)
    : [
        'total_courses' => 0,
        'published_courses' => 0,
        'draft_courses' => 0,
        'total_students' => 0,
        'monthly_students' => 0,
        'monthly_revenue' => 0,
        'total_revenue' => 0,
        'avg_rating' => 0,
        'review_count' => 0,
        'completion_rate' => 0,
    ];

$courses = $schemaReady ? ems_provider_fetch_courses($conn, $providerUserId, 6) : [];
$recentEnrollments = $schemaReady ? ems_provider_fetch_recent_enrollments($conn, $providerUserId, 6) : [];
$enrollmentTrend = $schemaReady ? ems_provider_fetch_monthly_enrollment_trend($conn, $providerUserId, 6) : [];
$revenueTrend = $schemaReady ? ems_provider_fetch_monthly_revenue_trend($conn, $providerUserId, 6) : [];

$maxEnrollments = 1;
foreach ($enrollmentTrend as $trendValue) {
    $maxEnrollments = max($maxEnrollments, (int)$trendValue);
}

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
        <h1 class="dashboard-title">Dashboard Overview</h1>
        <p class="dashboard-subtitle">Welcome back! Here's your performance summary.</p>
    </div>

    <?php if (!$schemaReady): ?>
        <div class="alert alert-warning" role="alert">
            Course tables are not available yet. Apply <code>config/eduskill.sql</code> to enable dashboard data.
        </div>
    <?php endif; ?>

    <section class="dashboard-section" id="dashboard">
        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">📚</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Total Courses</p>
                        <p class="overview-card-value"><?php echo (int)$metrics['total_courses']; ?></p>
                    </div>
                </div>
                <p class="overview-card-footer"><?php echo (int)$metrics['published_courses']; ?> published • <?php echo (int)$metrics['draft_courses']; ?> draft</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">👥</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Total Students</p>
                        <p class="overview-card-value"><?php echo (int)$metrics['total_students']; ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">+<?php echo (int)$metrics['monthly_students']; ?> this month</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💰</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Monthly Revenue</p>
                        <p class="overview-card-value"><?php echo ems_e(ems_provider_currency_format((float)$metrics['monthly_revenue'], 'USD')); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Total: <?php echo ems_e(ems_provider_currency_format((float)$metrics['total_revenue'], 'USD')); ?></p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">⭐</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Avg Rating</p>
                        <p class="overview-card-value"><?php echo number_format((float)$metrics['avg_rating'], 1); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Based on <?php echo (int)$metrics['review_count']; ?> reviews</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">✓</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Completion %</p>
                        <p class="overview-card-value"><?php echo number_format((float)$metrics['completion_rate'], 1); ?>%</p>
                    </div>
                </div>
                <p class="overview-card-footer">Average course progress</p>
            </div>
        </div>
    </section>

    <section class="dashboard-section" id="courses">
        <div class="section-header">
            <h2 class="section-title">Course Management</h2>
            <button class="btn btn-create-course" onclick="window.location.href='<?php echo BASE_URL; ?>provider/addcourses/index.php';">+ Create New Course</button>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Price</th>
                        <th>Students</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No courses found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <?php
                            $statusBadge = ems_provider_course_status_badge($course['status'] ?? '');
                            $priceText = ($course['access_type'] ?? 'free') === 'paid'
                                ? ems_provider_currency_format((float)($course['price_amount'] ?? 0), (string)($course['currency_code'] ?? 'USD'))
                                : 'Free';

                            $rating = (float)($course['avg_rating'] ?? 0);
                            $filledStars = max(0, min(5, (int)round($rating)));
                            $stars = str_repeat('⭐', $filledStars) . str_repeat('☆', 5 - $filledStars);
                            ?>
                            <tr>
                                <td><?php echo ems_e($course['title'] ?? 'Untitled course'); ?></td>
                                <td><?php echo ems_e($priceText); ?></td>
                                <td><?php echo (int)($course['students_count'] ?? 0); ?></td>
                                <td>
                                    <span class="rating-stars"><?php echo ems_e($stars); ?></span>
                                    <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                                </td>
                                <td><span class="<?php echo ems_e($statusBadge['class']); ?>"><?php echo ems_e($statusBadge['label']); ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit-btn" title="Edit" onclick="window.location.href='<?php echo BASE_URL; ?>provider/addcourses/index.php?course_id=<?php echo (int)$course['id']; ?>'">✏️</button>
                                        <button class="action-btn delete-btn" title="Delete (coming soon)" disabled>🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-section" id="students">
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
                    <?php if (empty($recentEnrollments)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No enrollments found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentEnrollments as $enrollment): ?>
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

    <section class="dashboard-section" id="analytics">
        <div class="section-header">
            <h2 class="section-title">Analytics & Reports</h2>
        </div>

        <div class="charts-grid">
            <div class="chart-container">
                <h3 class="chart-title">Monthly Enrollments Chart</h3>
                <div class="chart-placeholder">
                    <div class="chart-bars">
                        <?php if (empty($enrollmentTrend)): ?>
                            <div class="small text-muted">No enrollment trend data available.</div>
                        <?php else: ?>
                            <?php foreach ($enrollmentTrend as $monthKey => $count): ?>
                                <?php
                                $heightPercent = max(10, (int)round(((int)$count / $maxEnrollments) * 100));
                                ?>
                                <div class="chart-bar" style="height: <?php echo $heightPercent; ?>%;" title="<?php echo ems_e($monthKey . ': ' . $count); ?>"></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($enrollmentTrend)): ?>
                        <div class="chart-labels">
                            <?php foreach (array_keys($enrollmentTrend) as $monthKey): ?>
                                <span><?php echo ems_e(date('M', strtotime($monthKey . '-01'))); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">Revenue Trend (Line Chart)</h3>
                <div class="chart-placeholder">
                    <?php if (empty($revenueTrend)): ?>
                        <div class="small text-muted">No revenue trend data available.</div>
                    <?php else: ?>
                        <?php
                        $revenueValues = array_values($revenueTrend);
                        $maxRevenue = max(1.0, (float)max($revenueValues));
                        $points = [];
                        $countRevenue = count($revenueValues);
                        foreach ($revenueValues as $idx => $value) {
                            $x = $countRevenue > 1 ? (20 + (($idx * 350) / ($countRevenue - 1))) : 20;
                            $y = 180 - (((float)$value / $maxRevenue) * 140);
                            $points[] = round($x, 1) . ',' . round($y, 1);
                        }
                        $polylinePoints = implode(' ', $points);
                        ?>
                        <div class="revenue-chart">
                            <svg viewBox="0 0 400 200" class="revenue-svg">
                                <polyline points="<?php echo ems_e($polylinePoints); ?>" stroke="#4186a0" stroke-width="3" fill="none" />
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
