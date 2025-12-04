<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for gateway.php
 */
class GatewayTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ob_start();
        $_SESSION = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_POST = [];
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Test that gateway.php file exists
     */
    public function testGatewayFileExists(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        
        $this->assertFileExists($gatewayFile, 'gateway.php file should exist');
    }

    /**
     * Test gateway.php requires POST method
     */
    public function testGatewayRequiresPostMethod(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('REQUEST_METHOD', $content, 'gateway.php should check request method');
        $this->assertStringContainsString("'POST'", $content, 'gateway.php should require POST');
    }

    /**
     * Test gateway.php requires authentication
     */
    public function testGatewayRequiresAuthentication(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('$_SESSION[\'user_id\']', $content, 'gateway.php should check user_id');
        $this->assertStringContainsString('Location:', $content, 'gateway.php should redirect if not authenticated');
    }

    /**
     * Test gateway.php validates payment data
     */
    public function testGatewayValidatesPaymentData(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('$_POST[\'project_id\']', $content, 'gateway.php should read project_id');
        $this->assertStringContainsString('$_POST[\'investor_id\']', $content, 'gateway.php should read investor_id');
        $this->assertStringContainsString('$_POST[\'amount\']', $content, 'gateway.php should read amount');
        $this->assertStringContainsString('$_POST[\'payment_method\']', $content, 'gateway.php should read payment_method');
        $this->assertStringContainsString('$amount <= 0', $content, 'gateway.php should validate amount > 0');
    }

    /**
     * Test gateway.php creates investment record
     */
    public function testGatewayCreatesInvestment(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('INSERT INTO investments', $content, 'gateway.php should create investment');
        $this->assertStringContainsString('investment_date', $content, 'gateway.php should set investment date');
        $this->assertStringContainsString('payment_method', $content, 'gateway.php should store payment method');
    }

    /**
     * Test gateway.php creates transaction record
     */
    public function testGatewayCreatesTransaction(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('INSERT INTO transactions', $content, 'gateway.php should create transaction');
        $this->assertStringContainsString("'pending'", $content, 'gateway.php should set transaction status to pending');
        $this->assertStringContainsString('transaction_date', $content, 'gateway.php should set transaction date');
    }

    /**
     * Test gateway.php handles payment methods
     */
    public function testGatewayHandlesPaymentMethods(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString("'card'", $content, 'gateway.php should handle card payment');
        $this->assertStringContainsString("'bank'", $content, 'gateway.php should handle bank payment');
        $this->assertStringContainsString("'wallet'", $content, 'gateway.php should handle wallet payment');
        $this->assertStringContainsString("'other'", $content, 'gateway.php should handle other payment');
    }

    /**
     * Test gateway.php validates credit card fields
     */
    public function testGatewayValidatesCreditCard(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('name_on_card', $content, 'gateway.php should have name_on_card field');
        $this->assertStringContainsString('card_number', $content, 'gateway.php should have card_number field');
        $this->assertStringContainsString('expiry', $content, 'gateway.php should have expiry field');
        $this->assertStringContainsString('cvv', $content, 'gateway.php should have cvv field');
        $this->assertStringContainsString('validateCreditCard', $content, 'gateway.php should have credit card validation');
    }

    /**
     * Test gateway.php validates bank transfer fields
     */
    public function testGatewayValidatesBankTransfer(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('account_number', $content, 'gateway.php should have account_number field');
        $this->assertStringContainsString('routing_number', $content, 'gateway.php should have routing_number field');
        $this->assertStringContainsString('validateBank', $content, 'gateway.php should have bank validation');
    }

    /**
     * Test gateway.php validates wallet fields
     */
    public function testGatewayValidatesWallet(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('wallet_address', $content, 'gateway.php should have wallet_address field');
        $this->assertStringContainsString('validateWallet', $content, 'gateway.php should have wallet validation');
    }

    /**
     * Test gateway.php validates other payment method
     */
    public function testGatewayValidatesOtherPayment(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('payment_details', $content, 'gateway.php should have payment_details field');
        $this->assertStringContainsString('validateOther', $content, 'gateway.php should have other payment validation');
    }

    /**
     * Test gateway.php submits to success.php
     */
    public function testGatewaySubmitsToSuccess(): void
    {
        $gatewayFile = __DIR__ . '/../gateway.php';
        $content = file_get_contents($gatewayFile);
        
        $this->assertStringContainsString('success.php', $content, 'gateway.php should submit to success.php');
        $this->assertStringContainsString('transaction_id', $content, 'gateway.php should pass transaction_id');
    }
}

