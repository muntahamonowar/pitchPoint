<?php
session_start();

require_once __DIR__ . '/helpers/csrf.php';
require_once __DIR__ . '/model/userModel.php';   
require_once __DIR__ . '/controller/userController.php'; 
require_once __DIR__ . '/waf/theFire.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login - PitchPoint</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<div class="login-container">
    <h1>Login</h1>

    <?php if (!empty($_GET['err'])): ?>
        <div class="message error">
            <?php
                $err = $_GET['err'];
                if ($err === 'csrf') echo 'Security token mismatch. Please try again.';
                elseif ($err === 'invalid') echo 'Please enter valid email and password.';
                elseif ($err === 'unverified') echo 'Your account is not verified. Check your email.';
                elseif ($err === 'ratelimited') echo 'Too many login attempts. Please wait a minute before trying again.';
                else echo 'Invalid credentials.';
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['verify'])): ?>
        <div class="message info">
            <?php
                switch ($_GET['verify']) {
                    case 'success': echo "Your email is verified. You may log in."; break;
                    case 'already': echo "Your account is already verified."; break;
                    case 'invalid': echo "Invalid or expired verification link."; break;
                }
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['bye'])): ?>
        <div class="message info">You have been logged out.</div>
    <?php endif; ?>

    <?php if (!empty($_GET['ok'])): ?>
        <div class="message success">Account created! Check MailHog for verification email.</div>
    <?php endif; ?>

    <form method="post" action="/pitchPoint/pitchpoint_investor/index/investorindex.php?action=login">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCSRFToken()) ?>">

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn">Login</button>
    </form>

    <p style="text-align:center;margin-top:1rem;color:#666;">
        No account? <a href="signUp.php">Create one</a>
    </p>
</div>
</body>
</html>
