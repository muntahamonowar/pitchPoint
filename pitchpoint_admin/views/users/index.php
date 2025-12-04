<?php
/** @var string $title */
// The page title passed from UsersController.

/** @var array  $users */
// Array of users returned from the model after filtering.

/** @var string $roleFilter */
/** @var string $statusFilter */
/** @var string $searchFilter */
// These hold the selected filter values so the UI can keep them selected.
?>

<!-- Display page heading -->
<h1 class="page-title"><?= esc($title); ?></h1>

<!-- Container for filters & search boxes -->
<div class="filters-container">

    <!-- ===========================
         MAIN FILTERS FORM (LEFT)
         =========================== -->
    <form method="get"
          action="index.php"
          aria-label="Filter users"
          class="main-filters">

        <!-- Required MVC route params -->
        <input type="hidden" name="c" value="users">
        <input type="hidden" name="a" value="index">

        <div class="filter-row">

            <!-- ROLE FILTER -->
            <div class="filter-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="">All</option>

                    <?php foreach (['entrepreneur','investor','staff','admin'] as $roleOpt): ?>
                        <option value="<?= $roleOpt; ?>"
                            <?= $roleFilter === $roleOpt ? 'selected' : ''; ?>>
                            <!-- ucfirst() capitalizes the first letter -->
                            <?= ucfirst($roleOpt); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- STATUS FILTER -->
            <div class="filter-group">
                <label for="active">Status</label>
                <select id="active" name="active">
                    <option value="">All</option>

                    <!-- Active users -->
                    <option value="1" <?= $statusFilter === '1' ? 'selected' : ''; ?>>
                        Active
                    </option>

                    <!-- Inactive users -->
                    <option value="0" <?= $statusFilter === '0' ? 'selected' : ''; ?>>
                        Inactive
                    </option>
                </select>
            </div>

            <!-- APPLY BUTTON -->
            <div class="filter-group">
                <button class="btn btn-secondary" type="submit">
                    Apply Filters
                </button>
            </div>
        </div>
    </form>

    <!-- ===========================
         SEARCH FORM (RIGHT)
         =========================== -->
    <form method="get"
          action="index.php"
          aria-label="Search users"
          class="search-form">

        <!-- Route parameters -->
        <input type="hidden" name="c" value="users">
        <input type="hidden" name="a" value="index">

        <!-- Keep filters when searching -->
        <?php if ($roleFilter): ?>
            <input type="hidden" name="role" value="<?= esc($roleFilter); ?>">
        <?php endif; ?>

        <?php if ($statusFilter): ?>
            <input type="hidden" name="active" value="<?= esc($statusFilter); ?>">
        <?php endif; ?>

        <div class="search-group">

            <!-- Screen-reader-only label -->
            <label for="search" class="sr-only">Search users</label>

            <!-- SEARCH TEXT BOX -->
            <input type="text"
                   id="search"
                   name="search"
                   placeholder="Search by name or email..."
                   value="<?= esc($searchFilter); ?>"
                   class="search-input">

            <!-- SEARCH BUTTON -->
            <button class="btn btn-primary search-btn" type="submit">
                Search
            </button>
        </div>
    </form>
</div>


<!-- ===========================
     USERS TABLE
     =========================== -->
<div class="table-wrapper">

    <table>
        <caption>Registered users</caption>

        <!-- Table headers -->
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>

        <!-- If we have any users -->
        <?php if (!empty($users)): ?>

            <?php foreach ($users as $u): ?>

                <?php
                // Determine if user is active based on last action.
                // This is your logic: last_action = 'activated'
                $isActive = ($u['last_action'] === 'activated');
                ?>

                <tr>

                    <!-- USER ID -->
                    <td><?= (int)$u['user_id']; ?></td>

                    <!-- USER NAME -->
                    <td><?= esc($u['name']); ?></td>

                    <!-- EMAIL (click to open email app) -->
                    <td>
                        <a href="mailto:<?= esc($u['email']); ?>">
                            <?= esc($u['email']); ?>
                        </a>
                    </td>

                    <!-- ROLE (capitalize) -->
                    <td><?= esc(ucfirst($u['role'])); ?></td>

                    <!-- ACTIVE / INACTIVE BADGE -->
                    <td>
                        <span class="status-pill <?= $isActive ? 'status-success' : 'status-danger'; ?>">
                            <?= $isActive ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>

                    <!-- ACTION FORM (activate, deactivate, delete) -->
                    <td>
                        <form class="inline"
                              method="post"
                              action="index.php?c=users&a=doAction">

                            <!-- CSRF protection -->
                            <input type="hidden" name="csrf_token"
                                   value="<?= esc(csrf_token()); ?>">

                            <!-- User ID -->
                            <input type="hidden" name="user_id"
                                   value="<?= (int)$u['user_id']; ?>">

                            <!-- Accessible label -->
                            <label class="sr-only"
                                   for="action-<?= (int)$u['user_id']; ?>">
                                Choose action
                            </label>

                            <!-- ACTION DROPDOWN -->
                            <select id="action-<?= (int)$u['user_id']; ?>"
                                    name="action">

                                <option value="">Choose…</option>

                                <!-- Activate option -->
                                <option value="activate"
                                    <?= $isActive ? 'disabled' : ''; ?>>
                                    Activate
                                </option>

                                <!-- Deactivate option -->
                                <option value="deactivate"
                                    <?= !$isActive ? 'disabled' : ''; ?>>
                                    Deactivate
                                </option>

                                <!-- Delete option -->
                                <option value="delete">Delete</option>

                            </select>

                            <!-- APPLY BUTTON -->
                            <button class="btn btn-secondary" type="submit">
                                Apply
                            </button>

                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <!-- NO USERS FOUND -->
            <tr>
                <td colspan="6">
                    <?php if ($roleFilter || $statusFilter || $searchFilter): ?>
                        No users found matching your criteria.
                    <?php else: ?>
                        No users found.
                    <?php endif; ?>
                </td>
            </tr>

        <?php endif; ?>
        </tbody>
    </table>

</div>
