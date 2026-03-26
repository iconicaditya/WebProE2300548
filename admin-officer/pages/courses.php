<?php
$courseFilter = strtolower(trim((string)($_GET['status'] ?? 'all')));
if (!in_array($courseFilter, ['all', 'pending', 'published', 'suspended'], true)) {
    $courseFilter = 'all';
}

$courseQuery = trim((string)($_GET['q'] ?? ''));
$csrfToken = ems_csrf_token();

$courseFeedback = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = strtolower(trim((string)($_POST['course_action'] ?? '')));
    $decision = strtolower(trim((string)($_POST['decision'] ?? '')));
    $courseId = (int)($_POST['course_id'] ?? 0);
    $reviewNote = trim((string)($_POST['review_note'] ?? ''));

    if ($action === 'review_course') {
        if (!ems_verify_csrf_token((string)($_POST['csrf_token'] ?? ''))) {
            $courseFeedback = ['type' => 'error', 'message' => 'Invalid security token.'];
        } else {
            $result = function_exists('ems_admin_course_review')
                ? ems_admin_course_review($conn, $courseId, (int)($portalUser['id'] ?? 0), $decision, $reviewNote)
                : ['ok' => false, 'message' => 'Course review handler unavailable.'];

            $courseFeedback = [
                'type' => !empty($result['ok']) ? 'success' : 'error',
                'message' => (string)($result['message'] ?? 'Unable to process course action.'),
            ];
        }
    }
}

$courseRows = function_exists('ems_admin_fetch_course_management_rows')
    ? ems_admin_fetch_course_management_rows($conn, $courseFilter, $courseQuery, 300)
    : [];
?>

<div class="admin-page-header">
    <h1 class="page-title">Courses Management</h1>
    <p class="page-subtitle">Review, approve, and manage all platform courses</p>
</div>

<div class="admin-filters">
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=courses&status=all" class="filter-btn<?php echo $courseFilter === 'all' ? ' active' : ''; ?>">All Courses</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=courses&status=pending" class="filter-btn<?php echo $courseFilter === 'pending' ? ' active' : ''; ?>">Pending Approval</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=courses&status=published" class="filter-btn<?php echo $courseFilter === 'published' ? ' active' : ''; ?>">Published</a>
    <a href="<?php echo BASE_URL; ?>admin-officer/?page=courses&status=suspended" class="filter-btn<?php echo $courseFilter === 'suspended' ? ' active' : ''; ?>">Suspended</a>

    <form method="get" style="display:flex;gap:8px;flex:1;min-width:260px;">
        <input type="hidden" name="page" value="courses">
        <input type="hidden" name="status" value="<?php echo ems_e($courseFilter); ?>">
        <input type="text" name="q" placeholder="Search courses by title or instructor..." class="filter-input" value="<?php echo ems_e($courseQuery); ?>">
        <button class="filter-btn" type="submit">Filter</button>
    </form>
</div>

<?php if (is_array($courseFeedback)): ?>
<div class="pm-inline-alert <?php echo $courseFeedback['type'] === 'success' ? 'success' : 'error'; ?>">
    <?php echo ems_e((string)$courseFeedback['message']); ?>
</div>
<?php endif; ?>

<div class="dashboard-table-wrapper">
    <table class="dashboard-table">
        <thead>
            <tr>
                <th>Course Title</th>
                <th>Instructor</th>
                <th>Category</th>
                <th>Enrollments</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($courseRows)): ?>
            <tr>
                <td colspan="6">No courses found for this filter.</td>
            </tr>
            <?php else: ?>
                <?php foreach ($courseRows as $course): ?>
                <tr>
                    <td><?php echo ems_e((string)($course['title'] ?? 'Untitled Course')); ?></td>
                    <td><?php echo ems_e((string)($course['instructor_name'] ?? 'Provider')); ?></td>
                    <td><?php echo ems_e((string)($course['category'] ?? 'General')); ?></td>
                    <td><?php echo number_format((int)($course['enrollments'] ?? 0)); ?></td>
                    <td><span class="<?php echo ems_e((string)($course['status_class'] ?? 'status-pending')); ?>"><?php echo ems_e((string)($course['status_label'] ?? 'Pending')); ?></span></td>
                    <td>
                        <?php if (($course['status'] ?? '') === 'draft'): ?>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                <input type="hidden" name="course_action" value="review_course">
                                <input type="hidden" name="decision" value="approve">
                                <input type="hidden" name="course_id" value="<?php echo (int)($course['id'] ?? 0); ?>">
                                <input type="hidden" name="review_note" value="Approved by admin officer">
                                <button class="action-btn" type="submit" title="Approve">✅</button>
                            </form>

                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                <input type="hidden" name="course_action" value="review_course">
                                <input type="hidden" name="decision" value="reject">
                                <input type="hidden" name="course_id" value="<?php echo (int)($course['id'] ?? 0); ?>">
                                <input type="hidden" name="review_note" value="Rejected by admin officer">
                                <button class="action-btn" type="submit" title="Reject">❌</button>
                            </form>
                        <?php else: ?>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo ems_e($csrfToken); ?>">
                                <input type="hidden" name="course_action" value="review_course">
                                <input type="hidden" name="decision" value="archive">
                                <input type="hidden" name="course_id" value="<?php echo (int)($course['id'] ?? 0); ?>">
                                <input type="hidden" name="review_note" value="Archived by admin officer">
                                <button class="action-btn" type="submit" title="Suspend">🛑</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
