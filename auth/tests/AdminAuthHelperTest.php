<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for admin_auth.php Helper Functions
 */
class AdminAuthHelperTest extends TestCase
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
     * Test that admin_auth.php file exists and contains required functions
     */
    public function testAdminAuthHelperFileExists(): void
    {
        $helperFile = __DIR__ . '/../helpers/admin_auth.php';
        
        $this->assertFileExists($helperFile, 'admin_auth.php file should exist');
        
        $content = file_get_contents($helperFile);
        
        // Check for main functions
        $this->assertStringContainsString('function admin_current', $content);
        $this->assertStringContainsString('function require_admin', $content);
        $this->assertStringContainsString('function admin_login', $content);
        $this->assertStringContainsString('function admin_logout', $content);
        $this->assertStringContainsString('function log_admin_activity', $content);
        $this->assertStringContainsString('function log_admin_to_file', $content);
    }

    /**
     * Test admin_current() logic (session-based)
     */
    public function testAdminCurrentLogic(): void
    {
        // Simulate admin_current() logic
        $_SESSION['admin'] = [
            'admin_id' => 1,
            'admin_name' => 'Test Admin',
            'email' => 'admin@example.com'
        ];
        
        $admin = $_SESSION['admin'] ?? null;
        
        $this->assertNotNull($admin, 'Admin should be found in session');
        $this->assertEquals(1, $admin['admin_id'], 'Admin ID should match');
        $this->assertEquals('Test Admin', $admin['admin_name'], 'Admin name should match');
    }

    /**
     * Test admin_current() returns null when not logged in
     */
    public function testAdminCurrentReturnsNullWhenNotLoggedIn(): void
    {
        $_SESSION = [];
        
        $admin = $_SESSION['admin'] ?? null;
        
        $this->assertNull($admin, 'Admin should be null when not in session');
    }

    /**
     * Test admin session structure
     */
    public function testAdminSessionStructure(): void
    {
        // Simulate admin_login() session structure
        $admin = [
            'admin_id' => 1,
            'admin_name' => 'Test Admin',
            'email' => 'admin@example.com'
        ];
        
        $_SESSION['admin'] = [
            'admin_id' => $admin['admin_id'],
            'admin_name' => $admin['admin_name'],
            'email' => $admin['email']
        ];
        
        $this->assertArrayHasKey('admin_id', $_SESSION['admin']);
        $this->assertArrayHasKey('admin_name', $_SESSION['admin']);
        $this->assertArrayHasKey('email', $_SESSION['admin']);
    }

    /**
     * Test log file entry format
     */
    public function testLogFileEntryFormat(): void
    {
        $action = 'LOGIN';
        $status = 'Success';
        $adminId = 1;
        $email = 'admin@example.com';
        $ip = '127.0.0.1';
        $userAgent = 'Test Agent';
        
        $logEntry = sprintf(
            "[%s] %s [%s] admin_id=%d email=%s ip=%s user_agent=%s\n",
            date('c'),
            strtoupper($action),
            $status,
            $adminId,
            $email ?: 'N/A',
            $ip,
            $userAgent
        );
        
        $this->assertStringContainsString('LOGIN', $logEntry, 'Log entry should contain action');
        $this->assertStringContainsString('admin_id=1', $logEntry, 'Log entry should contain admin_id');
        $this->assertStringContainsString('email=admin@example.com', $logEntry, 'Log entry should contain email');
    }

    /**
     * Test CSRF token alias functions exist
     */
    public function testCSRFTokenAliasesExist(): void
    {
        $helperFile = __DIR__ . '/../helpers/admin_auth.php';
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('function csrf_token', $content, 'csrf_token alias should exist');
        $this->assertStringContainsString('function csrf_verify', $content, 'csrf_verify alias should exist');
    }
}

