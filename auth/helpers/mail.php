<?php
// helpers/mail.php
// MailHog-compatible wrapper for local dev.

if (!function_exists('sendVerificationEmail')) {
    function sendVerificationEmail(string $email, string $token, string $name = ''): bool {
        // Ensure PHP attempts to use MailHog
        ini_set('SMTP', 'localhost');
        ini_set('smtp_port', '1025');

        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        $subject = "Verify your PitchPoint account";
        $message = "Hello " . ($name ? $name : '') . "\r\n\r\n";
        $message .= "Please verify your account by clicking the link below:\r\n";
        $message .= $verifyLink . "\r\n\r\n";
        $message .= "If you did not sign up, ignore this message.\r\n";

        $headers = "From: noreply@pitchpoint.local\r\n";
        // For HTML emails adjust headers/body accordingly.

        return mail($email, $subject, $message, $headers);
    }
}
