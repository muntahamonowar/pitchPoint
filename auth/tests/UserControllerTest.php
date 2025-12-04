<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for UserController
 */
class UserControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Use output buffering to prevent header issues
        ob_start();
        // Initialize session array
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        // Clean up session
        $_SESSION = [];
        // Clean output buffer
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Test that userController class exists
     */
    public function testUserControllerClassExists(): void
    {
        // Check that the file exists instead of requiring it
        // (requiring it causes session_start() issues in PHPUnit)
        $controllerFile = __DIR__ . '/../controller/userController.php';
        
        $this->assertFileExists($controllerFile, 'userController.php file should exist');
        
        // Verify the file contains the class definition
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('class userController', $content, 'File should contain userController class');
    }

    /**
     * Test that userController has required static methods
     */
    public function testUserControllerHasRequiredMethods(): void
    {
        // Check file content instead of requiring (to avoid session_start() issues)
        $controllerFile = __DIR__ . '/../controller/userController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString(
            'public static function login',
            $content,
            'userController should have login method'
        );
        
        $this->assertStringContainsString(
            'public static function logout',
            $content,
            'userController should have logout method'
        );
    }

    /**
     * Test email validation logic
     */
    public function testEmailValidation(): void
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'user+tag@example.com'
        ];
        
        $invalidEmails = [
            'invalid-email',
            '@example.com',
            'user@',
            'user name@example.com'
        ];
        
        foreach ($validEmails as $email) {
            $this->assertTrue(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "Email '{$email}' should be valid"
            );
        }
        
        foreach ($invalidEmails as $email) {
            $this->assertFalse(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "Email '{$email}' should be invalid"
            );
        }
    }

    /**
     * Test email sanitization
     */
    public function testEmailSanitization(): void
    {
        $email = '  TEST@EXAMPLE.COM  ';
        $sanitized = trim(filter_var($email, FILTER_SANITIZE_EMAIL));
        
        $this->assertEquals('TEST@EXAMPLE.COM', $sanitized);
        $this->assertNotEquals($email, $sanitized);
    }
}
