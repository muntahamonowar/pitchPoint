<?php
// signUp.php
session_start();
require_once __DIR__ . '/helpers/csrf.php';
require_once __DIR__ . '/waf/theFire.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sign Up - PitchPoint</title>
    <link rel="stylesheet" href="/pitchPoint/auth/login.css">
</head>
<body>

<div class="login-container">
    <h1>Create Account</h1>

    <?php if (!empty($_GET['ok'])): ?>
        <div class="message success">Account created! Check your Mail for verification email.</div>
    <?php elseif (!empty($_GET['err'])): ?>
        <div class="message error">
            <?php 
                $err = $_GET['err'];
                if ($err === 'exists') echo 'Email already registered.';
                elseif ($err === 'ratelimited') echo 'Too many registration attempts. Please wait 10 minutes before trying again.';
                elseif ($err === 'shortpass') echo 'Password must be at least 8 characters long.';
                else echo 'Please fill out all fields correctly.'; 
            ?>
        </div>
    <?php endif; ?>

    <form action="../pitchpoint_investor/index/investorindex.php?action=signup" method="post" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCSRFToken()) ?>">

        <div class="form-group">
            <label for="name">Full name</label>
            <input id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" autocomplete="off" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="entrepreneur">Entrepreneur</option>
                <option value="investor">Investor</option>
            </select>
        </div>

        <button type="submit" class="btn">Create Account</button>
    </form>

    <p style="text-align:center;margin-top:1rem;color:#666;">
        Have an account? <a href="login.php">Login</a>
    </p>
</div>

</body>
</html>
