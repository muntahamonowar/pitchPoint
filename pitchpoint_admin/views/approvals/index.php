<?php
/** @var string $title      Page title */
/** @var array  $approvals  List of approvals from controller */
/** @var string $decision   Current decision filter ('', 'Approved', 'Rejected', 'Pending') */
?>
<h1 class="page-title"><?= esc($title); ?></h1>

<!-- ============================================================
     FILTER FORM (Decision: All / Approved / Rejected / Pending)
     ============================================================ -->
<form method="get" action="index.php" aria-label="Filter approvals">
    <!-- Hidden fields to route the request to the approvals controller -->
    <input type="hidden" name="c" value="approvals">
    <input type="hidden" name="a" value="index">

    <label for="decision">Decision</label>
    <select id="decision" name="decision">
        <option value="">All</option>
        <?php foreach (['Approved','Rejected','Pending'] as $d): ?>
            <option value="<?= $d; ?>" <?= ($decision === $d) ? 'selected' : ''; ?>>
                <?= $d; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button class="btn btn-secondary" type="submit">
        Apply filters
    </button>
</form>

<!-- ============================================================
     TABLE: IDEA APPROVALS
     ============================================================ -->
<div class="table-wrapper" style="margin-top:1rem;">
    <table>
        <caption>Idea approvals</caption>

        <thead>
        <tr>
            <th>ID</th>
            <th>Project</th>
            <th>Decision</th>
            <th>Comments</th>
            <th>Date</th>
            <th>Dicission By</th>
            <th>Update</th>
        </tr>
        </thead>

        <tbody>
        <?php if (!empty($approvals)): ?>

            <?php foreach ($approvals as $row): ?>
                <?php
                // Decide if this row is pending
                $isPending = ($row['decision'] === 'Pending');

                // Neutral "approved by" display:
                //  - If decision is Pending → show '—' (or 'Pending')
                //  - Else if admin_name exists → show that name
                //  - Else → 'Unknown'
                if ($isPending) {
                    $approvedByDisplay = '—'; // neutral for pending
                } elseif (!empty($row['admin_name'])) {
                    $approvedByDisplay = $row['admin_name'];
                } else {
                    $approvedByDisplay = 'Unknown';
                }
                ?>
                <tr>
                    <!-- ID -->
                    <td><?= (int)$row['approval_id']; ?></td>

                    <!-- Project id + title -->
                    <td>
                        #<?= (int)$row['project_id']; ?> —
                        <?= esc($row['project_title']); ?>
                    </td>

                    <!-- Current decision -->
                    <td><?= esc($row['decision']); ?></td>

                    <!-- Comments -->
                    <td><?= esc($row['comments'] ?? ''); ?></td>

                    <!-- Decision date -->
                    <td><?= esc($row['approval_date'] ?? ''); ?></td>

                    <!-- Approved By (fixed to be neutral when Pending) -->
                    <td><?= esc($approvedByDisplay); ?></td>

                    <!-- UPDATE FORM (dropdown + Save button) -->
                    <td>
                        <form method="post"
                              action="index.php?c=approvals&a=update"
                              class="inline">

                            <!-- CSRF protection -->
                            <input type="hidden" name="csrf_token"
                                   value="<?= esc(csrf_token()); ?>">

                            <!-- Which approval we are updating -->
                            <input type="hidden" name="approval_id"
                                   value="<?= (int)$row['approval_id']; ?>">

                            <!-- Decision dropdown -->
                            <select name="decision">
                                <option value="Approved"
                                    <?= $row['decision'] === 'Approved' ? 'selected' : ''; ?>>
                                    Approved
                                </option>
                                <option value="Rejected"
                                    <?= $row['decision'] === 'Rejected' ? 'selected' : ''; ?>>
                                    Rejected
                                </option>
                                <option value="Pending"
                                    <?= $row['decision'] === 'Pending' ? 'selected' : ''; ?>>
                                    Pending
                                </option>
                            </select>

                            <!-- Optional comments textarea or input.
                                 If you want NO editing here, you can remove this field. -->
                            <input type="text"
                                   name="comments"
                                   placeholder="Update comments (optional)"
                                   value="<?= esc($row['comments'] ?? ''); ?>"
                                   style="margin-top:0.4rem; display:block; width:100%; max-width:260px;">

                            <!-- Save button -->
                            <button class="btn btn-secondary" type="submit" style="margin-top:0.4rem;">
                                Save
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php else: ?>
            <!-- No approvals found message -->
            <tr><td colspan="7">No approvals found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
