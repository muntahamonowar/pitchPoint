<?php
/** @var string $title   Title of the page ("Compose email") */
/** @var array  $data    Previously submitted form values (to repopulate inputs) */
/** @var array  $errors  Validation errors for each field */
?>

<!-- Page heading -->
<h1 class="page-title"><?= esc($title); ?></h1>

<!-- Back link to return to the email list -->
<a href="index.php?c=emails&a=index" class="email-back-link">
    &larr; Back to email list
</a>

<!-- Main card container for compose form -->
<section class="card" style="margin-top:1rem; max-width: 640px;">

    <!-- The form uses POST and submits to the same compose action -->
    <form method="post" action="index.php?c=emails&a=compose" novalidate>

        <!-- ============================
             TO (Recipient email)
             ============================ -->
        <div class="form-group">
            <label for="to">To</label>

            <!-- Email input field -->
            <input
                type="email"
                id="to"
                name="to"
                value="<?= esc($data['to'] ?? ''); ?>"  <!-- Keep old value after validation error -->
                required
            >

            <!-- Error message under "To" field -->
            <?php if (!empty($errors['to'])): ?>
                <p class="form-error"><?= esc($errors['to']); ?></p>
            <?php endif; ?>
        </div>


        <!-- ============================
             SUBJECT
             ============================ -->
        <div class="form-group">
            <label for="subject">Subject</label>

            <!-- Text input for email subject -->
            <input
                type="text"
                id="subject"
                name="subject"
                value="<?= esc($data['subject'] ?? ''); ?>"  <!-- Keep old value -->
                required
            >

            <!-- Error for subject -->
            <?php if (!empty($errors['subject'])): ?>
                <p class="form-error"><?= esc($errors['subject']); ?></p>
            <?php endif; ?>
        </div>


        <!-- ============================
             EMAIL BODY / MESSAGE
             ============================ -->
        <div class="form-group">
            <label for="body">Message</label>

            <!-- Textarea for email message -->
            <textarea
                id="body"
                name="body"
                rows="8"
                required
            ><?= esc($data['body'] ?? ''); ?></textarea>

            <!-- Error under message textarea -->
            <?php if (!empty($errors['body'])): ?>
                <p class="form-error"><?= esc($errors['body']); ?></p>
            <?php endif; ?>
        </div>


        <!-- ============================
             SUBMIT BUTTON
             ============================ -->
        <button type="submit" class="btn btn-primary">
            Send email
        </button>

    </form>
</section>
