<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$wishlistItems = ems_learner_fetch_wishlist_items($conn, $learnerUserId, 100);
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Wishlist</h1>
        <p class="dashboard-subtitle">Courses saved for later enrollment.</p>
    </div>

    <section class="dashboard-section">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Instructor</th>
                        <th>Price</th>
                        <th>Rating</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($wishlistItems)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Your wishlist is currently empty.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($wishlistItems as $item): ?>
                            <?php
                            $rating = (float)($item['avg_rating'] ?? 0);
                            $filled = max(0, min(5, (int)round($rating)));
                            $stars = str_repeat('⭐', $filled) . str_repeat('☆', 5 - $filled);
                            ?>
                            <tr>
                                <td><?php echo ems_e($item['title'] ?? 'Untitled course'); ?></td>
                                <td><?php echo ems_e($item['provider_name'] ?? 'Instructor'); ?></td>
                                <td><?php echo ems_e($item['price_text'] ?? 'Free'); ?></td>
                                <td><span class="rating-stars"><?php echo ems_e($stars); ?></span> <span class="rating-value"><?php echo number_format($rating, 1); ?></span></td>
                                <td>
                                    <button
                                        class="action-btn edit-btn"
                                        title="Add to Cart"
                                        type="button"
                                        data-action="wishlist-add-to-cart"
                                        data-course-id="<?php echo (int)($item['course_id'] ?? 0); ?>"
                                    >🛒</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
