<?php
$providerFilter = isset($_GET['status']) ? strtolower(trim((string)$_GET['status'])) : 'all';
$providerFilter = preg_replace('/[^a-z\-]/', '', $providerFilter);
$allowedFilters = ['all', 'approved', 'rejected', 'applications'];
if (!in_array($providerFilter, $allowedFilters, true)) {
    $providerFilter = 'all';
}

$providerQuery = trim((string)($_GET['q'] ?? ''));

$providerFeedback = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewAction = strtolower(trim((string)($_POST['review_action'] ?? '')));
    $reviewDecision = strtolower(trim((string)($_POST['decision'] ?? '')));
    $approvalRequestId = (int)($_POST['approval_request_id'] ?? 0);
    $reviewNote = trim((string)($_POST['review_note'] ?? ''));

    if ($reviewAction === 'review_application') {
        if (!ems_verify_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
            $providerFeedback = ['type' => 'error', 'message' => 'Invalid security token.'];
        } else {
            $reviewResult = ems_admin_provider_review_application(
                $conn,
                $approvalRequestId,
                (int)($portalUser['id'] ?? 0),
                $reviewDecision,
                $reviewNote
            );
            $providerFeedback = [
                'type' => !empty($reviewResult['ok']) ? 'success' : 'error',
                'message' => (string)($reviewResult['message'] ?? 'Unable to update application status.'),
            ];
        }
    }
}

$providerRows = function_exists('ems_admin_provider_fetch_management_rows')
    ? ems_admin_provider_fetch_management_rows($conn, $providerFilter, 250, $providerQuery)
    : [];
$csrfToken = ems_csrf_token();
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

    <form method="get" class="pm-search-inline" style="display:flex;gap:8px;align-items:center;">
        <input type="hidden" name="page" value="providermanagement">
        <input type="hidden" name="status" value="<?php echo ems_e($providerFilter); ?>">
        <input type="text" class="filter-input" name="q" placeholder="Search provider, email, specialization" value="<?php echo ems_e($providerQuery); ?>">
        <button type="submit" class="filter-btn">Search</button>
    </form>
</div>

<?php if (is_array($providerFeedback)): ?>
<div class="pm-inline-alert <?php echo $providerFeedback['type'] === 'success' ? 'success' : 'error'; ?>">
    <?php echo ems_e((string)$providerFeedback['message']); ?>
</div>
<?php endif; ?>

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
            <?php if (empty($providerRows)): ?>
            <tr>
                <td colspan="<?php echo $providerFilter === 'applications' ? 6 : 5; ?>">No providers found for this filter.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($providerRows as $row): ?>
                    <tr
                        data-provider="<?php echo ems_e((string)($row['provider_name'] ?? 'Provider')); ?>"
                        data-email="<?php echo ems_e((string)($row['email'] ?? '')); ?>"
                        data-specialization="<?php echo ems_e((string)($row['specialization'] ?? 'General')); ?>"
                        data-applied="<?php echo ems_e((string)($row['applied_on_text'] ?? '-')); ?>"
                        data-experience="<?php echo ems_e((string)($row['experience'] ?? 'Not provided')); ?>"
                        data-docs="<?php echo ems_e((string)($row['docs_text'] ?? '')); ?>"
                        data-note="<?php echo ems_e((string)($row['review_note'] ?? '')); ?>"
                    >
                        <td><?php echo ems_e((string)($row['provider_name'] ?? 'Provider')); ?></td>
                        <td><?php echo ems_e((string)($row['email'] ?? '')); ?></td>
                        <td><?php echo ems_e((string)($row['specialization'] ?? 'General')); ?></td>
                        <td><span class="<?php echo ems_e((string)($row['status_class'] ?? 'status-pending')); ?>"><?php echo ems_e((string)($row['status_label'] ?? 'Pending')); ?></span></td>
                        <td><?php echo ems_e($providerFilter === 'applications' ? (string)($row['applied_on_text'] ?? '-') : (string)($row['joined_on_text'] ?? '-')); ?></td>

                        <?php if ($providerFilter === 'applications'): ?>
                        <td class="pm-actions-cell">
                            <button
                                type="button"
                                class="pm-action-btn pm-review"
                                data-action="review"
                                data-approval-request-id="<?php echo (int)($row['approval_request_id'] ?? 0); ?>"
                            >Review</button>

                            <form method="post" class="pm-inline-form">
                                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                <input type="hidden" name="review_action" value="review_application">
                                <input type="hidden" name="decision" value="approve">
                                <input type="hidden" name="approval_request_id" value="<?php echo (int)($row['approval_request_id'] ?? 0); ?>">
                                <input type="hidden" name="review_note" value="Approved by admin officer">
                                <button type="submit" class="pm-action-btn pm-approve">Approve</button>
                            </form>

                            <form method="post" class="pm-inline-form">
                                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                <input type="hidden" name="review_action" value="review_application">
                                <input type="hidden" name="decision" value="reject">
                                <input type="hidden" name="approval_request_id" value="<?php echo (int)($row['approval_request_id'] ?? 0); ?>">
                                <input type="hidden" name="review_note" value="Rejected by admin officer">
                                <button type="submit" class="pm-action-btn pm-reject">Reject</button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="pm-footer-note">
    <p>Backend mode enabled: Provider approval decisions are persisted and reflected in profile status.</p>
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
            <form method="post" class="pm-modal-form" id="pmModalRejectForm">
                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                <input type="hidden" name="review_action" value="review_application">
                <input type="hidden" name="decision" value="reject">
                <input type="hidden" name="approval_request_id" id="pmModalRejectRequestId" value="0">
                <input type="hidden" name="review_note" value="Rejected by admin officer">
                <button type="submit" class="pm-action-btn pm-reject" id="pmModalRejectBtn">Reject</button>
            </form>
            <form method="post" class="pm-modal-form" id="pmModalApproveForm">
                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                <input type="hidden" name="review_action" value="review_application">
                <input type="hidden" name="decision" value="approve">
                <input type="hidden" name="approval_request_id" id="pmModalApproveRequestId" value="0">
                <input type="hidden" name="review_note" value="Approved by admin officer">
                <button type="submit" class="pm-action-btn pm-approve" id="pmModalApproveBtn">Approve</button>
            </form>
        </div>
    </div>
</div>
