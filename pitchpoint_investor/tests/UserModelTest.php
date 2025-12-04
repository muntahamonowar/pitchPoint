<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for Investor Module User Model
 */
class InvestorUserModelTest extends TestCase
{
    /**
     * Test that User class exists
     */
    public function testUserClassExists(): void
    {
        $modelFile = __DIR__ . '/../app/models/User.php';
        
        $this->assertFileExists($modelFile, 'User.php file should exist');
        
        $content = file_get_contents($modelFile);
        $this->assertStringContainsString('class User', $content, 'File should contain User class');
        $this->assertStringContainsString('extends Model', $content, 'User should extend Model');
    }

    /**
     * Test that User has required methods
     */
    public function testUserHasRequiredMethods(): void
    {
        $modelFile = __DIR__ . '/../app/models/User.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('function find', $content, 'User should have find method');
        $this->assertStringContainsString('function findByEmail', $content, 'User should have findByEmail method');
        $this->assertStringContainsString('function updateProfile', $content, 'User should have updateProfile method');
    }

    /**
     * Test SQL query structure for find method
     */
    public function testFindMethodQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/User.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('SELECT * FROM users', $content, 'find method should query users table');
        $this->assertStringContainsString('user_id=?', $content, 'find method should filter by user_id');
        $this->assertStringContainsString('is_active=1', $content, 'find method should filter active users');
    }

    /**
     * Test SQL query structure for findByEmail method
     */
    public function testFindByEmailMethodQueryStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/User.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('email=?', $content, 'findByEmail should filter by email');
        $this->assertStringContainsString('is_active=1', $content, 'findByEmail should filter active users');
    }

    /**
     * Test updateProfile method structure
     */
    public function testUpdateProfileMethodStructure(): void
    {
        $modelFile = __DIR__ . '/../app/models/User.php';
        $content = file_get_contents($modelFile);
        
        $this->assertStringContainsString('UPDATE users SET', $content, 'updateProfile should update users table');
        $this->assertStringContainsString('name=', $content, 'updateProfile should update name');
        $this->assertStringContainsString('email=', $content, 'updateProfile should update email');
        $this->assertStringContainsString('bio=', $content, 'updateProfile should update bio');
    }
}

