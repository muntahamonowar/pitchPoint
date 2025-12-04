<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit Tests for mail.php Helper Functions
 */
class MailHelperTest extends TestCase
{
    /**
     * Test that mail.php file exists and contains required functions
     */
    public function testMailHelperFileExists(): void
    {
        $helperFile = __DIR__ . '/../helpers/mail.php';
        
        $this->assertFileExists($helperFile, 'mail.php file should exist');
        
        $content = file_get_contents($helperFile);
        
        $this->assertStringContainsString('function sendVerificationEmail', $content, 'sendVerificationEmail function should exist');
    }

    /**
     * Test email verification link format
     */
    public function testVerificationLinkFormat(): void
    {
        $token = bin2hex(random_bytes(32));
        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        
        $this->assertStringContainsString('verify.php?token=', $verifyLink, 'Link should contain verify.php?token=');
        $this->assertStringContainsString($token, $verifyLink, 'Link should contain the token');
    }

    /**
     * Test email subject format
     */
    public function testEmailSubjectFormat(): void
    {
        $subject = "Verify your PitchPoint account";
        
        $this->assertStringContainsString('Verify', $subject, 'Subject should contain Verify');
        $this->assertStringContainsString('PitchPoint', $subject, 'Subject should contain PitchPoint');
    }

    /**
     * Test email message format
     */
    public function testEmailMessageFormat(): void
    {
        $name = 'John Doe';
        $token = bin2hex(random_bytes(32));
        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        
        $message = "Hello " . ($name ? $name : '') . "\r\n\r\n";
        $message .= "Please verify your account by clicking the link below:\r\n";
        $message .= $verifyLink . "\r\n\r\n";
        $message .= "If you did not sign up, ignore this message.\r\n";
        
        $this->assertStringContainsString('Hello', $message, 'Message should contain greeting');
        $this->assertStringContainsString($name, $message, 'Message should contain name');
        $this->assertStringContainsString($verifyLink, $message, 'Message should contain verification link');
        $this->assertStringContainsString('verify your account', $message, 'Message should contain verification instruction');
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

    /**
     * Test email address validation
     */
    public function testEmailAddressValidation(): void
    {
        $validEmails = [
            'user@example.com',
            'test.user@domain.co.uk',
            'user+tag@example.com'
        ];
        
        foreach ($validEmails as $email) {
            $this->assertTrue(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "Email '{$email}' should be valid"
            );
        }
    }
}

