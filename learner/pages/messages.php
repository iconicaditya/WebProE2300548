<?php
$learnerUserId = (int)($learnerUserId ?? ($portalUser['id'] ?? 0));
$messages = ems_learner_fetch_messages($conn, $learnerUserId, 120);
?>

<main class="provider-main-content">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Messages</h1>
        <p class="dashboard-subtitle">Stay connected with instructors.</p>
    </div>

    <section class="dashboard-section">
        <div class="dashboard-table-wrapper">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No messages found yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <?php
                            $isRead = !empty($message['is_read']);
                            $statusClass = $isRead ? 'status-badge status-inactive' : 'status-badge status-active';
                            $statusLabel = $isRead ? 'Read' : 'Unread';
                            ?>
                            <tr>
                                <td><?php echo ems_e($message['provider_name'] ?? 'Instructor'); ?></td>
                                <td><?php echo ems_e($message['subject'] ?? 'Message'); ?></td>
                                <td><?php echo ems_e($message['message_text'] ?? ''); ?></td>
                                <td><?php echo ems_e($message['time_ago'] ?? 'just now'); ?></td>
                                <td><span class="<?php echo ems_e($statusClass); ?>"><?php echo ems_e($statusLabel); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
