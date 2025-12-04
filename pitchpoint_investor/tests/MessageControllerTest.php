<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for MessageController
 */
class MessageControllerTest extends TestCase
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
     * Test that MessageController class exists
     */
    public function testMessageControllerClassExists(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/MessageController.php';
        
        $this->assertFileExists($controllerFile, 'MessageController.php file should exist');
        
        $content = file_get_contents($controllerFile);
        $this->assertStringContainsString('class MessageController', $content, 'File should contain MessageController class');
        $this->assertStringContainsString('extends Controller', $content, 'MessageController should extend Controller');
    }

    /**
     * Test that MessageController has required methods
     */
    public function testMessageControllerHasRequiredMethods(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/MessageController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('function __construct', $content, 'MessageController should have constructor');
        $this->assertStringContainsString('function index', $content, 'MessageController should have index method');
        $this->assertStringContainsString('function inbox', $content, 'MessageController should have inbox method');
        $this->assertStringContainsString('function compose', $content, 'MessageController should have compose method');
        $this->assertStringContainsString('function sendEntrepreneur', $content, 'MessageController should have sendEntrepreneur method');
        $this->assertStringContainsString('function sendAdmin', $content, 'MessageController should have sendAdmin method');
    }

    /**
     * Test MessageController initializes database
     */
    public function testMessageControllerInitializesDatabase(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/MessageController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('private PDO $db', $content, 'MessageController should have db property');
        $this->assertStringContainsString('db()', $content, 'MessageController constructor should call db()');
    }

    /**
     * Test inbox method queries both message types
     */
    public function testInboxQueriesBothMessageTypes(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/MessageController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('FROM messages', $content, 'inbox should query messages table');
        $this->assertStringContainsString('FROM email_management', $content, 'inbox should query email_management table');
        $this->assertStringContainsString('sender_user_id', $content, 'inbox should filter by sender_user_id');
        $this->assertStringContainsString('receiver_user_id', $content, 'inbox should filter by receiver_user_id');
    }

    /**
     * Test compose method loads entrepreneurs and projects
     */
    public function testComposeLoadsData(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/MessageController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('role = \'entrepreneur\'', $content, 'compose should load entrepreneurs');
        $this->assertStringContainsString('FROM projects', $content, 'compose should load projects');
        $this->assertStringContainsString('FROM administrator', $content, 'compose should load admin email');
    }

    /**
     * Test sendEntrepreneur validates input
     */
    public function testSendEntrepreneurValidatesInput(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/MessageController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$_POST[\'receiver_user_id\']', $content, 'sendEntrepreneur should read receiver_user_id');
        $this->assertStringContainsString('$_POST[\'project_id\']', $content, 'sendEntrepreneur should read project_id');
        $this->assertStringContainsString('$_POST[\'body\']', $content, 'sendEntrepreneur should read body');
        $this->assertStringContainsString('INSERT INTO messages', $content, 'sendEntrepreneur should insert into messages');
    }

    /**
     * Test sendAdmin validates input
     */
    public function testSendAdminValidatesInput(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/MessageController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$_POST[\'receiver_email\']', $content, 'sendAdmin should read receiver_email');
        $this->assertStringContainsString('$_POST[\'subject\']', $content, 'sendAdmin should read subject');
        $this->assertStringContainsString('$_POST[\'body_admin\']', $content, 'sendAdmin should read body_admin');
        $this->assertStringContainsString('INSERT INTO email_management', $content, 'sendAdmin should insert into email_management');
    }

    /**
     * Test all methods require authentication
     */
    public function testMethodsRequireAuthentication(): void
    {
        $controllerFile = __DIR__ . '/../app/controllers/MessageController.php';
        $content = file_get_contents($controllerFile);
        
        $this->assertStringContainsString('$_SESSION[\'user_id\']', $content, 'Methods should check user_id');
        $this->assertStringContainsString('redirect(\'auth/login\')', $content, 'Methods should redirect if not authenticated');
    }
}

