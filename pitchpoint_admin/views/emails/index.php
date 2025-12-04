<?php
/**
 * VARIABLES PROVIDED BY THE CONTROLLER:
 * ------------------------------------
 * @var string $title          Page title ("Email Management")
 * @var array  $emails         List of email rows from email_management table
 * @var string $currentFolder  Which folder filter is active: inbox | sent | all
 * @var string $adminEmail     Email address used to detect inbox/sent
 */
?>
<h1 class="page-title"><?= esc($title); ?></h1>

<!-- ============================================================
     TOP ACTION BAR – "Compose New Email"
     ============================================================ -->
<div class="page-actions">
    <!-- Link to email compose page -->
    <a href="index.php?c=emails&a=compose" class="btn btn-primary email-compose-btn">
        Compose new email
    </a>
</div>


<!-- ============================================================
     FOLDER FILTER (Inbox / Sent / All)
     Uses GET because filtering does not change data
     ============================================================ -->
<form method="get" action="index.php" class="filters-row" style="margin-top:1rem;">
    <!-- Hidden fields tell the router which controller and action to load -->
    <input type="hidden" name="c" value="emails">
    <input type="hidden" name="a" value="index">

    <!-- Folder dropdown -->
    <label for="folder">Folder</label>
    <select name="folder" id="folder">
        <option value="inbox" <?= $currentFolder === 'inbox' ? 'selected' : ''; ?>>Inbox</option>
        <option value="sent"  <?= $currentFolder === 'sent'  ? 'selected' : ''; ?>>Sent</option>
        <option value="all"   <?= $currentFolder === 'all'   ? 'selected' : ''; ?>>All</option>
    </select>

    <!-- Submit button for filter -->
    <button type="submit" class="btn btn-secondary">
        Apply filters
    </button>
</form>


<!-- ============================================================
     EMAIL TABLE LIST
     ============================================================ -->
<div class="table-wrapper" style="margin-top:1rem;">
    <table>
        <caption>Email log</caption>

        <!-- Table header -->
        <thead>
        <tr>
            <th>ID</th>
            <th>Folder</th>
            <th>From</th>
            <th>To</th>
            <th>Subject</th>
            <th>Sent date</th>
            <th>Read?</th>
            <th>Actions</th>
        </tr>
        </thead>

        <!-- Table body -->
        <tbody>

        <?php if (!empty($emails)): ?>
            <!-- Loop every email -->
            <?php foreach ($emails as $e): ?>

                <!-- Highlight row if unread -->
                <tr class="<?= (int)($e['is_read'] ?? 0) === 0 ? 'row-unread' : ''; ?>">

                    <!-- Email ID -->
                    <td><?= (int)$e['email_id']; ?></td>

                    <!-- Detect folder based on sender_email -->
                    <td>
                        <?= ($e['sender_email'] ?? '') === $adminEmail
                            ? 'Sent'
                            : 'Inbox'; ?>
                    </td>

                    <!-- From address -->
                    <td><?= esc($e['sender_email']   ?? ''); ?></td>

                    <!-- To address -->
                    <td><?= esc($e['receiver_email'] ?? ''); ?></td>

                    <!-- Email subject -->
                    <td><?= esc($e['subject']        ?? ''); ?></td>

                    <!-- Sent timestamp -->
                    <td><?= esc($e['sent_date']      ?? ''); ?></td>

                    <!-- Read status -->
                    <td><?= (int)($e['is_read'] ?? 0) === 1 ? 'Yes' : 'No'; ?></td>

                    <!-- ACTION BUTTONS -->
                    <td class="actions-cell">

                        <!-- View email (opens show page) -->
                        <a href="index.php?c=emails&a=show&id=<?= (int)$e['email_id']; ?>"
                           class="btn-pill small">
                            View
                        </a>

                        <!-- If email is unread → show "Mark as read" button -->
                        <?php if ((int)($e['is_read'] ?? 0) === 0): ?>
                            <form method="post"
                                  action="index.php?c=emails&a=markRead"
                                  style="display:inline;">

                                <input type="hidden" name="email_id"
                                       value="<?= (int)$e['email_id']; ?>">

                                <button type="submit" class="btn-pill small">
                                    Mark as read
                                </button>
                            </form>
                        <?php endif; ?>

                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>
            <!-- If no emails in the list -->
            <tr>
                <td colspan="8">No emails found for this filter.</td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
</div>
