<?php
/** @var array  $entrepreneurs */
/** @var array  $projects */
/** @var string $defaultAdminEmail */
/** @var int    $selectedProjectId */
/** @var int    $selectedReceiverId */
?>

<h1>Send a Message</h1>

<!-- ========== TO ENTREPRENEUR (messages table) ========== -->
<section class="box">
    <h2>Message an entrepreneur</h2>

    <form method="post" action="<?= route('message/sendEntrepreneur') ?>" class="simple-form">

        <div class="row">
            <label for="project_id">Regarding project</label>
            <select id="project_id" name="project_id" required>
                <option value="">-- Select project --</option>
                <?php foreach ($projects as $p): ?>
                    <?php
                        $pid = (int)$p['project_id'];

                        // Handle NULL / missing entrepreneur data
                        // Use entrepreneur_user_id from the query
                        $entrepreneurId = '';
                        if (isset($p['entrepreneur_user_id']) && $p['entrepreneur_user_id'] !== null && $p['entrepreneur_user_id'] !== '') {
                            $entrepreneurId = (int)$p['entrepreneur_user_id'];
                        }

                        // Get entrepreneur name - ensure we're using the correct field
                        $entrepreneurName = 'Unknown';
                        if (!empty($p['entrepreneur_name']) && $p['entrepreneur_name'] !== null) {
                            $entrepreneurName = htmlspecialchars($p['entrepreneur_name'], ENT_QUOTES, 'UTF-8');
                        }
                    ?>
                    <!-- If entrepreneur data is missing, data-entrepreneur-id will be empty string -->
                    <option
                        value="<?= $pid ?>"
                        data-entrepreneur-id="<?= $entrepreneurId ?>"
                        data-entrepreneur-name="<?= $entrepreneurName ?>"
                        data-project-id="<?= $pid ?>"
                        <?= $pid === $selectedProjectId ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($p['title'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Hidden field for entrepreneur - automatically set based on project -->
        <input type="hidden" id="receiver_user_id" name="receiver_user_id" value="">

        <!-- Display selected entrepreneur info -->
        <div class="row" id="entrepreneur-info" style="display: none; margin-bottom: 10px;">
            <p><b>Entrepreneur:</b> <span id="entrepreneur-name"></span></p>
        </div>

        <div class="row">
            <label for="body">Message</label>
            <textarea id="body" name="body" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Send to entrepreneur</button>
    </form>
</section>

<hr>

<!-- ========== TO ADMIN (email_management table) ========== -->
<section class="box">
    <h2>Message an admin</h2>

    <form method="post" action="<?= route('message/sendAdmin') ?>" class="simple-form">

        <!-- Hidden field - automatically sends to admin -->
        <input
            type="hidden"
            name="receiver_email"
            value="<?= htmlspecialchars($defaultAdminEmail ?? 'rafia@pitchpoint.com', ENT_QUOTES, 'UTF-8') ?>"
        >

        <div class="row">
            <label for="subject">Subject (optional)</label>
            <input type="text" id="subject" name="subject" placeholder="Subject">
        </div>

        <div class="row">
            <label for="body_admin">Message</label>
            <textarea id="body_admin" name="body_admin" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-secondary">Send to admin</button>
    </form>
</section>

<div style="margin-top: 1rem;">
    <a href="<?= route('message/inbox') ?>">Back to inbox</a>
</div>

<script>
// Auto-set entrepreneur when project is selected
document.getElementById('project_id').addEventListener('change', function() {
    const projectSelect       = this;
    const entrepreneurInput   = document.getElementById('receiver_user_id');
    const entrepreneurInfo    = document.getElementById('entrepreneur-info');
    const entrepreneurNameSpan = document.getElementById('entrepreneur-name');
    const selectedOption      = projectSelect.options[projectSelect.selectedIndex];

    if (selectedOption.value) {
        // Get data attributes - will be empty string if entrepreneur data is missing
        const entrepreneurId   = selectedOption.dataset.entrepreneurId || '';
        const entrepreneurName = selectedOption.dataset.entrepreneurName || 'Unknown';

        const hasValidEntrepreneur =
            entrepreneurId &&
            entrepreneurId !== '' &&
            entrepreneurId !== '0' &&
            entrepreneurName !== 'Unknown';

        if (hasValidEntrepreneur) {
            entrepreneurInput.value        = entrepreneurId;
            entrepreneurNameSpan.textContent = entrepreneurName;
            entrepreneurInfo.style.display = 'block';
        } else {
            // Handle projects without entrepreneur data gracefully
            entrepreneurInput.value        = '';
            entrepreneurNameSpan.textContent = '';
            entrepreneurInfo.style.display = 'none';

            console.log(
                'No entrepreneur data for project:',
                selectedOption.textContent,
                'ID:', entrepreneurId,
                'Name:', entrepreneurName
            );
        }
    } else {
        // Clear if no project selected
        entrepreneurInput.value        = '';
        entrepreneurNameSpan.textContent = '';
        entrepreneurInfo.style.display = 'none';
    }
});

// Trigger on page load if project is already selected
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    if (projectSelect.value) {
        projectSelect.dispatchEvent(new Event('change'));
    }
});
</script>
