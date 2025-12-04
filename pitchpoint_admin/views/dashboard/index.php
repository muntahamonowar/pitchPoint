<?php /** @var string $title */ ?>
<!-- Page title passed from DashboardController -->
<h1 class="page-title"><?= esc($title); ?></h1>


<!-- ============================================================
     KPI (Key Performance Indicator) CARDS SECTION
     Shows small summary boxes at the top of the dashboard
     ============================================================ -->
<section aria-label="Key metrics">
    <div class="cards">

        <!-- Total number of all registered users -->
        <div class="card">
            <strong>Total Users</strong>
            <?= (int)$totalUsers; ?>
        </div>

        <!-- Total projects and how many of them are published -->
        <div class="card">
            <strong>Projects</strong>
            <?= (int)$totalProjects; ?> (<?= (int)$publishedProjects; ?> published)
        </div>

        <!-- Total number of investments in the system -->
        <div class="card">
            <strong>Investments</strong>
            <?= (int)$totalInvestments; ?>
        </div>

        <!-- Total invested amount, formatted with 2 decimals -->
        <div class="card">
            <strong>Total amount invested</strong>
            DKK <?= number_format($totalAmount, 2); ?>
        </div>

    </div>
</section>


<!-- ============================================================
     RECENT ADMIN ACTIVITY TABLE
     Shows the latest actions performed by administrators
     ============================================================ -->
<section aria-label="Recent admin activity">
    <div class="table-wrapper">
        <table>
            <caption>Recent activity</caption>

            <!-- Table Headings -->
            <thead>
            <tr>
                <th>ID and Name</th>        <!-- Shows admin ID + admin name -->
                <th>Description</th>        <!-- What action was done -->
                <th>Status</th>             <!-- Success / Warning / Error -->
                <th>Time</th>               <!-- When the action happened -->
            </tr>
            </thead>

            <tbody>
            <!-- If there are activity records -->
            <?php if (!empty($recentActivities)): ?>

                <!-- Loop through each activity row -->
                <?php foreach ($recentActivities as $act): ?>
                    <tr>

                        <!-- Admin identity -->
                        <td>
                            <?php if (!empty($act['admin_id'])): ?>
                                <!-- Show admin ID and admin name if available -->
                                #<?= (int)$act['admin_id']; ?>
                                <?= esc($act['admin_name'] ?? ''); ?>
                            <?php else: ?>
                                <!-- If admin_id is missing, treat as system entry -->
                                System
                            <?php endif; ?>
                        </td>

                        <!-- Activity description from activity_log -->
                        <td><?= esc($act['activity_description']); ?></td>

                        <!-- Status badge (success, warning, danger) -->
                        <td>
                            <span class="status-pill
                                <?= $act['status'] === 'Success' ? 'status-success'
                                   : ($act['status'] === 'Warning' ? 'status-warning' : 'status-danger'); ?>">
                                <?= esc($act['status']); ?>
                            </span>
                        </td>

                        <!-- Activity timestamp -->
                        <td><?= esc($act['logged_at']); ?></td>
                    </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <!-- Fallback if no activity exists -->
                <tr><td colspan="4">No activity logged yet.</td></tr>
            <?php endif; ?>
            </tbody>

        </table>
    </div>
</section>
