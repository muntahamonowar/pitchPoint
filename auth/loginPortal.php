<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$HOME  = 'login.php';
$STAFF = 'loginStaff.php';
$ADMIN = 'loginAdmin.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login Portal - PitchPoint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<section class="chooser">
  <h1>PitchPoint</h1>
  <p class="muted">Choose who you want to sign in as.</p>

  <div class="cards">
    <!-- Users Card -->
    <div class="card">
      <h2>Users</h2>
      <p>Go to user log in</p>
      <div class="actions">
        <a class="btn" href="<?= $HOME ?>">Click here</a>
      </div>
    </div>

    <!-- Staff Card -->
    <div class="card">
      <h2>Staff</h2>
      <p>Log in here</p>
      <div class="actions">
        <a class="btn" href="<?= $STAFF ?>">Staff portal</a>
      </div>
    </div>

    <!-- Admin Card -->
    <div class="card">
      <h2>Admin Portal</h2>
      <p>For administrators.</p>
      <div class="actions">
        <a class="btn" href="<?= $ADMIN ?>">Admin portal</a>
      </div>
    </div>
  </div>
</section>


<footer>
            <div class="footer-content">

                <img src="logo.png" alt="logo" width="100px" height="100px">

                <h3>Contact Us</h3>

                <ul class="socials">
                    <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                    <li><a href="#"><i class="fa fa-youtube"></i></a></li>
                    <li><a href="#"><i class="fa fa-linkedin-square"></i></a></li>
                </ul>
                <p>All of the content are strictly copyrighted. <br> DO NOT USE WITHOUT THE WRITTEN CONSENT OF THE OWNER</p>
            </div>
            <div class="footer-bottom">
                <p>copyright &copy;2025. Designed by <span>PHOENIX</span></p>
            </div>
</footer>
</body>
</html>