<?php
$TITLE = 'Invite Investors';
require dirname(__DIR__).'/includes/header.php';
require_login();
?>
<h1 class="page-title">Invite Investors</h1>
<form class="form" method="post" action="#" onsubmit="alert('Coming soon'); return false;">
<label>Investor Email
<input type="email" placeholder="investor@example.com" required>
</label>
<label>Personal Note
<textarea rows="3" placeholder="Tell them why this is exciting…"></textarea>
</label>
<div class="actions"><button class="btn btn-primary" type="submit">Send Invite</button></div>
</form>
<?php require dirname(__DIR__).'/includes/footer.php';