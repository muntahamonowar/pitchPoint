<!-- Page heading showing the title passed from controller -->
<h1 class="page-title"><?= esc($title); ?></h1>

<!-- Wrapper for table layout (scroll styling, spacing, etc.) -->
<div class="table-wrapper">

    <table>
        <!-- Caption provides a text description for accessibility -->
        <caption>System Notifications</caption>

        <thead>
        <tr>
            <!-- Column headers for the table -->
            <th>ID</th>
            <th>User</th>
            <th>Type</th>
            <th>Status</th>
            <th>Created At</th>
            <!-- "Mark read" column removed as per your updates -->
        </tr>
        </thead>

        <tbody>

        <!-- If there are notifications in the list -->
        <?php if (!empty($notifications)): ?>

            <!-- Loop through each notification row -->
            <?php foreach ($notifications as $n): ?>
                <tr>

                    <!-- Notification ID -->
                    <td><?= (int)$n['notification_id']; ?></td>

                    <!-- User email + User ID -->
                    <td>
                        <?= esc($n['email']); ?>
                        (ID <?= (int)$n['user_id']; ?>)
                    </td>

                    <!-- Notification type (e.g., project_approved, new_message) -->
                    <td><?= esc($n['type']); ?></td>

                    <!-- Read / Unread status badge -->
                    <td>
                        <?php if ($n['is_read']): ?>
                            <!-- If is_read = 1 -->
                            <span class="status-badge status-read">Read</span>
                        <?php else: ?>
                            <!-- If is_read = 0 -->
                            <span class="status-badge status-unread">Unread</span>
                        <?php endif; ?>
                    </td>

                    <!-- Date/time the notification was created -->
                    <td><?= esc($n['created_at']); ?></td>

                    <!-- Previously: a column with a "Mark Read" form was here -->
                    <!-- You removed it intentionally, so leaving it out -->
                </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <!-- If no notifications exist -->
            <tr>
                <td colspan="5" class="text-center">
                    No notifications found.
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
</div>
