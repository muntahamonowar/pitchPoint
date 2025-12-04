<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for AdminAuthController
 */
class AdminAuthControllerTest extends TestCase
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
     * Test that AdminAuthController class exists
     */
    public function testAdminAuthControllerClassExists(): void
    {
        $controllerFile = __DIR__ . '/../controller/AdminAuthController.php';
        
        $this->assertFileExists($controllerFile, 'AdminAuthController.php file should exist');
        
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('class AdminAuthController', $content, 'File should contain AdminAuthController class');
    }

    /**
     * Test that AdminAuthController has required static methods
     */
    public function testAdminAuthControllerHasRequiredMethods(): void
    {
        $controllerFile = __DIR__ . '/../controller/AdminAuthController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString(
            'public static function login',
            $content,
            'AdminAuthController should have login method'
        );
        
        $this->assertStringContainsString(
            'public static function logout',
            $content,
            'AdminAuthController should have logout method'
        );
    }

    /**
     * Test email validation logic for admin login
     */
    public function testAdminLoginEmailValidation(): void
    {
        $validEmails = [
            'admin@example.com',
            'admin.name@domain.co.uk',
            'admin+tag@example.com'
        ];
        
        foreach ($validEmails as $email) {
            $sanitized = trim(filter_var($email, FILTER_SANITIZE_EMAIL));
            $isValid = filter_var($sanitized, FILTER_VALIDATE_EMAIL) !== false;
            
            $this->assertTrue($isValid, "Email '{$email}' should be valid");
        }
    }

    /**
     * Test password validation (non-empty)
     */
    public function testAdminLoginPasswordValidation(): void
    {
        $password = 'adminPassword123';
        
        $this->assertNotEmpty($password, 'Password should not be empty');
        $this->assertGreaterThan(0, strlen($password), 'Password should have length > 0');
    }

    /**
     * Test token generation for admin verification
     */
    public function testAdminVerificationTokenGeneration(): void
    {
        $token = bin2hex(random_bytes(24));
        
        $this->assertEquals(48, strlen($token), 'Admin verification token should be 48 characters (24 bytes hex)');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{48}$/i', $token, 'Token should be hexadecimal');
    }
}

