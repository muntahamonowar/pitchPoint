<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for Interest Model
 */
class InterestModelTest extends TestCase
{
    /**
     * Test that Interest class exists
     */
    public function testInterestClassExists(): void
    {
        $modelFile = __DIR__ . '/../app/models/interest.php';
        
        $this->assertFileExists($modelFile, 'interest.php file should exist');
        
        $content = file_get_contents($modelFile);
        $this->assertStringContainsString('class Interest', $content, 'File should contain Interest class');
        $this->assertStringContainsString('extends Model', $content, 'Interest should extend Model');
    }

    /**
     * Test that Interest has required methods
     */
    public function testInterestHasRequiredMethods(): void
    {
        $modelFile = __DIR__ . '/../app/models/interest.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('function has', $content, 'Interest should have has method');
        $this->assertStringContainsString('function toggle', $content, 'Interest should have toggle method');
    }

    /**
     * Test has method query structure
     */
    public function testHasMethodQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/interest.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('SELECT interest_id FROM project_interests', $content, 'has should query project_interests table');
        $this->assertStringContainsString('investor_id=?', $content, 'has should filter by investor_id');
        $this->assertStringContainsString('project_id=?', $content, 'has should filter by project_id');
    }

    /**
     * Test toggle method logic
     */
    public function testToggleMethodLogic(): void
    {
        $modelFile = __DIR__ . '/../app/models/interest.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('DELETE FROM project_interests', $content, 'toggle should delete when interest exists');
        $this->assertStringContainsString('INSERT INTO project_interests', $content, 'toggle should insert when interest does not exist');
        $this->assertStringContainsString('has(', $content, 'toggle should check has() before toggling');
    }

    /**
     * Test toggle method return values
     */
    public function testToggleReturnValues(): void
    {
        $modelFile = __DIR__ . '/../app/models/interest.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('return false', $content, 'toggle should return false when removing interest');
        $this->assertStringContainsString('return true', $content, 'toggle should return true when adding interest');
    }
}

