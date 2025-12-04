<?php
// Variables: $projects (array)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PitchPoint · Staff Dashboard</title>
  <link rel="stylesheet" href="/pitchPoint/pitchpoint_staff/public/css/style.css">
  <link rel="stylesheet" href="/pitchPoint/pitchpoint_staff/public/css/staff.css">


</head>
<body>
<header class="topbar">
    <div class="topbar-row">
        <!-- RIGHT: Staff label + logout -->
        <div class="staff-right">
            <span class="staff-role">PitchPoint Staff</span>
            <a class="btn-outline sm" href="/pitchPoint/pitchpoint_staff/public/logout.php">Logout</a>
        </div>

    </div>
</header>


<section class="project-section">
  <h1>Project Reviews</h1>

<nav class="status-tabs">
    <a href="staff.php"                         class="<?= $currentStatusFilter === 'pending_review' ? 'active' : '' ?>">Pending</a>
    <a href="staff.php?status=published"       class="<?= $currentStatusFilter === 'published'       ? 'active' : '' ?>">Published</a>
    <a href="staff.php?status=rejected"        class="<?= $currentStatusFilter === 'rejected'        ? 'active' : '' ?>">Rejected</a>
    

  <table border="0" cellspacing="0" cellpadding="8" style="width:100%;">
    <thead>
      <tr style="text-align:left;border-bottom:1px solid #ddd;">
        <th>#</th><th>Title</th><th>Owner</th><th>Stage</th>
        <th>Status</th><th>Created</th><th>Actions</th>
      </tr>
    </thead>
      <tbody>
<?php if (!empty($projects)): ?>
    <?php foreach ($projects as $idx => $p): ?>
        <?php $pid = (int)$p['project_id']; ?>
        <tr>
            <td><?= $idx + 1 ?></td>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td><?= htmlspecialchars($p['owner_name']) ?></td>
            <td><?= htmlspecialchars($p['stage']) ?></td>
            <td><?= htmlspecialchars($p['status']) ?></td>
            <td><?= htmlspecialchars(date('Y-m-d', strtotime($p['created_at']))) ?></td>
            <td>
            <td>
    <a href="/pitchPoint/pitchpoint_staff/public/staff-review.php?id=<?= $pid ?>">Review</a>
</td>

            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7" style="text-align:center; padding:16px;">
            No projects to review.
        </td>
    </tr>
<?php endif; ?>
</tbody>
</table>
</section>

</body>
</html>
