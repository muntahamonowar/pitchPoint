<?php
// Display any flash messages that were set during the previous request.
// Flash messages are stored in the session and removed once shown.

$flashes = flash_consume(); // Get messages and clear them from session

if ($flashes): ?>
  <div class="flash-wrap">
    <?php foreach ($flashes as $type => $msgs): ?>
      <?php foreach ($msgs as $m): ?>
        <!-- Each message gets a class based on its type (success, error, info, etc.) -->
        <div class="flash <?= h($type) ?>">
          <?= h($m) ?>
        </div>
      <?php endforeach ?>
    <?php endforeach ?>
  </div>
<?php endif; ?>