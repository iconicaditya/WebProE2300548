<?php
$userRoleFilter = strtolower(trim((string)($_GET['role'] ?? 'all')));
if (!in_array($userRoleFilter, ['all', 'learner', 'provider', 'officer'], true)) {
    $userRoleFilter = 'all';
}

$userStatusFilter = strtolower(trim((string)($_GET['status'] ?? 'all')));
if (!in_array($userStatusFilter, ['all', 'active', 'inactive'], true)) {
    $userStatusFilter = 'all';
}

$userQuery = trim((string)($_GET['q'] ?? ''));
$csrfToken = ems_csrf_token();

$userFeedback = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = strtolower(trim((string)($_POST['user_action'] ?? '')));
    $targetUserId = (int)($_POST['user_id'] ?? 0);
    $nextStatus = strtolower(trim((string)($_POST['status'] ?? '')));

    if ($action === 'update_status') {
        if (!ems_verify_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
            $userFeedback = ['type' => 'error', 'message' => 'Invalid security token.'];
        } else {
            $result = function_exists('ems_admin_user_update_status')
                ? ems_admin_user_update_status($conn, $targetUserId, (int)($portalUser['id'] ?? 0), $nextStatus)
                : ['ok' => false, 'message' => 'User status handler unavailable.'];

            $userFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => (string)($result['message'] ?? 'Unable to update user status.'),
            ];
        }
    }
}

$userRows = function_exists('ems_admin_fetch_user_management_rows')
    ? ems_admin_fetch_user_management_rows($conn, $userRoleFilter, $userStatusFilter, $userQuery, 300)
    : [];
?>

<div class="admin-page-header">
    <h1 class="page-title">Users Management</h1>
    <p class="page-subtitle">Manage all learners, instructors, and platform users</p>
</div>

<div class="admin-filters" style="align-items:center;">
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=users&role=all&status=<?php echo ems_e($userStatusFilter); ?>" class="filter-btn<?php echo $userRoleFilter === 'all' ? ' active' : ''; ?>">All Users</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=users&role=learner&status=<?php echo ems_e($userStatusFilter); ?>" class="filter-btn<?php echo $userRoleFilter === 'learner' ? ' active' : ''; ?>">Learners</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=users&role=provider&status=<?php echo ems_e($userStatusFilter); ?>" class="filter-btn<?php echo $userRoleFilter === 'provider' ? ' active' : ''; ?>">Providers</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=users&role=officer&status=<?php echo ems_e($userStatusFilter); ?>" class="filter-btn<?php echo $userRoleFilter === 'officer' ? ' active' : ''; ?>">Officers</a>

    <a href="<?php echo BASE_URL; ?>admin-officer/?page=users&role=<?php echo ems_e($userRoleFilter); ?>&status=active" class="filter-btn<?php echo $userStatusFilter === 'active' ? ' active' : ''; ?>">Active</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=users&role=<?php echo ems_e($userRoleFilter); ?>&status=inactive" class="filter-btn<?php echo $userStatusFilter === 'inactive' ? ' active' : ''; ?>">Inactive</a>

    <form method="get" style="display:flex;gap:8px;flex:1;min-width:260px;">
        <input type="hidden" name="page" value="users">
        <input type="hidden" name="role" value="<?php echo ems_e($userRoleFilter); ?>">
        <input type="hidden" name="status" value="<?php echo ems_e($userStatusFilter); ?>">
        <input type="text" name="q" placeholder="Search users by name or email..." class="filter-input" value="<?php echo ems_e($userQuery); ?>">
        <button class="filter-btn" type="submit">Filter</button>
    </form>
</div>

<?php if (is_array($userFeedback)): ?>
<div class="pm-inline-alert <?php echo $userFeedback['type'] === 'success' ? 'success' : 'error'; ?>">
    <?php echo ems_e((string)$userFeedback['message']); ?>
</div>
<?php endif; ?>

<div class="dashboard-table-wrapper">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Status</th>
                <th>Joined Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($userRows)): ?>
            <tr>
                <td colspan="6">No users found for this filter.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($userRows as $user): ?>
                <tr>
                    <td><?php echo ems_e((string)($user['full_name'] ?? 'User')); ?></td>
                    <td><?php echo ems_e((string)($user['email'] ?? '')); ?></td>
                    <td><?php echo ems_e((string)($user['role_label'] ?? 'User')); ?></td>
                    <td><span class="<?php echo ems_e((string)($user['status_class'] ?? 'status-inactive')); ?>"><?php echo ems_e(ucfirst((string)($user['status'] ?? 'inactive'))); ?></span></td>
                    <td><?php echo ems_e((string)($user['joined_on_text'] ?? '-')); ?></td>
                    <td>
                        <?php if ((int)($user['id'] ?? 0) !== (int)($portalUser['id'] ?? 0)): ?>
                            <?php if (($user['status'] ?? '') === 'active'): ?>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                <input type="hidden" name="user_action" value="update_status">
                                <input type="hidden" name="user_id" value="<?php echo (int)($user['id'] ?? 0); ?>">
                                <input type="hidden" name="status" value="inactive">
                                <button class="action-btn" title="Deactivate" type="submit">🚫</button>
                            </form>
                            <?php else: ?>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                <input type="hidden" name="user_action" value="update_status">
                                <input type="hidden" name="user_id" value="<?php echo (int)($user['id'] ?? 0); ?>">
                                <input type="hidden" name="status" value="active">
                                <button class="action-btn" title="Activate" type="submit">✅</button>
                            </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="font-size:12px;color:#64748b;">Current user</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
