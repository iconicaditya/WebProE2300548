<?php
$providerFilter = isset($_GET['status']) ? strtolower(trim((string)$_GET['status'])) : 'all';
$providerFilter = preg_replace('/[^a-z\-]/', '', $providerFilter);
$allowedFilters = ['all', 'approved', 'rejected', 'applications'];
if (!in_array($providerFilter, $allowedFilters, true)) {
    $providerFilter = 'all';
}
?>

<div class="admin-page-header">
    <h1 class="page-title">Provider Management</h1>
    <p class="page-subtitle">Manage providers and review new provider applications</p>
</div>

<div class="admin-filters pm-filters">
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=providermanagement&status=all" class="filter-btn<?php echo ($providerFilter === 'all') ? ' active' : ''; ?>">All</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=providermanagement&status=approved" class="filter-btn<?php echo ($providerFilter === 'approved') ? ' active' : ''; ?>">Approved Providers</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=providermanagement&status=rejected" class="filter-btn<?php echo ($providerFilter === 'rejected') ? ' active' : ''; ?>">Rejected Providers</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=providermanagement&status=applications" class="filter-btn<?php echo ($providerFilter === 'applications') ? ' active' : ''; ?>">Applications</a>
</div>

<div class="dashboard-table-wrapper provider-table-wrap">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Provider Name</th>
                <th>Email</th>
                <th>Specialization</th>
                <th>Status</th>
                <th><?php echo ($providerFilter === 'applications') ? 'Applied On' : 'Joined'; ?></th>
                <?php if ($providerFilter === 'applications'): ?>
                <th>Action</th>
                <?php endif; ?>
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

            <?php if ($providerFilter === 'applications'): ?>
            <tr data-app-row="1" data-provider="CodeCraft Academy" data-email="hello@codecraft.com" data-specialization="Full Stack Development" data-applied="Mar 09, 2026" data-experience="6 years" data-docs="Company Registration, PAN, Instructor Portfolio" data-note="Strong technical curriculum with project-based teaching approach.">
                <td>CodeCraft Academy</td>
                <td>hello@codecraft.com</td>
                <td>Full Stack Development</td>
                <td><span class="status-pending">Pending Review</span></td>
                <td>Mar 09, 2026</td>
                <td class="pm-actions-cell">
                    <button type="button" class="pm-action-btn pm-review" data-action="review">Review</button>
                    <button type="button" class="pm-action-btn pm-approve" data-action="approve">Approve</button>
                    <button type="button" class="pm-action-btn pm-reject" data-action="reject">Reject</button>
                </td>
            </tr>
            <tr data-app-row="2" data-provider="BrightMind Institute" data-email="team@brightmind.org" data-specialization="Business & Leadership" data-applied="Mar 11, 2026" data-experience="4 years" data-docs="Business License, Trainer CV, Sample Course Videos" data-note="Good engagement plan, but needs minor updates in course outcomes section.">
                <td>BrightMind Institute</td>
                <td>team@brightmind.org</td>
                <td>Business & Leadership</td>
                <td><span class="status-pending">Pending Review</span></td>
                <td>Mar 11, 2026</td>
                <td class="pm-actions-cell">
                    <button type="button" class="pm-action-btn pm-review" data-action="review">Review</button>
                    <button type="button" class="pm-action-btn pm-approve" data-action="approve">Approve</button>
                    <button type="button" class="pm-action-btn pm-reject" data-action="reject">Reject</button>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pm-footer-note">
    <p>Frontend demo mode: Provider actions update UI only and do not change backend data.</p>
</div>

<div class="pm-modal" id="pmReviewModal" aria-hidden="true" role="dialog" aria-labelledby="pmReviewTitle">
    <div class="pm-modal-backdrop" data-close="true"></div>
    <div class="pm-modal-card">
        <div class="pm-modal-header">
            <h3 id="pmReviewTitle">Application Review</h3>
            <button type="button" class="pm-modal-close" id="pmCloseModalBtn" aria-label="Close">×</button>
        </div>
        <div class="pm-modal-body">
            <div class="pm-review-grid">
                <div><span class="pm-label">Provider</span><p id="pmProviderName">-</p></div>
                <div><span class="pm-label">Email</span><p id="pmProviderEmail">-</p></div>
                <div><span class="pm-label">Specialization</span><p id="pmProviderSpecialization">-</p></div>
                <div><span class="pm-label">Applied On</span><p id="pmProviderApplied">-</p></div>
                <div><span class="pm-label">Experience</span><p id="pmProviderExperience">-</p></div>
                <div><span class="pm-label">Documents</span><p id="pmProviderDocs">-</p></div>
                <div class="pm-review-full"><span class="pm-label">Reviewer Note</span><p id="pmProviderNote">-</p></div>
            </div>
        </div>
        <div class="pm-modal-actions">
            <button type="button" class="pm-action-btn pm-reject" id="pmModalRejectBtn">Reject</button>
            <button type="button" class="pm-action-btn pm-approve" id="pmModalApproveBtn">Approve</button>
        </div>
    </div>
</div>
