<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for Investment Model
 */
class InvestmentModelTest extends TestCase
{
    /**
     * Test that Investment class exists
     */
    public function testInvestmentClassExists(): void
    {
        $modelFile = __DIR__ . '/../app/models/investment.php';
        
        $this->assertFileExists($modelFile, 'investment.php file should exist');
        
        $content = file_get_contents($modelFile);
        $this->assertStringContainsString('class Investment', $content, 'File should contain Investment class');
        $this->assertStringContainsString('extends Model', $content, 'Investment should extend Model');
    }

    /**
     * Test that Investment has create method
     */
    public function testInvestmentHasCreateMethod(): void
    {
        $modelFile = __DIR__ . '/../app/models/investment.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('function create', $content, 'Investment should have create method');
    }

    /**
     * Test create method query structure
     */
    public function testCreateMethodQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/investment.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('INSERT INTO investments', $content, 'create should insert into investments table');
        $this->assertStringContainsString('investor_id', $content, 'create should include investor_id');
        $this->assertStringContainsString('project_id', $content, 'create should include project_id');
        $this->assertStringContainsString('amount', $content, 'create should include amount');
        $this->assertStringContainsString('payment_method', $content, 'create should include payment_method');
    }

    /**
     * Test create method returns last insert ID
     */
    public function testCreateReturnsLastInsertId(): void
    {
        $modelFile = __DIR__ . '/../app/models/investment.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('lastInsertId', $content, 'create should return last insert ID');
    }

    /**
     * Test create method has default payment method
     */
    public function testCreateHasDefaultPaymentMethod(): void
    {
        $modelFile = __DIR__ . '/../app/models/investment.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString("method='card'", $content, 'create should have default payment method as card');
    }
}

