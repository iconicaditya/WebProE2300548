<?php
$dashboardMetrics = function_exists('ems_admin_fetch_dashboard_metrics')
    ? ems_admin_fetch_dashboard_metrics($conn)
    : [
        'total_users' => 0,
        'active_courses' => 0,
        'total_revenue' => 0,
        'completed_enrollments' => 0,
    ];

$activityTrend = function_exists('ems_admin_fetch_activity_trend')
    ? ems_admin_fetch_activity_trend($conn, 4)
    : [
        ['label' => 'Week 1', 'value' => 0],
        ['label' => 'Week 2', 'value' => 0],
        ['label' => 'Week 3', 'value' => 0],
        ['label' => 'Week 4', 'value' => 0],
    ];

$revenueBreakdown = function_exists('ems_admin_fetch_revenue_breakdown')
    ? ems_admin_fetch_revenue_breakdown($conn)
    : [
        'courses' => ['percent' => 0],
        'certificates' => ['percent' => 0],
        'subscriptions' => ['percent' => 0],
    ];

$recentActivity = function_exists('ems_admin_fetch_recent_activity')
    ? ems_admin_fetch_recent_activity($conn, 10)
    : [];

$trendMaxValue = 1;
foreach ($activityTrend as $trendPoint) {
    $trendMaxValue = max($trendMaxValue, (int)($trendPoint['value'] ?? 0));
}
?>

<div class="admin-dashboard-header">
    <h1 class="dashboard-title">System Overview</h1>
    <p class="dashboard-subtitle">Monitor and manage the EduSkill platform</p>
</div>

<div class="overview-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
            <h3 class="stat-value"><?php echo number_format((int)($dashboardMetrics['total_users'] ?? 0)); ?></h3>
            <p class="stat-label">Total Users</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-content">
            <h3 class="stat-value"><?php echo number_format((int)($dashboardMetrics['active_courses'] ?? 0)); ?></h3>
            <p class="stat-label">Active Courses</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
            <h3 class="stat-value">$<?php echo number_format((float)($dashboardMetrics['total_revenue'] ?? 0), 2); ?></h3>
            <p class="stat-label">Total Revenue</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
            <h3 class="stat-value"><?php echo number_format((int)($dashboardMetrics['completed_enrollments'] ?? 0)); ?></h3>
            <p class="stat-label">Completed Enrollments</p>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-card">
        <h3>Platform Activity (Last 30 Days)</h3>
        <div class="chart-placeholder">
            <div class="chart-bars">
                <?php foreach ($activityTrend as $trendPoint): ?>
                    <?php
                    $value = (int)($trendPoint['value'] ?? 0);
                    $heightPercent = max(8, (int)round(($value / $trendMaxValue) * 100));
                    ?>
                    <div class="chart-bar" style="height: <?php echo $heightPercent; ?>%;" title="<?php echo ems_e((string)($trendPoint['label'] ?? 'Week')); ?>: <?php echo $value; ?>"></div>
                <?php endforeach; ?>
            </div>
            <div class="chart-labels">
                <?php foreach ($activityTrend as $trendPoint): ?>
                    <span><?php echo ems_e((string)($trendPoint['label'] ?? 'Week')); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="chart-card">
        <h3>Revenue Breakdown</h3>
        <div class="chart-placeholder">
            <div style="text-align: center; padding: 20px;">
                <p>
                    Courses: <?php echo number_format((float)($revenueBreakdown['courses']['percent'] ?? 0), 2); ?>%
                    |
                    Certificates: <?php echo number_format((float)($revenueBreakdown['certificates']['percent'] ?? 0), 2); ?>%
                    |
                    Subscriptions: <?php echo number_format((float)($revenueBreakdown['subscriptions']['percent'] ?? 0), 2); ?>%
                </p>
            </div>
        </div>
    </div>
</div>

<div class="recent-activity">
    <h3>Recent System Activity</h3>
    <div class="activity-table">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Activity Type</th>
                    <th>User</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentActivity)): ?>
                <tr>
                    <td colspan="4">No recent activity found.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($recentActivity as $activity): ?>
                    <tr>
                        <td><span class="activity-badge"><?php echo ems_e((string)($activity['activity_type'] ?? 'Activity')); ?></span></td>
                        <td><?php echo ems_e((string)($activity['user'] ?? 'System')); ?></td>
                        <td><?php echo ems_e((string)($activity['details'] ?? '-')); ?></td>
                        <td><?php echo ems_e((string)($activity['timestamp_text'] ?? 'Just now')); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
