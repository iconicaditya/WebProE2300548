<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$schemaReady = !empty($GLOBALS['ems_learner_tables_ready']);

$metrics = ems_learner_fetch_overview_metrics($conn, $learnerUserId);
$continueLearning = ems_learner_fetch_continue_learning($conn, $learnerUserId, 8);
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Learner Dashboard</h1>
        <p class="dashboard-subtitle">Track your progress and continue your learning journey.</p>
    </div>

    <?php if (!$schemaReady): ?>
        <div class="alert alert-warning" role="alert">
            Learner backend tables are not available yet. Apply <code>config/migrations/2026_03_learner_domain.sql</code> to enable full learner data.
        </div>
    <?php endif; ?>

    <section class="dashboard-section">
        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">📚</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Enrolled Courses</p>
                        <p class="overview-card-value"><?php echo (int)$metrics['enrolled_courses']; ?></p>
                    </div>
                </div>
                <p class="overview-card-footer"><?php echo (int)$metrics['monthly_enrollments']; ?> new enrollments this month</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">✅</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Completed</p>
                        <p class="overview-card-value"><?php echo (int)$metrics['completed_courses']; ?></p>
                    </div>
                </div>
                <p class="overview-card-footer"><?php echo (int)$metrics['monthly_completed']; ?> completed this month</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">🎓</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Certificates</p>
                        <p class="overview-card-value"><?php echo (int)$metrics['certificates']; ?></p>
                    </div>
                </div>
                <p class="overview-card-footer"><?php echo (int)$metrics['ready_certificates']; ?> ready to download</p>
            </div>

            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">🎯</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Quiz Average</p>
                        <p class="overview-card-value"><?php echo number_format((float)$metrics['quiz_average'], 1); ?>%</p>
                    </div>
                </div>
                <p class="overview-card-footer">Across quiz attempts</p>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Continue Learning</h2>
            <button class="btn btn-create-course" type="button" onclick="window.location.href='<?php echo BASE_URL; ?>pages/allcources.php';">Browse Courses</button>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Instructor</th>
                        <th>Progress</th>
                        <th>Last Activity</th>
                        <th>Next Lesson</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($continueLearning)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No enrolled courses found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($continueLearning as $row): ?>
                            <tr>
                                <td><?php echo ems_e($row['course_title'] ?? 'Untitled course'); ?></td>
                                <td><?php echo ems_e($row['provider_name'] ?? 'Instructor'); ?></td>
                                <td><?php echo number_format((float)($row['progress_percent'] ?? 0), 1); ?>%</td>
                                <td><?php echo ems_e($row['last_activity_human'] ?? 'just now'); ?></td>
                                <td><?php echo ems_e($row['next_lesson'] ?? 'Start learning'); ?></td>
                                <td>
                                    <button
                                        class="action-btn edit-btn"
                                        title="Continue"
                                        type="button"
                                        onclick="window.location.href='<?php echo BASE_URL; ?>learner/?page=courses&course_id=<?php echo (int)($row['course_id'] ?? 0); ?>';"
                                    >▶️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
