<?php
session_start();
require_once __DIR__ . '/../pitchpoint_admin/config/database.php';

$txn = null;
$project = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    // Sanitize the transaction ID
    $txn_id = intval($_POST['transaction_id']);
    
    // Server-side validation of payment data (security check)
    // Note: Payment method is stored in the investment record, fetch it for validation
    $stmt = db()->prepare("
        SELECT i.payment_method, t.transaction_id 
        FROM transactions t
        JOIN investments i ON t.investment_id = i.investment_id
        WHERE t.transaction_id = ?
    ");
    $stmt->execute([$txn_id]);
    $paymentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$paymentInfo) {
        die("Transaction not found.");
    }
    
    $payment_method = $paymentInfo['payment_method'] ?? '';
    $validationErrors = [];
    
    // Validate payment data based on method
    if ($payment_method === 'card') {
        $name = trim($_POST['name_on_card'] ?? '');
        $card = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
        $expiry = trim($_POST['expiry'] ?? '');
        $cvv = trim($_POST['cvv'] ?? '');
        
        if (empty($name) || strlen($name) < 2 || strlen($name) > 50) {
            $validationErrors[] = 'Invalid name on card.';
        }
        if (!preg_match('/^[A-Za-z\s]+$/', $name)) {
            $validationErrors[] = 'Invalid name format.';
        }
        if (!preg_match('/^\d{13,19}$/', $card)) {
            $validationErrors[] = 'Invalid card number.';
        }
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
            $validationErrors[] = 'Invalid expiry format.';
        }
        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            $validationErrors[] = 'Invalid CVV.';
        }
    } elseif ($payment_method === 'bank') {
        $account = preg_replace('/\s+/', '', $_POST['account_number'] ?? '');
        $routing = preg_replace('/\s+/', '', $_POST['routing_number'] ?? '');
        
        if (!preg_match('/^\d{8,20}$/', $account)) {
            $validationErrors[] = 'Invalid account number.';
        }
        if (!preg_match('/^\d{6,9}$/', $routing)) {
            $validationErrors[] = 'Invalid routing/sort code.';
        }
    } elseif ($payment_method === 'wallet') {
        $wallet = trim($_POST['wallet_address'] ?? '');
        
        if (strlen($wallet) < 20 || strlen($wallet) > 64 || !preg_match('/^[A-Za-z0-9]+$/', $wallet)) {
            $validationErrors[] = 'Invalid wallet address.';
        }
    } elseif ($payment_method === 'other') {
        $details = trim($_POST['payment_details'] ?? '');
        
        if (strlen($details) < 10 || strlen($details) > 500) {
            $validationErrors[] = 'Invalid payment details.';
        }
    }
    
    // If validation fails, mark transaction as failed
    if (!empty($validationErrors)) {
        $stmt = db()->prepare("UPDATE transactions SET transaction_status = 'failed' WHERE transaction_id = ?");
        $stmt->execute([$txn_id]);
        header('Location: failed.php');
        exit;
    }

    // Update transaction status safely (only if validation passed)
    $stmt = db()->prepare("UPDATE transactions SET transaction_status = 'succeeded' WHERE transaction_id = ?");
    $stmt->execute([$txn_id]);

    // Fetch the transaction with project information
    $stmt = db()->prepare("
        SELECT t.*, p.title as project_title, p.project_id 
        FROM transactions t
        LEFT JOIN projects p ON p.project_id = t.project_id
        WHERE t.transaction_id = ?
    ");
    $stmt->execute([$txn_id]);
    $txn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($txn && $txn['project_id']) {
        $stmt = db()->prepare("SELECT title FROM projects WHERE project_id = ?");
        $stmt->execute([$txn['project_id']]);
        $project = $stmt->fetch();
    }
}

// If transaction not found, show an error
if (!$txn) {
    die("Transaction not found or invalid ID.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Success</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container success">
    <h2>✅ Payment Successful!</h2>
    <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($txn['transaction_id']); ?></p>
    <p><strong>Investment ID:</strong> <?php echo htmlspecialchars($txn['investment_id']); ?></p>
    <?php if ($project): ?>
        <p><strong>Project:</strong> <?php echo htmlspecialchars($project['title']); ?></p>
    <?php endif; ?>
    <p><strong>Amount:</strong> $<?php echo number_format((float)$txn['transaction_amount'], 2); ?></p>
    <p><strong>Date:</strong> <?php echo date('F j, Y g:i A', strtotime($txn['transaction_date'])); ?></p>
    <a href="/pitchPoint/pitchpoint_investor/index/investorindex.php?url=investor/investments" class="btn-primary">View My Investments</a>
</div>
</body>
</html>
