<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for UserModel Authentication
 */
class UserModelTest extends TestCase
{
    /**
     * Test that userModel class exists
     */
    public function testUserModelClassExists(): void
    {
        require_once __DIR__ . '/../model/userModel.php';
        
        $this->assertTrue(
            class_exists('userModel'),
            'userModel class should exist'
        );
    }

    /**
     * Test that userModel has required static methods
     */
    public function testUserModelHasRequiredMethods(): void
    {
        require_once __DIR__ . '/../model/userModel.php';
        
        $this->assertTrue(
            method_exists('userModel', 'findByEmail'),
            'userModel should have findByEmail method'
        );
        
        $this->assertTrue(
            method_exists('userModel', 'createUser'),
            'userModel should have createUser method'
        );
        
        $this->assertTrue(
            method_exists('userModel', 'verify'),
            'userModel should have verify method'
        );
        
        $this->assertTrue(
            method_exists('userModel', 'verifyAccount'),
            'userModel should have verifyAccount method'
        );
    }

    /**
     * Test password hashing functionality
     */
    public function testPasswordHashing(): void
    {
        $password = 'testPassword123!';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertNotEmpty($hash, 'Hash should not be empty');
        $this->assertTrue(
            password_verify($password, $hash),
            'Password should verify against hash'
        );
        $this->assertFalse(
            password_verify('wrongPassword', $hash),
            'Wrong password should not verify'
        );
    }

    /**
     * Test that different passwords produce different hashes
     */
    public function testPasswordHashUniqueness(): void
    {
        $password = 'samePassword';
        $hash1 = password_hash($password, PASSWORD_DEFAULT);
        $hash2 = password_hash($password, PASSWORD_DEFAULT);
        
        // Even same password should produce different hashes (due to salt)
        $this->assertNotEquals($hash1, $hash2, 'Same password should produce different hashes');
        
        // But both should verify correctly
        $this->assertTrue(password_verify($password, $hash1));
        $this->assertTrue(password_verify($password, $hash2));
    }

    /**
     * Test verification token generation
     */
    public function testVerificationTokenGeneration(): void
    {
        $token1 = bin2hex(random_bytes(32));
        $token2 = bin2hex(random_bytes(32));
        
        $this->assertEquals(64, strlen($token1), 'Token should be 64 characters (32 bytes hex)');
        $this->assertNotEquals($token1, $token2, 'Tokens should be unique');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/i', $token1, 'Token should be hexadecimal');
    }
}

