<?php
$learnerStatus = strtolower(trim((string)($_GET['status'] ?? 'all')));
if (!in_array($learnerStatus, ['all', 'active', 'inactive'], true)) {
    $learnerStatus = 'all';
}

$learnerQuery = trim((string)($_GET['q'] ?? ''));

$learnerRows = function_exists('ems_admin_fetch_learner_management_rows')
    ? ems_admin_fetch_learner_management_rows($conn, $learnerStatus, $learnerQuery, 300)
    : [];
?>

<div class="admin-page-header">
    <h1 class="page-title">Learner Management</h1>
    <p class="page-subtitle">Manage learner accounts and track engagement</p>
</div>

<div class="admin-filters">
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=learnermanagement&status=all" class="filter-btn<?php echo $learnerStatus === 'all' ? ' active' : ''; ?>">All Learners</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=learnermanagement&status=active" class="filter-btn<?php echo $learnerStatus === 'active' ? ' active' : ''; ?>">Active</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=learnermanagement&status=inactive" class="filter-btn<?php echo $learnerStatus === 'inactive' ? ' active' : ''; ?>">Inactive</a>

    <form method="get" style="display:flex;gap:8px;flex:1;min-width:260px;">
        <input type="hidden" name="page" value="learnermanagement">
        <input type="hidden" name="status" value="<?php echo ems_e($learnerStatus); ?>">
        <input type="text" name="q" class="filter-input" placeholder="Search learner name or email" value="<?php echo ems_e($learnerQuery); ?>">
        <button type="submit" class="filter-btn">Search</button>
    </form>
</div>

<div class="dashboard-table-wrapper">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Learner Name</th>
                <th>Email</th>
                <th>Enrolled Courses</th>
                <th>Progress</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($learnerRows)): ?>
            <tr>
                <td colspan="5">No learner records found.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($learnerRows as $learner): ?>
                <tr>
                    <td><?php echo ems_e((string)($learner['full_name'] ?? 'Learner')); ?></td>
                    <td><?php echo ems_e((string)($learner['email'] ?? '')); ?></td>
                    <td><?php echo (int)($learner['enrolled_courses'] ?? 0); ?></td>
                    <td><?php echo number_format((float)($learner['avg_progress'] ?? 0), 1); ?>%</td>
                    <td><span class="<?php echo ems_e((string)($learner['status_class'] ?? 'status-inactive')); ?>"><?php echo ems_e(ucfirst((string)($learner['status'] ?? 'inactive'))); ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
