<?php
$providerFilter = isset($_GET['status']) ? sanitize_input($_GET['status']) : 'all';
$allowedFilters = ['all', 'approved', 'rejected'];
if (!in_array($providerFilter, $allowedFilters, true)) {
    $providerFilter = 'all';
}
?>

<div class="admin-page-header">
    <h1 class="page-title">Provider Management</h1>
    <p class="page-subtitle">View all providers and filter by approval status</p>
</div>

<div class="admin-filters">
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=providers&status=all" class="filter-btn<?php echo ($providerFilter === 'all') ? ' active' : ''; ?>">All</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=providers&status=approved" class="filter-btn<?php echo ($providerFilter === 'approved') ? ' active' : ''; ?>">Approved Providers</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=providers&status=rejected" class="filter-btn<?php echo ($providerFilter === 'rejected') ? ' active' : ''; ?>">Rejected Providers</a>
</div>

<div class="dashboard-table-wrapper">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Provider Name</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Status</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($providerFilter === 'all' || $providerFilter === 'approved'): ?>
            <tr>
                <td>Skill Hub Academy</td>
                <td>contact@skillhub.com</td>
                <td>Programming</td>
                <td><span class="status-active">Approved</span></td>
                <td>Nov 10, 2024</td>
            </tr>
            <tr>
                <td>Data Pro Institute</td>
                <td>admin@datapro.com</td>
                <td>Data Science</td>
                <td><span class="status-active">Approved</span></td>
                <td>Dec 01, 2024</td>
            </tr>
            <?php endif; ?>

            <?php if ($providerFilter === 'all' || $providerFilter === 'rejected'): ?>
            <tr>
                <td>Design Master Studio</td>
                <td>team@designmaster.com</td>
                <td>UI/UX</td>
                <td><span class="status-inactive">Rejected</span></td>
                <td>Nov 18, 2024</td>
            </tr>
            <tr>
                <td>Quick Learn Center</td>
                <td>support@quicklearn.com</td>
                <td>Business</td>
                <td><span class="status-inactive">Rejected</span></td>
                <td>Dec 05, 2024</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
