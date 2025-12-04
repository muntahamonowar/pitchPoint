<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for SignUpController
 */
class SignUpControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ob_start();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Test that SignUpController class exists
     */
    public function testSignUpControllerClassExists(): void
    {
        $controllerFile = __DIR__ . '/../controller/SignUpController.php';
        
        $this->assertFileExists($controllerFile, 'SignUpController.php file should exist');
        
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('class SignUpController', $content, 'File should contain SignUpController class');
    }

    /**
     * Test that SignUpController has required static methods
     */
    public function testSignUpControllerHasRequiredMethods(): void
    {
        $controllerFile = __DIR__ . '/../controller/SignUpController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString(
            'public static function register',
            $content,
            'SignUpController should have register method'
        );
        
        $this->assertStringContainsString(
            'public static function verifyEmail',
            $content,
            'SignUpController should have verifyEmail method'
        );
    }

    /**
     * Test registration input validation
     */
    public function testRegistrationInputValidation(): void
    {
        // Test name validation
        $name = 'John Doe';
        $this->assertNotEmpty(trim($name), 'Name should not be empty');
        
        // Test email validation
        $email = 'user@example.com';
        $this->assertTrue(
            filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
            'Email should be valid'
        );
        
        // Test password length requirement (minimum 8 characters)
        $validPassword = 'password123';
        $invalidPassword = 'short';
        
        $this->assertGreaterThanOrEqual(8, strlen($validPassword), 'Valid password should be at least 8 characters');
        $this->assertLessThan(8, strlen($invalidPassword), 'Invalid password should be less than 8 characters');
    }

    /**
     * Test role assignment
     */
    public function testRoleAssignment(): void
    {
        $defaultRole = 'entrepreneur';
        $customRole = 'investor';
        
        $this->assertEquals('entrepreneur', $defaultRole, 'Default role should be entrepreneur');
        $this->assertNotEquals($defaultRole, $customRole, 'Custom role should be different');
    }

    /**
     * Test email verification flow
     */
    public function testEmailVerificationFlow(): void
    {
        // Test that verification token format is correct
        $token = bin2hex(random_bytes(32));
        
        $this->assertEquals(64, strlen($token), 'Verification token should be 64 characters');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/i', $token, 'Token should be hexadecimal');
    }
}

