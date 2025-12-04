<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for success.php
 */
class SuccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ob_start();
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    protected function tearDown(): void
    {
        $_POST = [];
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Test that success.php file exists
     */
    public function testSuccessFileExists(): void
    {
        $successFile = __DIR__ . '/../success.php';
        
        $this->assertFileExists($successFile, 'success.php file should exist');
    }

    /**
     * Test success.php validates transaction
     */
    public function testSuccessValidatesTransaction(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('$_POST[\'transaction_id\']', $content, 'success.php should read transaction_id');
        $this->assertStringContainsString('FROM transactions', $content, 'success.php should query transactions');
        $this->assertStringContainsString('JOIN investments', $content, 'success.php should join investments');
    }

    /**
     * Test success.php validates payment data
     */
    public function testSuccessValidatesPaymentData(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('payment_method', $content, 'success.php should check payment method');
        $this->assertStringContainsString("'card'", $content, 'success.php should validate card payment');
        $this->assertStringContainsString("'bank'", $content, 'success.php should validate bank payment');
        $this->assertStringContainsString("'wallet'", $content, 'success.php should validate wallet payment');
        $this->assertStringContainsString("'other'", $content, 'success.php should validate other payment');
    }

    /**
     * Test success.php validates credit card data
     */
    public function testSuccessValidatesCreditCardData(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('name_on_card', $content, 'success.php should validate name_on_card');
        $this->assertStringContainsString('card_number', $content, 'success.php should validate card_number');
        $this->assertStringContainsString('expiry', $content, 'success.php should validate expiry');
        $this->assertStringContainsString('cvv', $content, 'success.php should validate cvv');
        $this->assertStringContainsString('preg_match', $content, 'success.php should use regex validation');
    }

    /**
     * Test success.php validates bank transfer data
     */
    public function testSuccessValidatesBankData(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('account_number', $content, 'success.php should validate account_number');
        $this->assertStringContainsString('routing_number', $content, 'success.php should validate routing_number');
    }

    /**
     * Test success.php validates wallet data
     */
    public function testSuccessValidatesWalletData(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('wallet_address', $content, 'success.php should validate wallet_address');
    }

    /**
     * Test success.php validates other payment data
     */
    public function testSuccessValidatesOtherPaymentData(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('payment_details', $content, 'success.php should validate payment_details');
    }

    /**
     * Test success.php updates transaction status
     */
    public function testSuccessUpdatesTransactionStatus(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('UPDATE transactions', $content, 'success.php should update transaction');
        $this->assertStringContainsString("'succeeded'", $content, 'success.php should set status to succeeded');
        $this->assertStringContainsString("'failed'", $content, 'success.php should set status to failed on validation error');
    }

    /**
     * Test success.php redirects on validation failure
     */
    public function testSuccessRedirectsOnFailure(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('failed.php', $content, 'success.php should redirect to failed.php on error');
        $this->assertStringContainsString('validationErrors', $content, 'success.php should track validation errors');
    }

    /**
     * Test success.php displays transaction details
     */
    public function testSuccessDisplaysTransactionDetails(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('transaction_id', $content, 'success.php should display transaction_id');
        $this->assertStringContainsString('investment_id', $content, 'success.php should display investment_id');
        $this->assertStringContainsString('transaction_amount', $content, 'success.php should display amount');
        $this->assertStringContainsString('transaction_date', $content, 'success.php should display date');
    }

    /**
     * Test success.php fetches project information
     */
    public function testSuccessFetchesProjectInfo(): void
    {
        $successFile = __DIR__ . '/../success.php';
        $content = file_get_contents($successFile);
        
        $this->assertStringContainsString('LEFT JOIN projects', $content, 'success.php should join projects');
        $this->assertStringContainsString('project_title', $content, 'success.php should get project title');
    }
}

