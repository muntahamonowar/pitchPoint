<?php
/**
 * VARIABLES PROVIDED BY THE CONTROLLER:
 * ------------------------------------
 * @var string $title         Page title (e.g., “View email #3”)
 * @var array  $email         Full email row from email_management table
 * @var string $adminEmail    The admin’s email address (used to detect folder)
 *
 * BELOW:
 * We automatically detect which DB column contains the message body.
 * Some setups store it as 'body', some as 'email_body', some as 'message'.
 */
$body = '';
foreach (['body', 'email_body', 'message'] as $k) {
    if (!empty($email[$k])) {
        $body = $email[$k];
        break;
    }
}
?>

<!-- PAGE TITLE -->
<h1 class="page-title"><?= esc($title); ?></h1>

<!-- BACK LINK -->
<a href="index.php?c=emails&a=index" class="email-back-link">
    &larr; Back to email list
</a>

<!-- ============================================================
     EMAIL DETAIL CARD – SHOWS ALL METADATA
     ============================================================ -->
<section class="email-detail-card">

    <!-- Metadata list -->
    <dl class="email-meta">

        <!-- EMAIL ID -->
        <div class="meta-row">
            <dt>ID</dt>
            <dd><?= (int)$email['email_id']; ?></dd>
        </div>

        <!-- INBOX / SENT DETECTION -->
        <div class="meta-row">
            <dt>Folder</dt>
            <dd>
                <?= ($email['sender_email'] ?? '') === $adminEmail
                    ? 'Sent'
                    : 'Inbox'; ?>
            </dd>
        </div>

        <!-- FROM ADDRESS -->
        <div class="meta-row">
            <dt>From</dt>
            <dd><?= esc($email['sender_email']   ?? ''); ?></dd>
        </div>

        <!-- TO ADDRESS -->
        <div class="meta-row">
            <dt>To</dt>
            <dd><?= esc($email['receiver_email'] ?? ''); ?></dd>
        </div>

        <!-- EMAIL SUBJECT -->
        <div class="meta-row">
            <dt>Subject</dt>
            <dd><?= esc($email['subject'] ?? ''); ?></dd>
        </div>

        <!-- TIMESTAMP -->
        <div class="meta-row">
            <dt>Sent date</dt>
            <dd><?= esc($email['sent_date'] ?? ''); ?></dd>
        </div>

        <!-- READ STATUS -->
        <div class="meta-row">
            <dt>Read?</dt>
            <dd><?= (int)($email['is_read'] ?? 0) === 1 ? 'Yes' : 'No'; ?></dd>
        </div>
    </dl>

    <!-- ============================================================
         BODY CONTENT
         ============================================================ -->
    <h2 class="email-body-heading">Body</h2>

    <div class="email-body-box">
        <?php if ($body !== ''): ?>
            <!-- nl2br() converts newlines to <br> for clean display -->
            <p><?= nl2br(esc($body)); ?></p>
        <?php else: ?>
            <p>(No body stored for this email.)</p>
        <?php endif; ?>
    </div>

</section>
