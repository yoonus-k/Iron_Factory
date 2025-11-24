<?php
require_once 'vendor/autoload.php';

// Load the .env file
if (file_exists('.env')) {
     = \Dotenv\Dotenv::createImmutable(__DIR__);
    ->load();
}

// Test email sending
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

\ = new PHPMailer(true);

try {
    // Server settings
    \->isSMTP();
    \->Host       = \['MAIL_HOST'];
    \->SMTPAuth   = true;
    \->Username   = \['MAIL_USERNAME'];
    \->Password   = \['MAIL_PASSWORD'];
    \->SMTPSecure = \['MAIL_ENCRYPTION'];
    \->Port       = \['MAIL_PORT'];
    
    // Recipients
    \->setFrom(\['MAIL_FROM_ADDRESS'], \['MAIL_FROM_NAME']);
    \->addAddress('abdu0560@gmail.com');
    
    // Content
    \->isHTML(true);
    \->Subject = 'Test Email from Iron Factory';
    \->Body    = 'This is a test email to verify your SMTP configuration is working.';
    
    \->send();
    echo 'Email sent successfully!';
} catch (Exception \) {
    echo 'Email could not be sent. Mailer Error: ' . \->ErrorInfo;
}

