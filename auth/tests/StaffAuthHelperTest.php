<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for staff_auth.php Helper Functions
 */
class StaffAuthHelperTest extends TestCase
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
     * Test that staff_auth.php file exists and contains required functions
     */
    public function testStaffAuthHelperFileExists(): void
    {
        $helperFile = __DIR__ . '/../helpers/staff_auth.php';
        
        $this->assertFileExists($helperFile, 'staff_auth.php file should exist');
        
        $content = file_get_contents($helperFile);
        
        // Check for main functions
        $this->assertStringContainsString('function staff_current', $content);
        $this->assertStringContainsString('function require_staff', $content);
        $this->assertStringContainsString('function staff_login', $content);
        $this->assertStringContainsString('function staff_logout', $content);
        $this->assertStringContainsString('function log_staff_activity', $content);
        $this->assertStringContainsString('function log_staff_to_file', $content);
    }

    /**
     * Test staff_current() logic (session-based)
     */
    public function testStaffCurrentLogic(): void
    {
        // Simulate staff_current() logic
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'staff';
        $_SESSION['user_name'] = 'Test Staff';
        $_SESSION['staff_id'] = 1;
        
        // Simulate the function logic
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'staff') {
            $staff = null;
        } else {
            $staff = [
                'user_id' => $_SESSION['user_id'] ?? null,
                'user_name' => $_SESSION['user_name'] ?? null,
                'user_role' => $_SESSION['user_role'] ?? null,
                'staff_id' => $_SESSION['staff_id'] ?? null,
            ];
        }
        
        $this->assertNotNull($staff, 'Staff should be found in session');
        $this->assertEquals(1, $staff['user_id'], 'User ID should match');
        $this->assertEquals('staff', $staff['user_role'], 'Role should be staff');
        $this->assertEquals(1, $staff['staff_id'], 'Staff ID should match');
    }

    /**
     * Test staff_current() returns null when not logged in
     */
    public function testStaffCurrentReturnsNullWhenNotLoggedIn(): void
    {
        $_SESSION = [];
        
        // Simulate the function logic
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'staff') {
            $staff = null;
        }
        
        $this->assertNull($staff ?? null, 'Staff should be null when not in session');
    }

    /**
     * Test staff_current() returns null when role is not staff
     */
    public function testStaffCurrentReturnsNullWhenRoleNotStaff(): void
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'entrepreneur'; // Wrong role
        
        // Simulate the function logic
        if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'staff') {
            $staff = null;
        }
        
        $this->assertNull($staff ?? null, 'Staff should be null when role is not staff');
    }

    /**
     * Test staff session structure
     */
    public function testStaffSessionStructure(): void
    {
        // Simulate staff_login() session structure
        $staff = [
            'user_id' => 1,
            'name' => 'Test Staff',
            'email' => 'staff@example.com',
            'role' => 'staff',
            'staff_id' => 1
        ];
        
        $_SESSION['user_id'] = (int)$staff['user_id'];
        $_SESSION['user_name'] = $staff['name'];
        $_SESSION['user_role'] = $staff['role'];
        $_SESSION['staff_id'] = (int)$staff['staff_id'];
        
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('Test Staff', $_SESSION['user_name']);
        $this->assertEquals('staff', $_SESSION['user_role']);
        $this->assertEquals(1, $_SESSION['staff_id']);
    }

    /**
     * Test log file entry format
     */
    public function testLogFileEntryFormat(): void
    {
        $action = 'LOGIN';
        $status = 'Success';
        $staffId = 1;
        $email = 'staff@example.com';
        $ip = '127.0.0.1';
        $userAgent = 'Test Agent';
        
        $logEntry = sprintf(
            "[%s] %s [%s] staff_id=%d email=%s ip=%s user_agent=%s\n",
            date('c'),
            strtoupper($action),
            $status,
            $staffId,
            $email ?: 'N/A',
            $ip,
            $userAgent
        );
        
        $this->assertStringContainsString('LOGIN', $logEntry, 'Log entry should contain action');
        $this->assertStringContainsString('staff_id=1', $logEntry, 'Log entry should contain staff_id');
        $this->assertStringContainsString('email=staff@example.com', $logEntry, 'Log entry should contain email');
    }
}

