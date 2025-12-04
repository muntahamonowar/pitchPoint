<?php $title = "Investor Profile"; ?>

<h1>My Profile</h1>

<?php if (!empty($message)): ?>
    <p style="color: green;"><?= esc($message) ?></p>
<?php endif; ?>

<div class="form">
    <div class="row">
        <label>Name</label>
        <p><?= esc($user['name'] ?? '') ?></p>
    </div>

    <div class="row">
        <label>Email</label>
        <p><?= esc($user['email'] ?? '') ?></p>
    </div>
</div>

<?php if (!empty($editing)): ?>

    <!-- EDIT MODE: textarea + Save + Cancel -->
    <form action="?url=investor/profile&edit=1" method="post" class="form">
        <div class="row">
            <label>Bio / Notes</label>
            <textarea name="bio" rows="4"><?= esc($user['bio'] ?? '') ?></textarea>
        </div>

        <button type="submit">Save</button>
        <a href="?url=investor/profile" class="link">Cancel</a>
    </form>

<?php else: ?>

    <!-- VIEW MODE: bio as text + Edit bio button -->
    <div class="form">
        <div class="row">
            <label>Bio / Notes</label>
            <?php if (!empty($user['bio'])): ?>
                <p><?= esc($user['bio']) ?></p>
            <?php else: ?>
                <p><em>No bio added yet.</em></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit bio as a button -->
    <form action="" method="get" style="display:inline-block;">
        <input type="hidden" name="url" value="investor/profile">
        <input type="hidden" name="edit" value="1">
        <button type="submit" class="btn">Edit bio</button>
    </form>

<?php endif; ?>

<hr>

<h2>Interested Projects</h2>

<?php if (!empty($interested)): ?>
    <div class="cards">
        <?php foreach ($interested as $proj): ?>
            <article class="card">
                <h3><?= esc($proj['title'] ?? 'Untitled project') ?></h3>

                <?php if (!empty($proj['summary'])): ?>
                    <p><?= esc($proj['summary']) ?></p>
                <?php endif; ?>

                <?php if (function_exists('route') && !empty($proj['project_id'])): ?>
                    <a href="<?= route('project/show/' . $proj['project_id']) ?>" class="link">
                        View details
                    </a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p><em>You have no interested projects yet.</em></p>
<?php endif; ?>

<br>

<a href="<?= route('investor/dashboard') ?>" class="link">Back to Investor page</a>
