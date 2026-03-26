<?php
$providerUserId = (int)($portalUser['id'] ?? 0);
$schemaReady = function_exists('ems_provider_tables_ready') ? ems_provider_tables_ready($conn) : false;
$courses = $schemaReady ? ems_provider_fetch_courses($conn, $providerUserId, 100) : [];

$approvalAccess = function_exists('ems_provider_course_creation_access')
    ? ems_provider_course_creation_access($conn, $providerUserId, (string)($portalUser['status'] ?? 'active'))
    : ['allowed' => false, 'status' => 'pending', 'message' => 'Your account must be approved by admin before you can create courses.'];

$courseCreationBlocked = empty($approvalAccess['allowed']);
$courseCreationMessage = (string)($approvalAccess['message'] ?? 'Your account must be approved by admin before you can create courses.');

$courseCreationAlertMessage = $courseCreationMessage;
if (strtolower(trim((string)($approvalAccess['status'] ?? 'pending'))) === 'pending') {
    $courseCreationAlertMessage = 'Account is pending for admin review and approval. After approved, you can add courses.';
}

$createCourseButtonOnClick = $courseCreationBlocked
    ? 'alert(' . json_encode($courseCreationAlertMessage, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '); return false;'
    : 'window.location.href=\'' . BASE_URL . 'provider/addcourses/index.php\';';
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">My Courses</h1>
        <p class="dashboard-subtitle">Manage and view all your courses.</p>
    </div>

    <?php if (!$schemaReady): ?>
        <div class="alert alert-warning" role="alert">
            Course tables are not available yet. Apply <code>config/eduskill.sql</code> to enable full backend data.
        </div>
    <?php endif; ?>

    <?php if ($courseCreationBlocked): ?>
        <div class="alert alert-warning" role="alert">
            <?php echo ems_e($courseCreationMessage); ?>
        </div>
    <?php endif; ?>

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Course Management</h2>
            <button class="btn btn-create-course" type="button" onclick="<?php echo ems_e($createCourseButtonOnClick); ?>">+ Create New Course</button>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Price</th>
                        <th>Students</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No courses found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $course): ?>
                            <?php
                            $statusBadge = ems_provider_course_status_badge($course['status'] ?? '');
                            $priceText = ($course['access_type'] ?? 'free') === 'paid'
                                ? ems_provider_currency_format((float)($course['price_amount'] ?? 0), (string)($course['currency_code'] ?? 'USD'))
                                : 'Free';

                            $rating = (float)($course['avg_rating'] ?? 0);
                            $filledStars = max(0, min(5, (int)round($rating)));
                            $stars = str_repeat('⭐', $filledStars) . str_repeat('☆', 5 - $filledStars);
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?php echo ems_e($course['title'] ?? 'Untitled course'); ?></div>
                                    <small class="text-muted">Created: <?php echo ems_e(date('d M Y', strtotime((string)($course['created_at'] ?? 'now')))); ?></small>
                                </td>
                                <td><?php echo ems_e($priceText); ?></td>
                                <td><?php echo (int)($course['students_count'] ?? 0); ?></td>
                                <td>
                                    <span class="rating-stars"><?php echo ems_e($stars); ?></span>
                                    <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                                </td>
                                <td>
                                    <span class="<?php echo ems_e($statusBadge['class']); ?>"><?php echo ems_e($statusBadge['label']); ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button
                                            class="action-btn edit-btn"
                                            title="<?php echo ems_e($courseCreationBlocked ? $courseCreationMessage : 'Edit'); ?>"
                                            <?php if ($courseCreationBlocked): ?>disabled aria-disabled="true"<?php else: ?>onclick="window.location.href='<?php echo BASE_URL; ?>provider/addcourses/index.php?course_id=<?php echo (int)$course['id']; ?>'"<?php endif; ?>
                                        >✏️</button>
                                        <button class="action-btn delete-btn" title="Delete (coming soon)" disabled>🗑️</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
