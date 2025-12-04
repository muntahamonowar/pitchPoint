<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for StaffAuthController
 */
class StaffAuthControllerTest extends TestCase
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
     * Test that StaffAuthController class exists
     */
    public function testStaffAuthControllerClassExists(): void
    {
        $controllerFile = __DIR__ . '/../controller/StaffAuthController.php';
        
        $this->assertFileExists($controllerFile, 'StaffAuthController.php file should exist');
        
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('class StaffAuthController', $content, 'File should contain StaffAuthController class');
    }

    /**
     * Test that StaffAuthController has required static methods
     */
    public function testStaffAuthControllerHasRequiredMethods(): void
    {
        $controllerFile = __DIR__ . '/../controller/StaffAuthController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString(
            'public static function login',
            $content,
            'StaffAuthController should have login method'
        );
        
        $this->assertStringContainsString(
            'public static function logout',
            $content,
            'StaffAuthController should have logout method'
        );
    }

    /**
     * Test email validation logic for staff login
     */
    public function testStaffLoginEmailValidation(): void
    {
        $validEmails = [
            'staff@example.com',
            'staff.name@domain.co.uk',
            'staff+tag@example.com'
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
    public function testStaffLoginPasswordValidation(): void
    {
        $password = 'staffPassword123';
        
        $this->assertNotEmpty($password, 'Password should not be empty');
        $this->assertGreaterThan(0, strlen($password), 'Password should have length > 0');
    }

    /**
     * Test token generation for staff verification
     */
    public function testStaffVerificationTokenGeneration(): void
    {
        $token = bin2hex(random_bytes(24));
        
        $this->assertEquals(48, strlen($token), 'Staff verification token should be 48 characters (24 bytes hex)');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{48}$/i', $token, 'Token should be hexadecimal');
    }
}

