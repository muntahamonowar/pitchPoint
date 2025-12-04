<?php
session_start();
require_once __DIR__ . '/../pitchpoint_admin/config/database.php';
// FIXED: Correct path to CSRF helper (located in auth folder, not payment folder)
require_once __DIR__ . '/../auth/helpers/csrf.php';
// FIXED: Correct path to WAF (located in auth folder, not payment folder)
require_once __DIR__ . '/../auth/waf/theFire.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid access.");
}

// Check if user is logged in
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header('Location: /pitchPoint/auth/login.php?redirect=payment');
    exit;
}

$project_id = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
$investor_id = isset($_POST['investor_id']) ? (int)$_POST['investor_id'] : 0;
$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
$payment_method = $_POST['payment_method'] ?? '';

// Validate inputs
if (!$project_id || !$investor_id || $amount <= 0 || !$payment_method) {
    die("Invalid payment data.");
}

// Map payment method to database enum values
$db_payment_method = 'other';
if (in_array($payment_method, ['card', 'bank', 'wallet', 'other'])) {
    $db_payment_method = $payment_method;
}

// Create investment record first
$stmt = db()->prepare("INSERT INTO investments (investor_id, project_id, amount, payment_method, investment_date) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([$investor_id, $project_id, $amount, $db_payment_method]);
$investment_id = db()->lastInsertId();

// Create transaction record as pending
$stmt = db()->prepare("INSERT INTO transactions (investment_id, project_id, transaction_amount, transaction_status, transaction_date) VALUES (?, ?, ?, 'pending', NOW())");
$stmt->execute([$investment_id, $project_id, $amount]);
$transaction_id = db()->lastInsertId();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Gateway</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="container">
    <h2>Mock Payment Gateway</h2>

    <form method="POST" action="success.php" id="paymentForm">
        <input type="hidden" name="transaction_id" value="<?php echo $transaction_id; ?>">

        <?php if ($payment_method === 'card'): ?>
            <div id="cardFields">
                <label>Name on Card:</label>
                <input type="text" id="name_on_card" name="name_on_card" 
                       pattern="[A-Za-z\s]{2,50}" 
                       title="Name must be 2-50 letters and spaces only"
                       required>

                <label>Card Number:</label>
                <input type="text" id="card_number" name="card_number" 
                       maxlength="19" 
                       pattern="[0-9\s]{13,19}"
                       placeholder="1234 5678 9012 3456"
                       required>

                <label>Expiry (MM/YY):</label>
                <input type="text" id="expiry" name="expiry" 
                       maxlength="5" 
                       pattern="(0[1-9]|1[0-2])\/\d{2}"
                       placeholder="MM/YY"
                       required>

                <label>CVV:</label>
                <input type="text" id="cvv" name="cvv" 
                       maxlength="4" 
                       pattern="[0-9]{3,4}"
                       placeholder="123"
                       required>
            </div>
        <?php elseif ($payment_method === 'bank'): ?>
            <div id="bankFields">
                <label>Account Number:</label>
                <input type="text" id="account_number" name="account_number" 
                       pattern="[0-9]{8,20}"
                       title="Account number must be 8-20 digits"
                       required>

                <label>Routing/Sort Code:</label>
                <input type="text" id="routing_number" name="routing_number" 
                       pattern="[0-9]{6,9}"
                       title="Routing/Sort code must be 6-9 digits"
                       required>
            </div>
        <?php elseif ($payment_method === 'wallet'): ?>
            <div id="walletFields">
                <label>Wallet Address:</label>
                <input type="text" id="wallet_address" name="wallet_address" 
                       pattern="[A-Za-z0-9]{20,64}"
                       title="Wallet address must be 20-64 alphanumeric characters"
                       required>
            </div>
        <?php elseif ($payment_method === 'other'): ?>
            <div id="otherFields">
                <label>Payment Details:</label>
                <textarea id="payment_details" name="payment_details" 
                          rows="3" 
                          minlength="10"
                          maxlength="500"
                          placeholder="Please provide payment details (10-500 characters)"
                          required></textarea>
            </div>
        <?php endif; ?>

        <p id="error" style="color:red;"></p>
        <button type="submit">Pay $<?php echo htmlspecialchars($amount); ?></button>
    </form>
</div>

<script>
    // VALIDATION FUNCTIONS - Match the field IDs in gateway.php
    
    /**
     * Validate credit card information
     * - Name: 2-50 letters and spaces
     * - Card Number: 13-19 digits (with optional spaces)
     * - Expiry: MM/YY format, not in the past
     * - CVV: 3-4 digits
     */
    function validateCreditCard() {
        const errorDiv = document.getElementById('error');
        const name = document.getElementById('name_on_card').value.trim();
        const card = document.getElementById('card_number').value.replace(/\s+/g, '');
        const expiry = document.getElementById('expiry').value.trim();
        const cvv = document.getElementById('cvv').value.trim();

        // Validate name
        if (name.length < 2 || name.length > 50) {
            errorDiv.textContent = 'Name on card must be 2-50 characters.';
            return false;
        }
        if (!/^[A-Za-z\s]+$/.test(name)) {
            errorDiv.textContent = 'Name on card can only contain letters and spaces.';
            return false;
        }

        // Validate card number (13-19 digits)
        if (!/^\d{13,19}$/.test(card)) {
            errorDiv.textContent = 'Card number must be 13-19 digits.';
            return false;
        }

        // Validate expiry format (MM/YY)
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
            errorDiv.textContent = 'Expiry must be in MM/YY format (e.g., 12/25).';
            return false;
        }

        // Check expiry not in past
        const [mmStr, yyStr] = expiry.split('/');
        const mm = parseInt(mmStr, 10);
        const yy = 2000 + parseInt(yyStr, 10);
        const expDate = new Date(yy, mm, 1); // First day of expiry month
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (expDate <= today) {
            errorDiv.textContent = 'Card expiry date must be in the future.';
            return false;
        }

        // Validate CVV (3-4 digits)
        if (!/^\d{3,4}$/.test(cvv)) {
            errorDiv.textContent = 'CVV must be 3 or 4 digits.';
            return false;
        }

        errorDiv.textContent = '';
        return true;
    }

    /**
     * Validate bank transfer information
     * - Account Number: 8-20 digits
     * - Routing/Sort Code: 6-9 digits
     */
    function validateBank() {
        const errorDiv = document.getElementById('error');
        const account = document.getElementById('account_number').value.replace(/\s+/g, '');
        const routing = document.getElementById('routing_number').value.replace(/\s+/g, '');

        // Validate account number
        if (!/^\d{8,20}$/.test(account)) {
            errorDiv.textContent = 'Account number must be 8-20 digits.';
            return false;
        }

        // Validate routing/sort code
        if (!/^\d{6,9}$/.test(routing)) {
            errorDiv.textContent = 'Routing/Sort code must be 6-9 digits.';
            return false;
        }

        errorDiv.textContent = '';
        return true;
    }

    /**
     * Validate wallet address
     * - Wallet Address: 20-64 alphanumeric characters
     */
    function validateWallet() {
        const errorDiv = document.getElementById('error');
        const wallet = document.getElementById('wallet_address').value.trim();

        if (wallet.length < 20 || wallet.length > 64) {
            errorDiv.textContent = 'Wallet address must be 20-64 characters.';
            return false;
        }

        if (!/^[A-Za-z0-9]+$/.test(wallet)) {
            errorDiv.textContent = 'Wallet address can only contain letters and numbers.';
            return false;
        }

        errorDiv.textContent = '';
        return true;
    }

    /**
     * Validate other payment method
     * - Payment Details: 10-500 characters
     */
    function validateOther() {
        const errorDiv = document.getElementById('error');
        const details = document.getElementById('payment_details').value.trim();

        if (details.length < 10) {
            errorDiv.textContent = 'Payment details must be at least 10 characters.';
            return false;
        }

        if (details.length > 500) {
            errorDiv.textContent = 'Payment details must not exceed 500 characters.';
            return false;
        }

        errorDiv.textContent = '';
        return true;
    }

    // Attach validation based on selected payment method
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        let valid = false;
        const method = "<?php echo htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8'); ?>";

        // Validate based on payment method
        if (method === 'card') {
            valid = validateCreditCard();
        } else if (method === 'bank') {
            valid = validateBank();
        } else if (method === 'wallet') {
            valid = validateWallet();
        } else if (method === 'other') {
            valid = validateOther();
        } else {
            document.getElementById('error').textContent = 'Invalid payment method.';
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            // Scroll to error message
            document.getElementById('error').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Real-time validation feedback (optional - adds visual feedback as user types)
    document.addEventListener('DOMContentLoaded', function() {
        const method = "<?php echo htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8'); ?>";
        
        // Add input event listeners for real-time validation feedback
        if (method === 'card') {
            const cardNumber = document.getElementById('card_number');
            if (cardNumber) {
                // Format card number with spaces (e.g., 1234 5678 9012 3456)
                cardNumber.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\s+/g, '');
                    if (/^\d+$/.test(value)) {
                        value = value.match(/.{1,4}/g)?.join(' ') || value;
                        e.target.value = value;
                    }
                });

                // Format expiry (auto-add slash)
                const expiry = document.getElementById('expiry');
                if (expiry) {
                    expiry.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length >= 2) {
                            value = value.substring(0, 2) + '/' + value.substring(2, 4);
                        }
                        e.target.value = value;
                    });
                }
            }
        }
    });
</script>
</body>
</html>
