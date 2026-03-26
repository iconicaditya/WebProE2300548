<?php
$providerUserId = (int)($portalUser['id'] ?? 0);
$schemaReady = function_exists('ems_provider_tables_ready') ? ems_provider_tables_ready($conn) : false;

$reviews = $schemaReady ? ems_provider_fetch_recent_reviews($conn, $providerUserId, 50) : [];
$metrics = $schemaReady ? ems_provider_fetch_dashboard_metrics($conn, $providerUserId) : ['avg_rating' => 0, 'review_count' => 0];
$breakdown = $schemaReady ? ems_provider_fetch_rating_breakdown($conn, $providerUserId) : [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$totalReviews = max(1, array_sum($breakdown));

$relativeTime = static function ($dateTime) {
    $ts = strtotime((string)$dateTime);
    if (!$ts) return 'just now';

    $diff = time() - $ts;
    if ($diff < 60) return $diff . ' sec ago';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('d M Y', $ts);
};

$userInitials = static function ($name) {
    $parts = preg_split('/\s+/', trim((string)$name));
    if (!$parts || $parts[0] === '') return 'U';
    $first = strtoupper(substr($parts[0], 0, 1));
    $second = isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : '';
    return $first . $second;
};
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Reviews & Ratings</h1>
        <p class="dashboard-subtitle">View feedback and ratings from your students.</p>
    </div>

    <?php if (!$schemaReady): ?>
        <div class="alert alert-warning" role="alert">
            Course tables are not available yet. Apply <code>config/eduskill.sql</code> to enable review data.
        </div>
    <?php endif; ?>

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Student Reviews</h2>
        </div>

        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Learner</th>
                        <th>Course</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reviews)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No reviews found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <?php
                            $rating = max(1, min(5, (int)($review['rating'] ?? 0)));
                            $stars = str_repeat('⭐', $rating) . str_repeat('☆', 5 - $rating);
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="status-badge status-active"><?php echo ems_e($userInitials($review['learner_name'] ?? 'Learner')); ?></span>
                                        <span><?php echo ems_e($review['learner_name'] ?? 'Learner'); ?></span>
                                    </div>
                                </td>
                                <td><?php echo ems_e($review['course_title'] ?? '-'); ?></td>
                                <td>
                                    <span class="rating-stars"><?php echo ems_e($stars); ?></span>
                                    <span class="rating-value"><?php echo $rating; ?>/5</span>
                                </td>
                                <td><?php echo ems_e($review['review_text'] ?? ''); ?></td>
                                <td><?php echo ems_e($relativeTime($review['created_at'] ?? 'now')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Rating Summary</h2>
        </div>

        <div class="overview-grid">
            <div class="overview-card">
                <div class="overview-card-header">
                    <div class="overview-card-icon">⭐</div>
                    <div class="overview-card-info">
                        <p class="overview-card-title">Average Rating</p>
                        <p class="overview-card-value"><?php echo number_format((float)($metrics['avg_rating'] ?? 0), 1); ?></p>
                    </div>
                </div>
                <p class="overview-card-footer">Based on <?php echo (int)($metrics['review_count'] ?? 0); ?> visible reviews</p>
            </div>

            <?php for ($star = 5; $star >= 1; $star--): ?>
                <?php
                $count = (int)($breakdown[$star] ?? 0);
                $percent = (int)round(($count / $totalReviews) * 100);
                ?>
                <div class="overview-card">
                    <div class="overview-card-header">
                        <div class="overview-card-icon"><?php echo $star; ?>★</div>
                        <div class="overview-card-info">
                            <p class="overview-card-title"><?php echo $star; ?> Star Reviews</p>
                            <p class="overview-card-value"><?php echo $count; ?></p>
                        </div>
                    </div>
                    <p class="overview-card-footer"><?php echo $percent; ?>% of total reviews</p>
                </div>
            <?php endfor; ?>
        </div>
    </section>
</main>
