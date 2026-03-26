<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$studyData = ems_learner_fetch_weekly_study_data($conn, $learnerUserId, 6);
$completionTrend = ems_learner_fetch_completion_trend($conn, $learnerUserId, 8);

$studyMinutes = $studyData['minutes'] ?? [];
$studyLabels = $studyData['labels'] ?? [];
$maxStudyMinutes = max(1, ...array_map('intval', $studyMinutes ?: [1]));

$completionValues = array_values($completionTrend);
$completionCount = count($completionValues);
$completionPoints = [];
if ($completionCount > 0) {
    foreach ($completionValues as $index => $value) {
        $x = $completionCount > 1 ? (20 + (($index * 350) / ($completionCount - 1))) : 20;
        $y = 180 - ((max(0.0, min(100.0, (float)$value)) / 100) * 140);
        $completionPoints[] = round($x, 1) . ',' . round($y, 1);
    }
}
$polylinePoints = !empty($completionPoints)
    ? implode(' ', $completionPoints)
    : '20,180 370,180';
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Progress Tracker</h1>
        <p class="dashboard-subtitle">View your weekly study and completion trends.</p>
    </div>

    <section class="dashboard-section">
        <div class="charts-grid">
            <div class="chart-container">
                <h3 class="chart-title">Weekly Study Time</h3>
                <div class="chart-placeholder">
                    <div class="chart-bars">
                        <?php if (empty($studyMinutes)): ?>
                            <div class="small text-muted">No study activity captured yet.</div>
                        <?php else: ?>
                            <?php foreach ($studyMinutes as $minutes): ?>
                                <?php
                                $heightPercent = max(10, (int)round(((int)$minutes / $maxStudyMinutes) * 100));
                                ?>
                                <div class="chart-bar" style="height: <?php echo $heightPercent; ?>%;" title="<?php echo ems_e((string)$minutes); ?> min"></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="chart-labels">
                        <?php foreach ($studyLabels as $label): ?>
                            <span><?php echo ems_e((string)$label); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">Completion Trend</h3>
                <div class="chart-placeholder">
                    <div class="revenue-chart">
                        <svg viewBox="0 0 400 200" class="revenue-svg">
                            <polyline points="<?php echo ems_e($polylinePoints); ?>" stroke="#4186a0" stroke-width="3" fill="none" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
