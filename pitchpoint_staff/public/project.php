<?php
declare(strict_types=1);
require __DIR__ . '/../config/db.php';


$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch a single, public + published project
$project = null;
if ($id > 0) {
  $sql = "
    SELECT 
      p.project_id,
      p.title,
      p.summary,
      p.problem,
      p.solution,
      p.stage,
      p.budget,
      p.visibility,
      p.status,
      p.created_at,
      c.category_name,
      e.company_name,
      u.name AS owner_name
    FROM projects p
    JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id
    JOIN users u ON e.user_id = u.user_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.project_id = :id
      AND p.visibility = 'public'
      AND p.status = 'published'
    LIMIT 1
  ";
  $stmt = db()->prepare($sql);
  $stmt->execute([':id' => $id]);
  $project = $stmt->fetch();
}

$title = $project ? $project['title'] : 'Project not found';
http_response_code($project ? 200 : 404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($title); ?> · PitchPoint</title>
  <link rel="stylesheet" href="css/project.css" />
</head>
<body>

  <!-- Top bar -->
  <header class="topbar">
    <div class="topbar-row">
      <a class="brand" href="index.php">PitchPoint</a>
      <div class="actions">
        <a class="btn-outline sm" href="index.php#projects">Back to home</a>
      </div>
    </div>
  </header>

  <main id="main-content" class="project-section" style="max-width:900px;margin:40px auto;">
    <?php if (!$project): ?>
      <article class="project-card">
        <h1>Project not found</h1>
        <p style="color:var(--muted)">The project you’re looking for doesn’t exist or isn’t public.</p>
      </article>
    <?php else: ?>
      <article class="project-card">
        <h1 style="margin-bottom:6px;"><?php echo htmlspecialchars($project['title']); ?></h1>
        <p style="color:var(--muted);margin-bottom:14px;">
          <strong>Owner:</strong>
          <?php echo htmlspecialchars($project['owner_name'] ?? 'Unknown'); ?>
          <?php if (!empty($project['company_name'])): ?>
            · <?php echo htmlspecialchars($project['company_name']); ?>
          <?php endif; ?>
          <?php if (!empty($project['category_name'])): ?>
            · <?php echo htmlspecialchars($project['category_name']); ?>
          <?php endif; ?>
        </p>

        <?php if (!empty($project['summary'])): ?>
          <section class="desc" style="margin-top:8px;">
            <h2>Summary</h2>
            <p><?php echo nl2br(htmlspecialchars($project['summary'])); ?></p>
          </section>
        <?php endif; ?>

        <?php if (!empty($project['problem'])): ?>
          <section class="desc" style="margin-top:8px;">
            <h2>Problem</h2>
            <p><?php echo nl2br(htmlspecialchars($project['problem'])); ?></p>
          </section>
        <?php endif; ?>

        <?php if (!empty($project['solution'])): ?>
          <section class="desc" style="margin-top:8px;">
            <h2>Solution</h2>
            <p><?php echo nl2br(htmlspecialchars($project['solution'])); ?></p>
          </section>
        <?php endif; ?>

        <section class="meta" style="margin-top:12px;color:var(--muted);font-size:.95rem;">
          <p>
            <strong>Stage:</strong> <?php echo htmlspecialchars($project['stage']); ?>
            <?php if ($project['budget'] !== null && $project['budget'] !== ''): ?>
              · <strong>Budget:</strong> $<?php echo number_format((float)$project['budget'], 2); ?>
            <?php endif; ?>
          </p>
          <p>
            <strong>Published:</strong> <?php echo htmlspecialchars((string)$project['created_at']); ?>
          </p>
        </section>

        <section class="cta-panel" style="margin-top:20px;">
          <h3>Interested in this project?</h3>
          <p>Sign up to follow updates, express interest and invest in the project.</p>
          <div class="row gap" style="display:flex;gap:10px;flex-wrap:wrap;">
            <a class="btn-primary" href="/pitchPoint/auth/signUp.php">Sign Up</a>
            <a class="btn" href="/pitchPoint/auth/login.php">Login</a>
          </div>
        </section>
      </article>
    <?php endif; ?>
  </main>

  <footer>
     <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <div class="footer-content">
      <img src="logo.png" alt="logo" width="100" height="100">
      <h3>Contact Us</h3>
      <ul class="socials">
        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
        <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
        <li><a href="#"><i class="fa fa-youtube"></i></a></li>
        <li><a href="#"><i class="fa fa-linkedin-square"></i></a></li>
      </ul>
      <p>All of the content are strictly copyrighted. <br> DO NOT USE WITHOUT THE WRITTEN CONSENT OF THE OWNER</p>
    </div>
    <div class="footer-bottom">
      <p>copyright &copy;2025. Designed by <span>PHOENIX</span></p>
    </div>
  </footer>

</body>
</html>

