<?php
include 'connection.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();                                     
    $mail->Host       = 'smtp.gmail.com';               
    $mail->SMTPAuth   = true;                             
    $mail->Username   = 'bcpclinicmanagement@gmail.com';     
    $mail->Password   = 'fvzf ldba jroq xzjf';            
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
    $mail->Port       = 587;                              
    $mail->setFrom('bcpclinicmanagement@gmail.com', 'Mailer');
    $mail->addAddress('acoalexis359@gmail.com', 'Sofio'); 
    $mail->isHTML(true);                                 
    $mail->Subject = 'Two Factor Authentication';
    $mail->Body    = 'Please do not share or post this <b>OTP</b>';
    $mail->AltBody = 'This is the plain text for non-HTML email clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
