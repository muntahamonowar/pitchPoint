<?php
// Setting page title so it shows up in the header
$TITLE = 'Create Project';

// This pulls in header, styles, nav, db connection, session etc.
require dirname(__DIR__).'/includes/header.php';

// Only logged-in users can be here. If not logged in, they get redirected.
require_login();

// Get database connection
$pdo = db();

// I’m putting categories in this array so I can use them in the dropdown later
$cats = [];
try {
  // Loading all categories from DB so I don’t hard-code them
  $cats = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name")->fetchAll();
} catch (Throwable $e) {
  // If something goes wrong, I just keep it empty.
  // Not perfect, but better than the whole page crashing.
  $cats = [];
}
?>
<h1 class="page-title">Create a Project</h1>

<!--
  Main project form.
  - action: sends data to my create_project action file
  - method: POST so data is not in the URL
  - enctype: needed because I upload files (image + PDF)
-->
<form action="<?= h(base_url('../actions/create_project.php')) ?>"
      method="post"
      enctype="multipart/form-data"
      class="form">

  <!-- CSRF token for security (prevents fake form submissions) -->
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

  <div class="grid">
    <!-- Project title. Required so user can’t submit empty stuff. -->
    <label>Title
      <input name="title"
             required
             maxlength="180"
             placeholder="e.g. Smart Irrigation Controller">
    </label>

    <!-- Short description shown in cards / listings -->
    <label>Short Summary
      <textarea name="summary"
                required
                maxlength="300"
                placeholder="Quick pitch in 1–2 sentences"></textarea>
    </label>

    <!-- What problem this project is trying to solve -->
    <label>Problem
      <textarea name="problem"
                required
                placeholder="Describe the real-life problem here"></textarea>
    </label>

    <!-- How this project solves that problem -->
    <label>Solution
      <textarea name="solution"
                required
                placeholder="Explain how your idea actually helps"></textarea>
    </label>

    <!-- Category select (data coming from the DB above) -->
    <label>Category
      <select name="category" required>
        <?php foreach ($cats as $cat): ?>
          <option value="<?= h($cat['category_id']) ?>">
            <?= h($cat['category_name']) ?>
          </option>
        <?php endforeach ?>
      </select>
    </label>

    <!-- Stage: what level the project is at -->
    <label>Stage
      <select name="stage" required>
        <option value="idea">Idea</option>
        <option value="prototype">Prototype</option>
        <option value="mvp">MVP</option>
        <option value="beta">Beta</option>
        <option value="launched">Launched</option>
      </select>
    </label>

    <!-- Budget for the project (optional, user can leave empty) -->
    <label>Estimated Budget (In USD)
      <input name="budget"
             type="number"
             step="0.01"
             min="0"
             placeholder="e.g. 25000">
    </label>

    <!-- Cover image upload to make the project card look nicer -->
    <label>Cover Image (jpg/png)
      <input name="cover_image"
             type="file"
             accept="image/*">
    </label>

    <!-- Pitch deck upload in PDF format -->
    <label>Project Proposal (PDF)
      <input name="project_proposal"
             type="file"
             accept="application/pdf">
    </label>

        <!-- Optional demo video for the project -->
    <!-- <label>Project Video (optional)
      <input name="project_video"
             type="file"
             accept="video/*">
    </label> -->

  </div>

  <div class="actions">
    <!-- Save as draft button (does not fully publish) -->
    <button class="btn" name="action" value="draft">Save Draft</button>

    <!-- Publish button (goes live if everything is valid) -->
    <button class="btn btn-primary" name="action" value="publish">Publish</button>
  </div>
</form>

<?php
// Footer to close the layout properly
require dirname(__DIR__).'/includes/footer.php';
?>
