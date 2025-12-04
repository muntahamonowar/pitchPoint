<?php
// helpers/mailAdmin.php
// MailHog-compatible wrapper for admin and staff verification emails.

if (!function_exists('sendAdminVerificationEmail')) {
    function sendAdminVerificationEmail(string $email, string $token, string $name = ''): bool {
        // Ensure PHP attempts to use MailHog
        ini_set('SMTP', 'localhost');
        ini_set('smtp_port', '1025');

        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        $subject = "Verify your PitchPoint Admin account";
        $message = "Hello " . ($name ? $name : 'Admin') . ",\r\n\r\n";
        $message .= "Please verify your admin account by clicking the link below:\r\n";
        $message .= $verifyLink . "\r\n\r\n";
        $message .= "This link will grant you access to the admin dashboard.\r\n";
        $message .= "If you did not request this, please ignore this message.\r\n";

        $headers = "From: noreply@pitchpoint.local\r\n";

        return mail($email, $subject, $message, $headers);
    }
}

if (!function_exists('sendStaffVerificationEmail')) {
    function sendStaffVerificationEmail(string $email, string $token, string $name = ''): bool {
        // Ensure PHP attempts to use MailHog
        ini_set('SMTP', 'localhost');
        ini_set('smtp_port', '1025');

        $verifyLink = "http://localhost/pitchPoint/auth/verify.php?token=" . urlencode($token);
        $subject = "Verify your PitchPoint Staff account";
        $message = "Hello " . ($name ? $name : 'Staff') . ",\r\n\r\n";
        $message .= "Please verify your staff account by clicking the link below:\r\n";
        $message .= $verifyLink . "\r\n\r\n";
        $message .= "This link will grant you access to the staff dashboard.\r\n";
        $message .= "If you did not request this, please ignore this message.\r\n";

        $headers = "From: noreply@pitchpoint.local\r\n";

        return mail($email, $subject, $message, $headers);
    }
}

