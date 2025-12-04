<?php
require_once __DIR__ . '/../pitchpoint_admin/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    $txn_id = $_POST['transaction_id'];
    $stmt = db()->prepare("UPDATE transactions SET transaction_status = 'failed' WHERE transaction_id = ?");
    $stmt->execute([$txn_id]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container failed">
    <h2>Payment Failed</h2>
    <p>Your transaction has been marked as failed.</p>
    <a href="javascript:history.back()" class="btn-primary">Try Again</a>
</div>
</body>
</html>
