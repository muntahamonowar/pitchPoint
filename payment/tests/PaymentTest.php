<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for payment.php
 */
class PaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ob_start();
        $_SESSION = [];
        $_GET = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_GET = [];
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Test that payment.php file exists
     */
    public function testPaymentFileExists(): void
    {
        $paymentFile = __DIR__ . '/../payment.php';
        
        $this->assertFileExists($paymentFile, 'payment.php file should exist');
    }

    /**
     * Test payment.php requires authentication
     */
    public function testPaymentRequiresAuthentication(): void
    {
        $paymentFile = __DIR__ . '/../payment.php';
        $content = file_get_contents($paymentFile);
        
        $this->assertStringContainsString('$_SESSION[\'user_id\']', $content, 'payment.php should check user_id in session');
        $this->assertStringContainsString('Location:', $content, 'payment.php should redirect if not authenticated');
        $this->assertStringContainsString('login.php', $content, 'payment.php should redirect to login');
    }

    /**
     * Test payment.php verifies investor role
     */
    public function testPaymentVerifiesInvestorRole(): void
    {
        $paymentFile = __DIR__ . '/../payment.php';
        $content = file_get_contents($paymentFile);
        
        $this->assertStringContainsString('role FROM users', $content, 'payment.php should check user role');
        $this->assertStringContainsString("'investor'", $content, 'payment.php should verify investor role');
    }

    /**
     * Test payment.php validates project
     */
    public function testPaymentValidatesProject(): void
    {
        $paymentFile = __DIR__ . '/../payment.php';
        $content = file_get_contents($paymentFile);
        
        $this->assertStringContainsString('$_GET[\'project_id\']', $content, 'payment.php should read project_id from GET');
        $this->assertStringContainsString('FROM projects', $content, 'payment.php should query projects table');
        $this->assertStringContainsString("status = 'published'", $content, 'payment.php should check project status');
    }

    /**
     * Test payment.php creates investor if needed
     */
    public function testPaymentCreatesInvestorIfNeeded(): void
    {
        $paymentFile = __DIR__ . '/../payment.php';
        $content = file_get_contents($paymentFile);
        
        $this->assertStringContainsString('FROM investors', $content, 'payment.php should query investors table');
        $this->assertStringContainsString('INSERT INTO investors', $content, 'payment.php should create investor if not exists');
        $this->assertStringContainsString('lastInsertId', $content, 'payment.php should get investor_id');
    }

    /**
     * Test payment.php displays form
     */
    public function testPaymentDisplaysForm(): void
    {
        $paymentFile = __DIR__ . '/../payment.php';
        $content = file_get_contents($paymentFile);
        
        $this->assertStringContainsString('<form', $content, 'payment.php should display form');
        $this->assertStringContainsString('gateway.php', $content, 'payment.php should submit to gateway.php');
        $this->assertStringContainsString('payment_method', $content, 'payment.php should have payment method field');
        $this->assertStringContainsString('amount', $content, 'payment.php should have amount field');
    }

    /**
     * Test payment.php includes required dependencies
     */
    public function testPaymentIncludesDependencies(): void
    {
        $paymentFile = __DIR__ . '/../payment.php';
        $content = file_get_contents($paymentFile);
        
        $this->assertStringContainsString('database.php', $content, 'payment.php should include database');
        $this->assertStringContainsString('csrf.php', $content, 'payment.php should include CSRF helper');
        $this->assertStringContainsString('theFire.php', $content, 'payment.php should include WAF');
    }
}

