<?php $title = "Explore Projects"; ?>

<h1>Explore Projects</h1>

<section class="filter-bar">
  <form method="get" action="" class="filter-form">
    <input type="hidden" name="url" value="project/explore">

    <div class="row">
      <label for="q">Search</label>
      <input
        type="text"
        id="q"
        name="q"
        value="<?= esc($q ?? '') ?>"
        placeholder="Search by title, summary, problem..."
      >
    </div>

    <div class="row">
      <label for="cat">Category</label>
      <select id="cat" name="cat">
        <option value="0">All categories</option>

        <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $cat): ?>
            <option
              value="<?= (int)$cat['category_id'] ?>"
              <?= (!empty($catId) && (int)$catId === (int)$cat['category_id']) ? 'selected' : '' ?>
            >
              <?= esc($cat['category_name']) ?>
            </option>
          <?php endforeach; ?>
        <?php endif; ?>
      </select>
    </div>

    <button type="submit">Apply</button>
  </form>
</section>

<section id="projects" class="project-section">
  <h2>Ongoing Projects</h2>
  <p class="section-subtitle">Discover ideas shared by entrepreneurs.</p>

  <?php if (empty($projects)): ?>
    <p><em>No projects found for this filter.</em></p>
  <?php else: ?>
    <div class="project-grid">
      <?php foreach ($projects as $p): ?>
        <?php
          $id    = (int)$p['project_id'];
          $title = esc($p['title']);
          $sum   = esc($p['summary']);
          $cat   = esc($p['category_name'] ?? '');
          $stage = esc($p['stage'] ?? '');
        ?>

        <article class="project-card">

          <?php if (!empty($p['cover_image'])): ?>
            <div class="project-image">
              <img
                src="/pitchPoint/pitchpoint_entrepreneur/<?= htmlspecialchars($p['cover_image'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= $title ?>"
                style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0; margin: -20px -20px 12px -20px;"
              >
            </div>
          <?php endif; ?>

          <header>
            <h3><?= $title ?></h3>
            <?php if ($cat): ?>
              <span class="badge"><?= $cat ?></span>
            <?php endif; ?>
          </header>

          <?php if ($sum): ?>
            <p class="summary"><?= $sum ?></p>
          <?php endif; ?>

          <p class="meta">
            <?php if ($stage): ?>
              <span class="tag">Stage: <?= $stage ?></span>
            <?php endif; ?>
          </p>

          <div class="actions">
            <!-- View details -->
            <a href="<?= route('project/show/' . $id) ?>" class="btn btn-light">
              View details
            </a>

            <!-- Invest -->
            <a href="/pitchPoint/payment/payment.php?project_id=<?= $id ?>" class="btn btn-primary">
              Invest
            </a>

            <!-- Interest -->
            <a href="<?= route('project/toggleInterest/' . $id) ?>" class="btn btn-outline">
              Interest
            </a>
          </div>

        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
