<?php
$providerUserId = (int)($portalUser['id'] ?? 0);
$schemaReady = function_exists('ems_provider_tables_ready') ? ems_provider_tables_ready($conn) : false;

$metrics = $schemaReady
    ? ems_provider_fetch_dashboard_metrics($conn, $providerUserId)
    : [
        'total_courses' => 0,
        'total_students' => 0,
        'monthly_students' => 0,
        'monthly_revenue' => 0,
        'completion_rate' => 0,
        'avg_rating' => 0,
        'review_count' => 0,
    ];

$enrollmentTrend = $schemaReady ? ems_provider_fetch_monthly_enrollment_trend($conn, $providerUserId, 6) : [];
$revenueTrend = $schemaReady ? ems_provider_fetch_monthly_revenue_trend($conn, $providerUserId, 6) : [];
$ratingBreakdown = $schemaReady ? ems_provider_fetch_rating_breakdown($conn, $providerUserId) : [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

$maxEnrollments = 1;
foreach ($enrollmentTrend as $trendValue) {
    $maxEnrollments = max($maxEnrollments, (int)$trendValue);
}

$maxRevenue = 1.0;
foreach ($revenueTrend as $trendValue) {
    $maxRevenue = max($maxRevenue, (float)$trendValue);
}

$totalRated = max(1, array_sum($ratingBreakdown));
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Analytics & Reports</h1>
        <p class="dashboard-subtitle">Analyze your course performance and trends.</p>
    </div>

    <?php if (!$schemaReady): ?>
        <div class="alert alert-warning" role="alert">
            Course tables are not available yet. Apply <code>config/eduskill.sql</code> to enable analytics data.
        </div>
    <?php endif; ?>

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Performance Metrics</h2>
        </div>

        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">📈</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Total Courses</p>
                        <p class="overview-card-value"><?php echo (int)($metrics['total_courses'] ?? 0); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Published + draft courses</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">👥</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">New Enrollments</p>
                        <p class="overview-card-value"><?php echo (int)($metrics['monthly_students'] ?? 0); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">This month</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💰</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Monthly Revenue</p>
                        <p class="overview-card-value"><?php echo ems_e(ems_provider_currency_format((float)($metrics['monthly_revenue'] ?? 0), 'USD')); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Based on paid enrollments</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">💯</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Completion Rate</p>
                        <p class="overview-card-value"><?php echo number_format((float)($metrics['completion_rate'] ?? 0), 1); ?>%</p>
                    </div>
                </div>
                <p class="overview-card-footer">Average learner progress</p>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="charts-grid">
            <div class="chart-container">
                <h3 class="chart-title">Monthly Enrollments Trend</h3>
                <div class="chart-placeholder">
                    <div class="chart-bars">
                        <?php if (empty($enrollmentTrend)): ?>
                            <div class="small text-muted">No enrollment trend data available.</div>
                        <?php else: ?>
                            <?php foreach ($enrollmentTrend as $monthKey => $count): ?>
                                <?php $heightPercent = max(10, (int)round(((int)$count / $maxEnrollments) * 100)); ?>
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
                <h3 class="chart-title">Revenue Trend (Last 6 Months)</h3>
                <div class="chart-placeholder">
                    <?php if (empty($revenueTrend)): ?>
                        <div class="small text-muted">No revenue trend data available.</div>
                    <?php else: ?>
                        <?php
                        $points = [];
                        $countRevenue = count($revenueTrend);
                        $index = 0;
                        foreach ($revenueTrend as $value) {
                            $x = $countRevenue > 1 ? (20 + (($index * 350) / ($countRevenue - 1))) : 20;
                            $y = 180 - (((float)$value / $maxRevenue) * 140);
                            $points[] = round($x, 1) . ',' . round($y, 1);
                            $index++;
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

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Ratings Breakdown</h2>
        </div>

        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">⭐</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Average Rating</p>
                        <p class="overview-card-value"><?php echo number_format((float)($metrics['avg_rating'] ?? 0), 1); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">From <?php echo (int)($metrics['review_count'] ?? 0); ?> visible reviews</p>
            </div>

            <?php for ($star = 5; $star >= 1; $star--): ?>
                <?php
                $count = (int)($ratingBreakdown[$star] ?? 0);
                $percent = (int)round(($count / $totalRated) * 100);
                ?>
                <div class="overview-card">
                    <div class="overview-card-header">
                        <div class="overview-card-icon"><?php echo $star; ?>★</div>
                        <div class="overview-card-info">
                            <p class="overview-card-title"><?php echo $star; ?> Star</p>
                            <p class="overview-card-value"><?php echo $count; ?></p>
                        </div>
                    </div>
                    <p class="overview-card-footer"><?php echo $percent; ?>% of all ratings</p>
                </div>
            <?php endfor; ?>
        </div>
    </section>
</main>
