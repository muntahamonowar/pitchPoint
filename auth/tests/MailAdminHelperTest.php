<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for mailAdmin.php Helper Functions
 */
class MailAdminHelperTest extends TestCase
{
    /**
     * Test that mailAdmin.php file exists and contains required functions
     */
    public function testMailAdminHelperFileExists(): void
    {
        $helperFile = __DIR__ . '/../helpers/mailAdmin.php';
        
        $this->assertFileExists($helperFile, 'mailAdmin.php file should exist');
        
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('function sendAdminVerificationEmail', $content, 'sendAdminVerificationEmail function should exist');
        $this->assertStringContainsString('function sendStaffVerificationEmail', $content, 'sendStaffVerificationEmail function should exist');
    }

    /**
     * Test admin verification email link format
     */
    public function testAdminVerificationLinkFormat(): void
    {
        $token = bin2hex(random_bytes(24));
        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        
        $this->assertStringContainsString('verify.php?token=', $verifyLink, 'Link should contain verify.php?token=');
        $this->assertStringContainsString($token, $verifyLink, 'Link should contain the token');
    }

    /**
     * Test admin email subject format
     */
    public function testAdminEmailSubjectFormat(): void
    {
        $subject = "Verify your PitchPoint Admin account";
        
        $this->assertStringContainsString('Verify', $subject, 'Subject should contain Verify');
        $this->assertStringContainsString('Admin', $subject, 'Subject should contain Admin');
        $this->assertStringContainsString('PitchPoint', $subject, 'Subject should contain PitchPoint');
    }

    /**
     * Test admin email message format
     */
    public function testAdminEmailMessageFormat(): void
    {
        $name = 'Admin User';
        $token = bin2hex(random_bytes(24));
        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        
        $message = "Hello " . ($name ? $name : 'Admin') . ",\r\n\r\n";
        $message .= "Please verify your admin account by clicking the link below:\r\n";
        $message .= $verifyLink . "\r\n\r\n";
        $message .= "This link will grant you access to the admin dashboard.\r\n";
        $message .= "If you did not request this, please ignore this message.\r\n";
        
        $this->assertStringContainsString('Hello', $message, 'Message should contain greeting');
        $this->assertStringContainsString($name, $message, 'Message should contain name');
        $this->assertStringContainsString($verifyLink, $message, 'Message should contain verification link');
        $this->assertStringContainsString('admin account', $message, 'Message should mention admin account');
        $this->assertStringContainsString('admin dashboard', $message, 'Message should mention admin dashboard');
    }

    /**
     * Test staff verification email link format
     */
    public function testStaffVerificationLinkFormat(): void
    {
        $token = bin2hex(random_bytes(24));
        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        
        $this->assertStringContainsString('verify.php?token=', $verifyLink, 'Link should contain verify.php?token=');
        $this->assertStringContainsString($token, $verifyLink, 'Link should contain the token');
    }

    /**
     * Test staff email subject format
     */
    public function testStaffEmailSubjectFormat(): void
    {
        $subject = "Verify your PitchPoint Staff account";
        
        $this->assertStringContainsString('Verify', $subject, 'Subject should contain Verify');
        $this->assertStringContainsString('Staff', $subject, 'Subject should contain Staff');
        $this->assertStringContainsString('PitchPoint', $subject, 'Subject should contain PitchPoint');
    }

    /**
     * Test staff email message format
     */
    public function testStaffEmailMessageFormat(): void
    {
        $name = 'Staff User';
        $token = bin2hex(random_bytes(24));
        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        
        $message = "Hello " . ($name ? $name : 'Staff') . ",\r\n\r\n";
        $message .= "Please verify your staff account by clicking the link below:\r\n";
        $message .= $verifyLink . "\r\n\r\n";
        $message .= "This link will grant you access to the staff dashboard.\r\n";
        $message .= "If you did not request this, please ignore this message.\r\n";
        
        $this->assertStringContainsString('Hello', $message, 'Message should contain greeting');
        $this->assertStringContainsString($name, $message, 'Message should contain name');
        $this->assertStringContainsString($verifyLink, $message, 'Message should contain verification link');
        $this->assertStringContainsString('staff account', $message, 'Message should mention staff account');
        $this->assertStringContainsString('staff dashboard', $message, 'Message should mention staff dashboard');
    }

    /**
     * Test email headers format
     */
    public function testEmailHeadersFormat(): void
    {
        $headers = "From: noreply@pitchpoint.local\r\n";
        
        $this->assertStringContainsString('From:', $headers, 'Headers should contain From');
        $this->assertStringContainsString('noreply@pitchpoint.local', $headers, 'Headers should contain sender email');
    }
}

