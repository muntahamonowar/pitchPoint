<?php 
session_start();
require_once __DIR__ . '/../pitchpoint_admin/config/database.php';
// FIXED: Correct path to CSRF helper (located in auth folder, not payment folder)
require_once __DIR__ . '/../auth/helpers/csrf.php';
// FIXED: Correct path to WAF (located in auth folder, not payment folder)
require_once __DIR__ . '/../auth/waf/theFire.php';

// Check if user is logged in
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: /pitchPoint/auth/login.php?redirect=payment');
    exit;
}

// Verify user is an investor
$stmt = db()->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
if (!$user || strtolower(trim($user['role'] ?? '')) !== 'investor') {
    die("Only investors can make investments.");
}

// Get project_id from URL
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

if (!$project_id) {
    die("Invalid project.");
}

// Verify project exists
$stmt = db()->prepare("SELECT project_id, title FROM projects WHERE project_id = ? AND status = 'published'");
$stmt->execute([$project_id]);
$project = $stmt->fetch();

if (!$project) {
    die("Project not found or not available.");
}

// Get or create investor record
$stmt = db()->prepare("SELECT investor_id FROM investors WHERE user_id = ?");
$stmt->execute([$userId]);
$investor = $stmt->fetch();

if (!$investor) {
    // Create investor record if it doesn't exist
    $stmt = db()->prepare("INSERT INTO investors (user_id, created_at) VALUES (?, NOW())");
    $stmt->execute([$userId]);
    $investor_id = db()->lastInsertId();
} else {
    $investor_id = $investor['investor_id'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invest in Project</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h2>Invest in: <?php echo htmlspecialchars($project['title']); ?></h2>
    <form method="POST" action="gateway.php">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        <input type="hidden" name="investor_id" value="<?php echo $investor_id; ?>">

        <label>Investment Amount ($):</label>
        <input type="number" name="amount" step="0.01" min="0.01" required>

        <label>Payment Method:</label>
        <select name="payment_method" required>
            <option value="card">Credit Card</option>
            <option value="bank">Bank Transfer</option>
            <option value="wallet">Wallet</option>
            <option value="other">Other</option>
        </select>

        <button type="submit">Continue to Payment</button>
    </form>
</div>
<script src="assets/script.js"></script>
</body>
</html>
