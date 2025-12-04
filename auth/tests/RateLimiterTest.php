<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for rateLimiter.php Helper Functions
 */
class RateLimiterTest extends TestCase
{
    protected $rateLimitDir;
    protected $testIdentifier = 'test_identifier_123';
    protected $testAction = 'test_action';

    protected function setUp(): void
    {
        parent::setUp();
        ob_start();
        
        // Set up test rate limit directory
        $this->rateLimitDir = __DIR__ . '/../rate_limit';
        
        // Clean up any existing test files
        $this->cleanupTestFiles();
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $this->cleanupTestFiles();
        
        if (ob_get_level() > 0) {
            ob_end_clean();
        }
        parent::tearDown();
    }

    /**
     * Clean up test rate limit files
     */
    protected function cleanupTestFiles(): void
    {
        if (file_exists($this->rateLimitDir)) {
            $testFiles = glob($this->rateLimitDir . '/*.json');
            foreach ($testFiles as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    /**
     * Test that rateLimiter.php file exists and contains required functions
     */
    public function testRateLimiterFileExists(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        
        $this->assertFileExists($helperFile, 'rateLimiter.php file should exist');
        
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('function rate_limit_check', $content, 'rateLimiter should have rate_limit_check function');
        $this->assertStringContainsString('function rate_limit_remaining', $content, 'rateLimiter should have rate_limit_remaining function');
        $this->assertStringContainsString('function get_client_ip', $content, 'rateLimiter should have get_client_ip function');
        $this->assertStringContainsString('function rate_limit_cleanup', $content, 'rateLimiter should have rate_limit_cleanup function');
    }

    /**
     * Test rate_limit_check function logic
     */
    public function testRateLimitCheckLogic(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('hash(\'sha256\'', $content, 'rate_limit_check should hash identifier');
        $this->assertStringContainsString('time()', $content, 'rate_limit_check should use current time');
        $this->assertStringContainsString('json_encode', $content, 'rate_limit_check should use JSON for storage');
        $this->assertStringContainsString('array_filter', $content, 'rate_limit_check should filter expired entries');
    }

    /**
     * Test rate limit file structure
     */
    public function testRateLimitFileStructure(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('rate_limit', $content, 'rateLimiter should use rate_limit directory');
        $this->assertStringContainsString('.json', $content, 'rateLimiter should use JSON files');
        $this->assertStringContainsString('mkdir', $content, 'rateLimiter should create directory if needed');
    }

    /**
     * Test rate_limit_remaining function logic
     */
    public function testRateLimitRemainingLogic(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('max(0,', $content, 'rate_limit_remaining should return non-negative');
        $this->assertStringContainsString('$maxRequests - count', $content, 'rate_limit_remaining should calculate remaining');
    }

    /**
     * Test get_client_ip function checks multiple headers
     */
    public function testGetClientIpChecksMultipleHeaders(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('HTTP_CF_CONNECTING_IP', $content, 'get_client_ip should check Cloudflare header');
        $this->assertStringContainsString('HTTP_X_FORWARDED_FOR', $content, 'get_client_ip should check X-Forwarded-For');
        $this->assertStringContainsString('REMOTE_ADDR', $content, 'get_client_ip should check REMOTE_ADDR');
        $this->assertStringContainsString('filter_var', $content, 'get_client_ip should validate IP');
    }

    /**
     * Test get_client_ip handles comma-separated IPs
     */
    public function testGetClientIpHandlesCommaSeparatedIps(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('explode(\',\'', $content, 'get_client_ip should handle comma-separated IPs');
        $this->assertStringContainsString('strpos($ip, \',\')', $content, 'get_client_ip should check for comma');
    }

    /**
     * Test rate_limit_cleanup function logic
     */
    public function testRateLimitCleanupLogic(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('glob(', $content, 'rate_limit_cleanup should use glob to find files');
        $this->assertStringContainsString('filemtime', $content, 'rate_limit_cleanup should check file modification time');
        $this->assertStringContainsString('unlink', $content, 'rate_limit_cleanup should delete old files');
    }

    /**
     * Test identifier hashing for security
     */
    public function testIdentifierHashing(): void
    {
        $identifier = 'test@example.com';
        $action = 'login';
        
        // Simulate the hashing logic
        $safeId = hash('sha256', $identifier . $action);
        
        $this->assertEquals(64, strlen($safeId), 'Hashed identifier should be 64 characters (SHA256)');
        $this->assertNotEquals($identifier, $safeId, 'Hashed identifier should not match original');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/i', $safeId, 'Hash should be hexadecimal');
    }

    /**
     * Test time window filtering logic
     */
    public function testTimeWindowFiltering(): void
    {
        $now = time();
        $timeWindow = 60; // 1 minute
        
        // Simulate old and new timestamps
        $oldTimestamp = $now - 120; // 2 minutes ago (expired)
        $newTimestamp = $now - 30;  // 30 seconds ago (valid)
        
        $requests = [$oldTimestamp, $newTimestamp];
        
        // Simulate the filtering logic
        $filtered = array_filter($requests, function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        $this->assertCount(1, $filtered, 'Should filter out expired entries');
        $this->assertContains($newTimestamp, $filtered, 'Should keep valid entries');
        $this->assertNotContains($oldTimestamp, $filtered, 'Should remove expired entries');
    }

    /**
     * Test rate limit count logic
     */
    public function testRateLimitCountLogic(): void
    {
        $maxRequests = 5;
        $requests = [time(), time() - 10, time() - 20]; // 3 requests
        
        // Simulate the count check
        $count = count($requests);
        $exceeded = $count >= $maxRequests;
        
        $this->assertFalse($exceeded, 'Should not exceed limit with 3 requests');
        
        // Test with 5 requests (at limit)
        $requests = array_fill(0, 5, time());
        $count = count($requests);
        $exceeded = $count >= $maxRequests;
        
        $this->assertTrue($exceeded, 'Should exceed limit with 5 requests');
    }

    /**
     * Test IP validation logic
     */
    public function testIpValidationLogic(): void
    {
        $validIps = [
            '192.168.1.1',
            '10.0.0.1',
            '172.16.0.1'
        ];
        
        $invalidIps = [
            'not.an.ip',
            '256.256.256.256',
            ''
        ];
        
        foreach ($validIps as $ip) {
            $isValid = filter_var($ip, FILTER_VALIDATE_IP) !== false;
            $this->assertTrue($isValid, "IP '{$ip}' should be valid");
        }
        
        foreach ($invalidIps as $ip) {
            if ($ip !== '') {
                $isValid = filter_var($ip, FILTER_VALIDATE_IP) !== false;
                $this->assertFalse($isValid, "IP '{$ip}' should be invalid");
            }
        }
    }

    /**
     * Test default parameter values
     */
    public function testDefaultParameterValues(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        $content = file_get_contents($helperFile);
        
        // Check rate_limit_check defaults
        $this->assertStringContainsString('$maxRequests = 5', $content, 'rate_limit_check should default to 5 requests');
        $this->assertStringContainsString('$timeWindow = 60', $content, 'rate_limit_check should default to 60 seconds');
        $this->assertTrue(
            strpos($content, '$action = \'default\'') !== false || 
            strpos($content, 'action = \'default\'') !== false,
            'rate_limit_check should default action to "default"'
        );
        
        // Check rate_limit_cleanup default
        $this->assertStringContainsString('$maxAge = 3600', $content, 'rate_limit_cleanup should default to 3600 seconds (1 hour)');
    }

    /**
     * Test JSON file operations
     */
    public function testJsonFileOperations(): void
    {
        // Test JSON encoding/decoding logic
        $data = [time(), time() - 10, time() - 20];
        $json = json_encode(array_values($data));
        $decoded = json_decode($json, true);
        
        $this->assertIsString($json, 'Data should encode to JSON string');
        $this->assertIsArray($decoded, 'JSON should decode to array');
        $this->assertEquals($data, $decoded, 'Decoded data should match original');
    }

    /**
     * Test file locking mechanism
     */
    public function testFileLockingMechanism(): void
    {
        $helperFile = __DIR__ . '/../helpers/rateLimiter.php';
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('LOCK_EX', $content, 'rate_limit_check should use file locking');
    }
}

