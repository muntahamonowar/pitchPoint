<?php
/** @var array  $entrepreneurMessages */
/** @var array  $adminEmailMessages */
/** @var int    $currentUserId */
/** @var string $currentUserEmail */
?>

<h1>Inbox</h1>

<div style="margin-bottom: 1rem;">
    <a href="<?= route('message/compose') ?>" class="btn btn-primary">New message</a>
</div>

<!-- ========== CHAT WITH ENTREPRENEURS ========== -->
<section class="chat-section">
    <h2>Messages with entrepreneurs</h2>

    <?php if (empty($entrepreneurMessages)): ?>
        <p>No messages yet.</p>
    <?php else: ?>
        <div class="chat-box">
            <?php foreach ($entrepreneurMessages as $m): ?>
                <?php
                    $isMe = ((int)$m['sender_user_id'] === (int)$currentUserId);
                    // Show "You" if I sent it, otherwise show the project owner's name
                    // Use project_owner_name if available, fallback to sender_name
                    if ($isMe) {
                        $who = 'You';
                    } else {
                        // Show project owner's name if project is selected, otherwise show sender's name
                        $who = !empty($m['project_owner_name']) 
                            ? htmlspecialchars($m['project_owner_name'], ENT_QUOTES, 'UTF-8')
                            : htmlspecialchars($m['sender_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
                    }
                ?>
                <div class="chat-row <?= $isMe ? 'me' : 'them' ?>">
                    <div class="chat-bubble">
                        <div class="chat-meta">
                            <?= $who ?>
                            <?php if (!empty($m['project_title'])): ?>
                                • <?= htmlspecialchars($m['project_title'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                            • <?= htmlspecialchars($m['created_at'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <p><?= nl2br(htmlspecialchars($m['body'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<hr>

<!-- ========== CHAT WITH ADMINS (SAME STYLE) ========== -->
<section class="chat-section">
    <h2>Messages with admins</h2>

    <?php if (empty($adminEmailMessages)): ?>
        <p>No admin messages yet.</p>
    <?php else: ?>
        <div class="chat-box">
            <?php foreach ($adminEmailMessages as $e): ?>
                <?php
                    $isMe = ($e['sender_email'] === $currentUserEmail);
                    $who  = $isMe ? 'You' : htmlspecialchars($e['sender_email'], ENT_QUOTES, 'UTF-8');
                ?>
                <div class="chat-row <?= $isMe ? 'me' : 'them' ?>">
                    <div class="chat-bubble">
                        <div class="chat-meta">
                            <?= $who ?>
                            <?php if (!empty($e['subject'])): ?>
                                • <?= htmlspecialchars($e['subject'], ENT_QUOTES, 'UTF-8') ?>
                            <?php endif; ?>
                            • <?= htmlspecialchars($e['sent_date'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <p><?= nl2br(htmlspecialchars($e['body'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
