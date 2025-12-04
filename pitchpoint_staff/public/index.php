<?php
declare(strict_types=1);

//  DB connection
require __DIR__ . '/../config/db.php';

// firewall
require_once __DIR__ . '/../../auth/waf/theFire.php';

/**
 * Small escaping helper 
 */
function esc(string $value = ''): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$pdo = db();

// read filters from query string 
$q     = trim($_GET['q'] ?? '');
$catId = (int)($_GET['cat'] ?? 0);

//  fetch categories for the dropdown 
$catStmt = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

//  fetch public + published projects  
$params = [];

$sql = "
  SELECT 
    p.project_id,
    p.title,
    p.summary,
    p.stage,
    p.budget,
    p.category_id,
    c.category_name,
    e.company_name,
    u.name AS owner_name,
    p.created_at,
    pf.storage_path AS cover_image
  FROM projects p
  JOIN entrepreneurs e ON p.entrepreneur_id = e.entrepreneur_id
  JOIN users u ON e.user_id = u.user_id
  LEFT JOIN categories c 
         ON p.category_id = c.category_id
  LEFT JOIN project_files pf 
         ON pf.project_id = p.project_id
        AND pf.storage_path LIKE 'uploads/cover_image%'  -- only cover images
  WHERE p.status = 'published'
    AND p.visibility = 'public'
";

if ($q !== '') {
    $sql .= " AND (
                p.title   LIKE :q1
             OR p.summary LIKE :q2
             OR p.problem LIKE :q3
             OR p.solution LIKE :q4
             OR c.category_name LIKE :q5
            )";

    $like = '%' . $q . '%';

    $params[':q1'] = $like;
    $params[':q2'] = $like;
    $params[':q3'] = $like;
    $params[':q4'] = $like;
    $params[':q5'] = $like;
}


if ($catId > 0) {
    $sql .= " AND p.category_id = :cat";
    $params[':cat'] = $catId;
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PitchPoint · Explore Projects</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--  the guest css -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!--  the header -->
  <header class="topbar">
    <div class="topbar-row">

      <!-- Brand logo -->
      <a class="brand" href="index.php" aria-label="PitchPoint home">
        <img src="css/pitchpointlogo.png" alt="PitchPoint Logo" class="logo"
             onerror="this.style.display='none'">
        <span>PITCHPOINT</span>
      </a>

      <!-- for the search and the category filter-->
      <form class="site-search" role="search" method="get">
        <svg class="search-ico" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M10.5 3a7.5 7.5 0 015.93 12.2l4.19 4.19-1.4 1.41-4.2-4.2A7.5 7.5 0 1110.5 3zm0 2a5.5 5.5 0 100 11 5.5 5.5 0 000-11z" />
        </svg>
        <input
          type="search"
          name="q"
          placeholder="Search projects and categories"
          value="<?= esc($q) ?>"
          aria-label="Search projects and categories"
        >

        <select name="cat" class="cat-filter" aria-label="Filter by category">
          <option value="0">All categories</option>
          <?php foreach ($categories as $cat): ?>
            <?php $cid = (int)$cat['category_id']; ?>
            <option value="<?= $cid ?>" <?= $cid === $catId ? 'selected' : '' ?>>
              <?= esc($cat['category_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="search-btn">Search</button>
      </form>

      <!-- Right-side actions -->
      <div class="actions">
        <a class="btn-primary sm" href="/pitchPoint/auth/signUp.php">Sign up</a>
        <a class="btn-outline sm" href="/pitchPoint/auth/login.php">Log in</a>
      </div>

    </div>
  </header>

  <!-- ======= MAIN CONTENT:========= -->
  <main class="page-main">

    <section class="hero">
      <h1>Explore Projects</h1>
      <p class="section-subtitle">
        Discover ideas shared by entrepreneurs. Save projects you like and come back later.
      </p>
    </section>

    <!-- PROJECT GRID -->
    <?php if (empty($projects)): ?>
      <p><em>No projects found for this filter.</em></p>
    <?php else: ?>
      <div class="project-grid">
        <?php foreach ($projects as $p): ?>
          <?php
            $id      = (int)$p['project_id'];
            $title   = esc($p['title']);
            $sum     = esc($p['summary'] ?? '');
            $catName = esc($p['category_name'] ?? '');
            $stage   = esc($p['stage'] ?? '');

            // cover image path from DB 
            // Need to point to pitchpoint_entrepreneur/uploads directory
            $coverRaw = $p['cover_image'] ?? '';
            $cover = '';
            if ($coverRaw !== '') {
              // Convert relative path to absolute path pointing to entrepreneur uploads
              $cover = '/pitchPoint/pitchpoint_entrepreneur/' . esc($coverRaw);
            }
          ?>
          <article class="project-card">

            <header>
              <h3><?= $title ?></h3>
              <?php if ($catName): ?>
                <span class="badge"><?= $catName ?></span>
              <?php endif; ?>
            </header>

            <!--  COVER IMAGE / PLACEHOLDER -->
            <div class="project-thumb">
              <?php if ($cover): ?>
                <img src="<?= $cover ?>" alt="<?= $title ?> cover image">
              <?php else: ?>
                <div class="project-thumb-placeholder"></div>
              <?php endif; ?>
            </div>

            <?php if ($sum): ?>
              <p class="summary"><?= $sum ?></p>
            <?php endif; ?>

            <p class="meta">
              <?php if ($stage): ?>
                <span class="tag">Stage: <?= $stage ?></span>
              <?php endif; ?>
            </p>

            <!-- View / Invest / Interest -->
            <div class="actions">
              <!-- VIEW DETAILS -->
              <a href="project.php?id=<?= $id ?>" class="btn btn-light">
                View details
              </a>

              <!-- INVEST - links to signup page -->
              <a href="/pitchPoint/auth/signUp.php?project_id=<?= $id ?>" class="btn btn-primary">
                Invest
              </a>

             
            </div>

          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    </section>
  </main>

  <!-- footer part -->
  <?php include __DIR__ . '/../app/views/partials/footer.php'; ?>

</body>
</html>
