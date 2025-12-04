<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for AdminModel Authentication
 */
class AdminModelTest extends TestCase
{
    /**
     * Test that AdminModel class exists
     */
    public function testAdminModelClassExists(): void
    {
        require_once __DIR__ . '/../model/AdminModel.php';
        
        $this->assertTrue(
            class_exists('AdminModel'),
            'AdminModel class should exist'
        );
    }

    /**
     * Test that AdminModel has required static methods
     */
    public function testAdminModelHasRequiredMethods(): void
    {
        require_once __DIR__ . '/../model/AdminModel.php';
        
        $this->assertTrue(
            method_exists('AdminModel', 'findByEmail'),
            'AdminModel should have findByEmail method'
        );
        
        $this->assertTrue(
            method_exists('AdminModel', 'verify'),
            'AdminModel should have verify method'
        );
        
        $this->assertTrue(
            method_exists('AdminModel', 'updatePasswordHash'),
            'AdminModel should have updatePasswordHash method'
        );
    }

    /**
     * Test admin password hashing
     */
    public function testAdminPasswordHashing(): void
    {
        $password = 'adminPassword123!';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertNotEmpty($hash);
        $this->assertTrue(password_verify($password, $hash));
    }
}

