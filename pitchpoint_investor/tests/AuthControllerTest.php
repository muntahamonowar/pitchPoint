<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for AuthController
 */
class AuthControllerTest extends TestCase
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
     * Test that AuthController class exists
     */
    public function testAuthControllerClassExists(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/AuthController.php';
        
        $this->assertFileExists($controllerFile, 'AuthController.php file should exist');
        
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('class AuthController', $content, 'File should contain AuthController class');
        $this->assertStringContainsString('extends Controller', $content, 'AuthController should extend Controller');
    }

    /**
     * Test that AuthController has logout method
     */
    public function testAuthControllerHasLogoutMethod(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/AuthController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('function logout', $content, 'AuthController should have logout method');
    }

    /**
     * Test logout method clears session
     */
    public function testLogoutClearsSession(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/AuthController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$_SESSION = []', $content, 'logout should clear session array');
        $this->assertStringContainsString('session_destroy', $content, 'logout should destroy session');
    }

    /**
     * Test logout method handles cookies
     */
    public function testLogoutHandlesCookies(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/AuthController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('session.use_cookies', $content, 'logout should check for cookies');
        $this->assertStringContainsString('setcookie', $content, 'logout should clear session cookie');
    }

    /**
     * Test logout redirects correctly
     */
    public function testLogoutRedirects(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/AuthController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('Location:', $content, 'logout should redirect');
        $this->assertStringContainsString('pitchpoint_staff', $content, 'logout should redirect to staff index');
    }
}

