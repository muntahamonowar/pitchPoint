<?php
// public/signup.php

// I set the page title here so the header can display it dynamically.
$TITLE = 'Sign Up (Entrepreneur)';

// I included the main header file to keep the layout consistent on all pages.
require dirname(__DIR__) . '/includes/header.php';
?>

<h1 class="page-title">Create Account</h1>

<form class="form" action="<?= h(base_url('../actions/signup.php')) ?>" method="post">

  <?php
  // I added a CSRF token for security so nobody can submit this form from outside my site.
  ?>
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

  <!-- I added this field so the entrepreneur can enter their full name -->
  <label>Name
    <input name="name" maxlength="150" required>
  </label>

  <!-- Email is required because this will be used for login and notifications -->
  <label>Email
    <input type="email" name="email" maxlength="190" required>
  </label>

  <!-- Password field with a minimum length so users cannot set extremely weak passwords -->
  <label>Password
    <input type="password" name="password" minlength="8" required>
  </label>

  <!-- Company name is optional because not every entrepreneur has a registered company yet -->
  <label>Company (optional)
    <input name="company_name" maxlength="200">
  </label>

  <div class="actions">
    <!-- This button submits the signup form -->
    <button class="btn btn-primary" type="submit">Sign Up</button>

    <!-- This link is here in case the user already has an account -->
    <a class="btn" href="/pitchPoint/auth/login.php">Already have an account?</a>
  </div>
</form>

<?php
// I included the footer to close the page structure properly.
require dirname(__DIR__) . '/includes/footer.php';
?>
