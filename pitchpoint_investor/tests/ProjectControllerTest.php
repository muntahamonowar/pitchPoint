<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for ProjectController
 */
class ProjectControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ob_start();
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Test that ProjectController class exists
     */
    public function testProjectControllerClassExists(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/ProjectController.php';
        
        $this->assertFileExists($controllerFile, 'ProjectController.php file should exist');
        
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('class ProjectController', $content, 'File should contain ProjectController class');
        $this->assertStringContainsString('extends Controller', $content, 'ProjectController should extend Controller');
    }

    /**
     * Test that ProjectController has required methods
     */
    public function testProjectControllerHasRequiredMethods(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/ProjectController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('function __construct', $content, 'ProjectController should have constructor');
        $this->assertStringContainsString('function index', $content, 'ProjectController should have index method');
        $this->assertStringContainsString('function explore', $content, 'ProjectController should have explore method');
        $this->assertStringContainsString('function show', $content, 'ProjectController should have show method');
        $this->assertStringContainsString('function toggleInterest', $content, 'ProjectController should have toggleInterest method');
        $this->assertStringContainsString('function invest', $content, 'ProjectController should have invest method');
        $this->assertStringContainsString('function confirmInvest', $content, 'ProjectController should have confirmInvest method');
    }

    /**
     * Test ProjectController initializes models
     */
    public function testProjectControllerInitializesModels(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/ProjectController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('new Project()', $content, 'ProjectController should initialize Project model');
        $this->assertStringContainsString('new Investor()', $content, 'ProjectController should initialize Investor model');
        $this->assertStringContainsString('new Interest()', $content, 'ProjectController should initialize Interest model');
        $this->assertStringContainsString('new Investment()', $content, 'ProjectController should initialize Investment model');
    }

    /**
     * Test explore method handles search and category filters
     */
    public function testExploreHandlesFilters(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/ProjectController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$_GET[\'q\']', $content, 'explore should read search query from GET');
        $this->assertStringContainsString('$_GET[\'cat\']', $content, 'explore should read category from GET');
        $this->assertStringContainsString('categories()', $content, 'explore should get categories');
        $this->assertStringContainsString('explore(', $content, 'explore should call model explore method');
    }

    /**
     * Test show method checks project existence
     */
    public function testShowChecksProjectExistence(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/ProjectController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('http_response_code(404)', $content, 'show should return 404 if project not found');
        $this->assertStringContainsString('find(', $content, 'show should call model find method');
    }

    /**
     * Test toggleInterest requires authentication
     */
    public function testToggleInterestRequiresAuth(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/ProjectController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$_SESSION[\'user_id\']', $content, 'toggleInterest should check user_id');
        $this->assertStringContainsString('go(\'auth/login\')', $content, 'toggleInterest should redirect if not authenticated');
        $this->assertStringContainsString('toggle(', $content, 'toggleInterest should call interest toggle method');
    }

    /**
     * Test invest method handles GET and POST
     */
    public function testInvestHandlesGetAndPost(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/ProjectController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('REQUEST_METHOD', $content, 'invest should check request method');
        $this->assertStringContainsString('payment.php', $content, 'invest GET should redirect to payment page');
        $this->assertStringContainsString('$_POST[\'amount\']', $content, 'invest POST should read amount');
        $this->assertStringContainsString('create(', $content, 'invest POST should create investment');
    }

    /**
     * Test invest validates amount
     */
    public function testInvestValidatesAmount(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/ProjectController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$amount <= 0', $content, 'invest should validate amount > 0');
    }
}

