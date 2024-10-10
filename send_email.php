<?php
// Include PHPMailer files
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create an instance of PHPMailer
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host       = 'smtp.gmail.com';               // Specify SMTP server
    $mail->SMTPAuth   = true;                             // Enable SMTP authentication
    $mail->Username   = 'bcpclinicmanagement@gmail.com';         // SMTP username
    $mail->Password   = 'fvzf ldba jroq xzjf';            // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587;                              // TCP port to connect to

    //Recipients
    $mail->setFrom('bcpclinicmanagement@gmail.com', 'Mailer');
    $mail->addAddress('acoalexis359@gmail.com', 'Sofio'); // Add a recipient

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Two Factor Authentication';
    $mail->Body    = 'Please do not share or post this <b>OTP</b>';
    $mail->AltBody = 'This is the plain text for non-HTML email clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
