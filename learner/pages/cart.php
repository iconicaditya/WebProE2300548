<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$cartPayload = ems_learner_fetch_cart_items($conn, $learnerUserId, 100);
$cartItems = $cartPayload['items'] ?? [];
$summary = $cartPayload['summary'] ?? ['subtotal' => 0, 'discount' => 0, 'total' => 0, 'currency_code' => 'USD'];

$subtotalText = ems_learner_currency_format((float)($summary['subtotal'] ?? 0), (string)($summary['currency_code'] ?? 'USD'));
$discountText = ems_learner_currency_format((float)($summary['discount'] ?? 0), (string)($summary['currency_code'] ?? 'USD'));
$totalText = ems_learner_currency_format((float)($summary['total'] ?? 0), (string)($summary['currency_code'] ?? 'USD'));
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">My Cart</h1>
        <p class="dashboard-subtitle">Review selected courses before checkout.</p>
    </div>

    <section class="dashboard-section">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Instructor</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cartItems)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Your cart is currently empty.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?php echo ems_e($item['title'] ?? 'Untitled course'); ?></td>
                                <td><?php echo ems_e($item['provider_name'] ?? 'Instructor'); ?></td>
                                <td><?php echo ems_e($item['unit_price_text'] ?? '$0.00'); ?></td>
                                <td><?php echo ems_e($item['discount_text'] ?? '$0.00'); ?></td>
                                <td><?php echo ems_e($item['line_total_text'] ?? '$0.00'); ?></td>
                                <td>
                                    <button
                                        class="action-btn edit-btn"
                                        title="Remove"
                                        type="button"
                                        data-action="cart-remove"
                                        data-course-id="<?php echo (int)($item['course_id'] ?? 0); ?>"
                                    >🗑️</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2"><strong>Summary</strong></td>
                            <td><strong><?php echo ems_e($subtotalText); ?></strong></td>
                            <td><strong><?php echo ems_e($discountText); ?></strong></td>
                            <td><strong><?php echo ems_e($totalText); ?></strong></td>
                            <td>
                                <button class="action-btn edit-btn" title="Checkout" type="button" onclick="window.location.href='<?php echo BASE_URL; ?>pages/payment.php';">✅</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
