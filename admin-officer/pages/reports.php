<?php
$reportsOverview = function_exists('ems_admin_fetch_reports_overview')
    ? ems_admin_fetch_reports_overview($conn)
    : [
        'user_growth_percent' => 0,
        'course_completion_rate' => 0,
        'monthly_revenue' => 0,
        'revenue_growth_percent' => 0,
        'avg_rating' => 0,
        'review_count' => 0,
    ];
?>

<div class="admin-page-header">
    <h1 class="page-title">Reports & Analytics</h1>
    <p class="page-subtitle">Comprehensive platform statistics and insights</p>
</div>

<div class="reports-grid">
    <div class="report-card">
        <h3>User Growth Trend</h3>
        <p class="report-stat"><?php echo number_format((float)($reportsOverview['user_growth_percent'] ?? 0), 2); ?>% this month</p>
        <div class="mini-chart">
            <div class="chart-bar" style="width: 20%; height: 45px;"></div>
            <div class="chart-bar" style="width: 20%; height: 55px;"></div>
            <div class="chart-bar" style="width: 20%; height: 70px;"></div>
            <div class="chart-bar" style="width: 20%; height: 65px;"></div>
            <div class="chart-bar" style="width: 20%; height: 80px;"></div>
        </div>
    </div>

    <div class="report-card">
        <h3>Course Completion Rate</h3>
        <p class="report-stat"><?php echo number_format((float)($reportsOverview['course_completion_rate'] ?? 0), 2); ?>%</p>
        <p class="report-desc">Across active and completed enrollments</p>
    </div>

    <div class="report-card">
        <h3>Revenue Performance</h3>
        <p class="report-stat">$<?php echo number_format((float)($reportsOverview['monthly_revenue'] ?? 0), 2); ?></p>
        <p class="report-desc"><?php echo number_format((float)($reportsOverview['revenue_growth_percent'] ?? 0), 2); ?>% compared to last month</p>
    </div>

    <div class="report-card">
        <h3>Customer Satisfaction</h3>
        <p class="report-stat"><?php echo number_format((float)($reportsOverview['avg_rating'] ?? 0), 1); ?>/5.0</p>
        <p class="report-desc">Based on <?php echo number_format((int)($reportsOverview['review_count'] ?? 0)); ?> visible reviews</p>
    </div>
</div>

<div class="reports-section">
    <h3>Detailed Reports</h3>
    <div class="report-list">
        <div class="report-item">
            <div class="report-info">
                <h4>Monthly Performance Report</h4>
                <p>Comprehensive review of all platform metrics</p>
            </div>
            <a class="report-download-btn" href="<?php echo BASE_URL; ?>admin-officer/api.php?action=report_export_download&type=monthly-performance">📥 Download</a>
        </div>
        <div class="report-item">
            <div class="report-info">
                <h4>User Engagement Report</h4>
                <p>Detailed user activity and engagement patterns</p>
            </div>
            <a class="report-download-btn" href="<?php echo BASE_URL; ?>admin-officer/api.php?action=report_export_download&type=user-engagement">📥 Download</a>
        </div>
        <div class="report-item">
            <div class="report-info">
                <h4>Instructor Performance Report</h4>
                <p>Course ratings and instructor effectiveness metrics</p>
            </div>
            <a class="report-download-btn" href="<?php echo BASE_URL; ?>admin-officer/api.php?action=report_export_download&type=instructor-performance">📥 Download</a>
        </div>
    </div>
</div>
