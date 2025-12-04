<?php
$to      = 'test@example.com';
$subject = 'MailHog Test';
$message = 'If you see this, MailHog is working!';
$headers = "From: noreply@pitchpoint.local\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Email Sent!";
} else {
    echo "Email failed!";
}
