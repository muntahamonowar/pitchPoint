<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for Investor Model
 */
class InvestorModelTest extends TestCase
{
    /**
     * Test that Investor class exists
     */
    public function testInvestorClassExists(): void
    {
        $modelFile = __DIR__ . '/../app/models/investor.php';
        
        $this->assertFileExists($modelFile, 'investor.php file should exist');
        
        $content = file_get_contents($modelFile);
        $this->assertStringContainsString('class Investor', $content, 'File should contain Investor class');
        $this->assertStringContainsString('extends Model', $content, 'Investor should extend Model');
    }

    /**
     * Test that Investor has required methods
     */
    public function testInvestorHasRequiredMethods(): void
    {
        $modelFile = __DIR__ . '/../app/models/investor.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('function byUserId', $content, 'Investor should have byUserId method');
        $this->assertStringContainsString('function createIfNotExists', $content, 'Investor should have createIfNotExists method');
        $this->assertStringContainsString('function projectsInvested', $content, 'Investor should have projectsInvested method');
        $this->assertStringContainsString('function projectsInterested', $content, 'Investor should have projectsInterested method');
    }

    /**
     * Test byUserId method query structure
     */
    public function testByUserIdQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/investor.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('SELECT * FROM investors', $content, 'byUserId should query investors table');
        $this->assertStringContainsString('user_id = ?', $content, 'byUserId should filter by user_id');
    }

    /**
     * Test createIfNotExists logic
     */
    public function testCreateIfNotExistsLogic(): void
    {
        $modelFile = __DIR__ . '/../app/models/investor.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('INSERT INTO investors', $content, 'createIfNotExists should insert into investors');
        $this->assertStringContainsString('byUserId', $content, 'createIfNotExists should check existing with byUserId');
    }

    /**
     * Test projectsInvested query structure
     */
    public function testProjectsInvestedQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/investor.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('FROM investments', $content, 'projectsInvested should query investments table');
        $this->assertStringContainsString('INNER JOIN projects', $content, 'projectsInvested should join projects');
        $this->assertStringContainsString('investor_id = ?', $content, 'projectsInvested should filter by investor_id');
    }

    /**
     * Test projectsInterested query structure
     */
    public function testProjectsInterestedQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/investor.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('FROM projects', $content, 'projectsInterested should query projects table');
        $this->assertStringContainsString('INNER JOIN project_interests', $content, 'projectsInterested should join project_interests');
        $this->assertStringContainsString('investor_id = ?', $content, 'projectsInterested should filter by investor_id');
    }
}

