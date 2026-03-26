<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$certificates = ems_learner_fetch_certificates($conn, $learnerUserId, 100);
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Certificates</h1>
        <p class="dashboard-subtitle">Download your completion certificates.</p>
    </div>

    <section class="dashboard-section">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Completion Date</th>
                        <th>Grade</th>
                        <th>Certificate ID</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($certificates)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No certificates are available yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($certificates as $certificate): ?>
                            <?php
                            $issuedAt = (string)($certificate['issued_at'] ?? date('Y-m-d H:i:s'));
                            $grade = trim((string)($certificate['grade_label'] ?? 'A')) ?: 'A';
                            $code = trim((string)($certificate['certificate_code'] ?? 'EDU-CERT'));
                            $downloadUrl = trim((string)($certificate['download_url'] ?? ''));
                            ?>
                            <tr>
                                <td><?php echo ems_e($certificate['course_title'] ?? 'Course'); ?></td>
                                <td><?php echo ems_e(date('d M Y', strtotime($issuedAt))); ?></td>
                                <td><?php echo ems_e($grade); ?></td>
                                <td><?php echo ems_e($code); ?></td>
                                <td>
                                    <?php if ($downloadUrl !== ''): ?>
                                        <a class="action-btn edit-btn" title="Download" href="<?php echo ems_e($downloadUrl); ?>" target="_blank" rel="noopener">⬇️</a>
                                    <?php else: ?>
                                        <button class="action-btn edit-btn" title="Download" type="button" onclick="window.location.href='<?php echo BASE_URL; ?>learner/?page=profile';">⬇️</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
