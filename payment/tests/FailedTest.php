<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for failed.php
 */
class FailedTest extends TestCase
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
     * Test that failed.php file exists
     */
    public function testFailedFileExists(): void
    {
        $failedFile = __DIR__ . '/../failed.php';
        
        $this->assertFileExists($failedFile, 'failed.php file should exist');
    }

    /**
     * Test failed.php updates transaction status
     */
    public function testFailedUpdatesTransactionStatus(): void
    {
        $failedFile = __DIR__ . '/../failed.php';
        $content = file_get_contents($failedFile);
        
        $this->assertStringContainsString('UPDATE transactions', $content, 'failed.php should update transaction');
        $this->assertStringContainsString("'failed'", $content, 'failed.php should set status to failed');
    }

    /**
     * Test failed.php handles POST request
     */
    public function testFailedHandlesPostRequest(): void
    {
        $failedFile = __DIR__ . '/../failed.php';
        $content = file_get_contents($failedFile);
        
        $this->assertStringContainsString('REQUEST_METHOD', $content, 'failed.php should check request method');
        $this->assertStringContainsString("'POST'", $content, 'failed.php should handle POST');
        $this->assertStringContainsString('$_POST[\'transaction_id\']', $content, 'failed.php should read transaction_id');
    }

    /**
     * Test failed.php displays failure message
     */
    public function testFailedDisplaysFailureMessage(): void
    {
        $failedFile = __DIR__ . '/../failed.php';
        $content = file_get_contents($failedFile);
        
        $this->assertStringContainsString('Payment Failed', $content, 'failed.php should display failure message');
        $this->assertStringContainsString('<h2>', $content, 'failed.php should have heading');
    }

    /**
     * Test failed.php provides retry option
     */
    public function testFailedProvidesRetryOption(): void
    {
        $failedFile = __DIR__ . '/../failed.php';
        $content = file_get_contents($failedFile);
        
        $this->assertStringContainsString('Try Again', $content, 'failed.php should provide retry option');
        $this->assertStringContainsString('history.back()', $content, 'failed.php should allow going back');
    }
}

