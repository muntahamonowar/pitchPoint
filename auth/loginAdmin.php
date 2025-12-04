<?php
session_start();

require_once __DIR__ . '/helpers/csrf.php';
require_once __DIR__ . '/waf/theFire.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Login - PitchPoint</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="login-container">
    <h1>Admin Login</h1>

    <?php if (!empty($_GET['err'])): ?>
        <div class="message error">
            <?php
                $err = $_GET['err'];
                if ($err === 'csrf') echo 'Security token mismatch. Please try again.';
                elseif ($err === 'invalid') echo 'Please enter valid email and password.';
                elseif ($err === 'bad') echo 'Invalid credentials.';
                elseif ($err === 'ratelimited') echo 'Too many login attempts. Please wait a minute before trying again.';
                else echo 'An error occurred.';
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['verify'])): ?>
        <div class="message info">
            <?php
                switch ($_GET['verify']) {
                    case 'success': echo "Your email is verified. You may log in."; break;
                    case 'invalid': echo "Invalid or expired verification link."; break;
                    case 'missing': echo "Verification token is missing."; break;
                }
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['bye'])): ?>
        <div class="message info">You have been logged out.</div>
    <?php endif; ?>

    <?php if (!empty($_GET['ok'])): ?>
        <div class="message success">Verification email sent! Check MailHog for verification email.</div>
    <?php endif; ?>

    <form method="post" action="/pitchPoint/pitchpoint_admin/index.php?c=auth&a=dologin">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCSRFToken()) ?>">

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn">Login</button>
    </form>
</div>
</body>
</html>
