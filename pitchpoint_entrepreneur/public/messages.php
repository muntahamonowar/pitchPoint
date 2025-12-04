<?php
// Page title shown in browser and header
$TITLE = 'Messages';

// Load layout + authentication
require dirname(__DIR__) . '/includes/header.php';
require_login();

// DB connection + current user info
$pdo = db();
$user = current_user();
$userId = (int) ($user['user_id'] ?? 0);

// Safety guard (very unlikely but prevents errors)
if ($userId <= 0) {
  ?>
  <h1 class="page-title">Messages</h1>
  <p>Unable to load your messages.</p>
  <?php
  require dirname(__DIR__) . '/includes/footer.php';
  exit;
}

/**
 * Load inbox messages for this user.
 * Shows:
 * - message body
 * - who sent it
 * - which project it’s about
 * - date and unread status
 */
$sql = "
  SELECT
    m.message_id,
    m.project_id,
    m.sender_user_id,
    m.receiver_user_id,
    m.body,
    m.is_read,
    m.created_at,
    p.title AS project_title,
    u.name  AS sender_name,
    u.email AS sender_email
  FROM messages m
  LEFT JOIN projects p ON p.project_id = m.project_id
  LEFT JOIN users u ON u.user_id = m.sender_user_id
  WHERE m.receiver_user_id = :uid
  ORDER BY m.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $userId]);
$messages = $stmt->fetchAll();
?>

<h1 class="page-title">Messages</h1>

<?php if (!$messages): ?>

  <!-- If inbox is empty -->
  <p>You don’t have any messages yet.</p>

<?php else: ?>

  <!-- Inbox list -->
  <div class="messages-list">
    <?php foreach ($messages as $msg): ?>
      <article class="message-card"
        style="background:#fff;border-radius:16px;padding:16px 18px;margin-bottom:16px;box-shadow:0 4px 10px rgba(0,0,0,0.04);">

        <header style="margin-bottom:8px;">

          <!-- Reference to project if available -->
          <?php if (!empty($msg['project_title'])): ?>
            <div class="message-project" style="font-weight:600;margin-bottom:4px;">
              Project: <?= h($msg['project_title']) ?>
            </div>
          <?php endif; ?>

          <!-- Sender + timestamp -->
          <div class="message-meta" style="font-size:0.9rem;color:#555;">
            From:
            <strong><?= h($msg['sender_name'] ?? 'Unknown') ?></strong>
            <?php if (!empty($msg['sender_email'])): ?>
              (<?= h($msg['sender_email']) ?>)
            <?php endif; ?>
            ·
            <span><?= h($msg['created_at']) ?></span>

            <!-- Show unread label -->
            <?php if (!(int) $msg['is_read']): ?>
              <span
                style="display:inline-block;margin-left:8px;padding:2px 8px;border-radius:999px;background:#ffeb3b;font-size:0.8rem;">
                Unread
              </span>
            <?php endif; ?>
          </div>

        </header>

        <!-- Body text -->
        <p class="message-body" style="margin:0;white-space:pre-wrap;">
          <?= nl2br(h($msg['body'])) ?>
        </p>

      </article>
    <?php endforeach; ?>
  </div>

<?php endif; ?>

<?php
// Close page layout
require dirname(__DIR__) . '/includes/footer.php';
?>