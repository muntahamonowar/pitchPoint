<?php
// admin/views/projects/index.php

/** @var string $title */
// The page title passed from the controller.

/** @var array $projects */
// The list of projects returned after filtering.

/** @var string $status */
// Selected status filter (draft/published/archived).

/** @var string $search */
// The search term typed in by the admin.
?>

<!-- Page Title -->
<h1 class="page-title"><?= esc($title); ?></h1>


<!-- Search + Filters Form -->
<form method="get"
      action="index.php"
      aria-label="Filter projects">

    <!-- MVC routing parameters -->
    <input type="hidden" name="c" value="projects">
    <input type="hidden" name="a" value="index">

    <div class="filters-container">

        <!-- ===========================================
             LEFT SIDE — STATUS FILTER + APPLY BUTTON
             =========================================== -->
        <div class="main-filters">
            <div class="filter-row">

                <!-- Status dropdown -->
                <div class="filter-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <!-- If empty, shows all projects -->
                        <option value="" <?= $status === '' ? 'selected' : ''; ?>>All</option>

                        <!-- Draft filter -->
                        <option value="draft"
                            <?= $status === 'draft' ? 'selected' : ''; ?>>
                            Draft
                        </option>

                        <!-- Published filter -->
                        <option value="published"
                            <?= $status === 'published' ? 'selected' : ''; ?>>
                            Published
                        </option>

                        <!-- Archived filter -->
                        <option value="archived"
                            <?= $status === 'archived' ? 'selected' : ''; ?>>
                            Archived
                        </option>
                    </select>
                </div>

                <!-- Button to apply filters -->
                <div class="filter-group">
                    <button class="btn btn-secondary" type="submit">
                        Apply filters
                    </button>
                </div>

            </div>
        </div>


        <!-- ===========================================
             RIGHT SIDE — SEARCH BAR
             =========================================== -->
        <div class="search-form">

            <label for="search">Search</label>
            <!-- Screen reader accessible label -->

            <div class="search-group">

                <!-- Search textbox -->
                <input
                    id="search"
                    type="text"
                    name="search"
                    class="search-input"
                    value="<?= esc($search); ?>"
                    placeholder="Title, owner, company..."
                >

                <!-- Search button -->
                <button class="btn btn-primary search-btn" type="submit">
                    Search
                </button>

            </div>
        </div>

    </div>
</form>


<!-- ===========================================
     PROJECTS TABLE
     =========================================== -->
<div class="table-wrapper mt-2">
    <table>

        <caption>Projects</caption>
        <!-- Optional caption for accessibility -->

        <thead>
        <tr>
            <!-- Column headers -->
            <th scope="col">ID</th>
            <th scope="col">Title</th>
            <th scope="col">Owner</th>
            <th scope="col">Company</th>
            <th scope="col">Stage</th>
            <th scope="col">Status</th>
            <th scope="col">Visibility</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>

        <tbody>

        <!-- If project list is NOT empty -->
        <?php if (!empty($projects)): ?>

            <?php foreach ($projects as $project): ?>
                <tr>

                    <!-- Project ID -->
                    <td><?= (int)$project['project_id']; ?></td>

                    <!-- Title (links to project details page) -->
                    <td>
                        <a href="index.php?c=projects&amp;a=show&amp;id=<?= (int)$project['project_id']; ?>">
                            <?= esc($project['title']); ?>
                        </a>
                    </td>

                    <!-- Owner name -->
                    <td><?= esc($project['owner_name']); ?></td>

                    <!-- Company name -->
                    <td><?= esc($project['company_name']); ?></td>

                    <!-- Stage -->
                    <td><?= esc($project['stage']); ?></td>

                    <!-- Status pill -->
                    <td>
                        <span class="status-pill status-<?= esc($project['status']); ?>">
                            <?= esc($project['status']); ?>
                        </span>
                    </td>

                    <!-- Visibility (public/private) -->
                    <td><?= esc($project['visibility']); ?></td>

                    <!-- ==============================
                         ACTIONS: Approve / Reject / Delete
                         ============================== -->
                    <td>
                        <form method="post"
                              action="index.php?c=projects&amp;a=changeStatus"
                              class="inline">

                            <!-- Hidden project ID -->
                            <input type="hidden" name="project_id"
                                   value="<?= (int)$project['project_id']; ?>">

                            <!-- Dropdown for selecting admin action -->
                            <select name="action">
                                <option value="">Choose…</option>
                                <option value="approve">Approve</option>
                                <option value="reject">Reject</option>
                                <option value="delete">Delete</option>
                            </select>

                            <!-- Submit action -->
                            <button type="submit" class="btn-table btn-table-secondary">
                                Apply
                            </button>
                        </form>
                    </td>

                </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <!-- No projects found under this filter/search -->
            <tr>
                <td colspan="8">
                    No projects found for this filter.
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
</div>
