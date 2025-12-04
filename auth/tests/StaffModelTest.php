<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for StaffModel Authentication
 */
class StaffModelTest extends TestCase
{
    /**
     * Test that StaffModel class exists
     */
    public function testStaffModelClassExists(): void
    {
        require_once __DIR__ . '/../model/StaffModel.php';
        
        $this->assertTrue(
            class_exists('StaffModel'),
            'StaffModel class should exist'
        );
    }

    /**
     * Test that StaffModel has required static methods
     */
    public function testStaffModelHasRequiredMethods(): void
    {
        require_once __DIR__ . '/../model/StaffModel.php';
        
        $this->assertTrue(
            method_exists('StaffModel', 'findByEmail'),
            'StaffModel should have findByEmail method'
        );
        
        $this->assertTrue(
            method_exists('StaffModel', 'verify'),
            'StaffModel should have verify method'
        );
        
        $this->assertTrue(
            method_exists('StaffModel', 'checkCredentials'),
            'StaffModel should have checkCredentials method'
        );
        
        $this->assertTrue(
            method_exists('StaffModel', 'updatePasswordHash'),
            'StaffModel should have updatePasswordHash method'
        );
    }

    /**
     * Test staff password hashing
     */
    public function testStaffPasswordHashing(): void
    {
        $password = 'staffPassword123!';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertNotEmpty($hash);
        $this->assertTrue(password_verify($password, $hash));
    }
}

