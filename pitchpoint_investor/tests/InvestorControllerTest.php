<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for InvestorController
 */
class InvestorControllerTest extends TestCase
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
     * Test that InvestorController class exists
     */
    public function testInvestorControllerClassExists(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/InvestorController.php';
        
        $this->assertFileExists($controllerFile, 'InvestorController.php file should exist');
        
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('class InvestorController', $content, 'File should contain InvestorController class');
        $this->assertStringContainsString('extends Controller', $content, 'InvestorController should extend Controller');
    }

    /**
     * Test that InvestorController has required methods
     */
    public function testInvestorControllerHasRequiredMethods(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/InvestorController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('function __construct', $content, 'InvestorController should have constructor');
        $this->assertStringContainsString('function index', $content, 'InvestorController should have index method');
        $this->assertStringContainsString('function dashboard', $content, 'InvestorController should have dashboard method');
        $this->assertStringContainsString('function profile', $content, 'InvestorController should have profile method');
        $this->assertStringContainsString('function investments', $content, 'InvestorController should have investments method');
    }

    /**
     * Test InvestorController initializes models
     */
    public function testInvestorControllerInitializesModels(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/InvestorController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('new User()', $content, 'InvestorController should initialize User model');
        $this->assertStringContainsString('new Investor()', $content, 'InvestorController should initialize Investor model');
        $this->assertStringContainsString('new Project()', $content, 'InvestorController should initialize Project model');
    }

    /**
     * Test dashboard method checks authentication
     */
    public function testDashboardChecksAuthentication(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/InvestorController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$_SESSION[\'user_id\']', $content, 'dashboard should check user_id in session');
        $this->assertStringContainsString('go(\'auth/login\')', $content, 'dashboard should redirect to login if not authenticated');
    }

    /**
     * Test profile method handles editing
     */
    public function testProfileHandlesEditing(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/InvestorController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$_GET[\'edit\']', $content, 'profile should check edit parameter');
        $this->assertStringContainsString('$_SERVER[\'REQUEST_METHOD\']', $content, 'profile should handle POST requests');
        $this->assertStringContainsString('updateProfile', $content, 'profile should call updateProfile');
    }

    /**
     * Test investments method structure
     */
    public function testInvestmentsMethodStructure(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/InvestorController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('projectsInvested', $content, 'investments should call projectsInvested');
    }
}

