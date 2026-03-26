<?php
$analyticsData = function_exists('ems_admin_fetch_reports_overview')
    ? ems_admin_fetch_reports_overview($conn)
    : [
        'monthly_active_users' => 0,
        'provider_approval_rate' => 0,
        'course_completion_rate' => 0,
        'monthly_revenue' => 0,
    ];
?>

<div class="admin-page-header">
    <h1 class="page-title">Analytic Reports</h1>
    <p class="page-subtitle">Platform growth and performance analytics</p>
</div>

<div class="reports-grid">
    <div class="report-card">
        <h3>Monthly Active Users</h3>
        <p class="report-stat"><?php echo number_format((int)($analyticsData['monthly_active_users'] ?? 0)); ?></p>
        <p class="report-desc">Users registered in current month</p>
    </div>
    <div class="report-card">
        <h3>Provider Approval Rate</h3>
        <p class="report-stat"><?php echo number_format((float)($analyticsData['provider_approval_rate'] ?? 0), 2); ?>%</p>
        <p class="report-desc">Total applications reviewed</p>
    </div>
    <div class="report-card">
        <h3>Learner Completion Rate</h3>
        <p class="report-stat"><?php echo number_format((float)($analyticsData['course_completion_rate'] ?? 0), 2); ?>%</p>
        <p class="report-desc">Across all active enrollments</p>
    </div>
    <div class="report-card">
        <h3>Revenue Growth</h3>
        <p class="report-stat"><?php echo number_format((float)($analyticsData['revenue_growth_percent'] ?? 0), 2); ?>%</p>
        <p class="report-desc">Compared to previous month</p>
    </div>
</div>

<div class="reports-section">
    <h3>Downloadable Reports</h3>
    <div class="report-list">
        <div class="report-item">
            <div class="report-info">
                <h4>Provider Review Report</h4>
                <p>Approval and rejection breakdown by category</p>
            </div>
            <a class="report-download-btn" href="<?php echo BASE_URL; ?>admin-officer/api.php?action=report_export_download&type=provider-review">Download</a>
        </div>
        <div class="report-item">
            <div class="report-info">
                <h4>Learner Engagement Report</h4>
                <p>Retention, activity, and completion insights</p>
            </div>
            <a class="report-download-btn" href="<?php echo BASE_URL; ?>admin-officer/api.php?action=report_export_download&type=learner-engagement">Download</a>
        </div>
    </div>
</div>
