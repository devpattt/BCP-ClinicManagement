<?php
session_start();
if (isset($_SESSION['accountId'])) {
    header("Location: clinic-dashboard.php");
    exit();
}

include 'connection.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accountId = $_POST['accountId'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM bcp_sms3_users WHERE accountId = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $otp = rand(100000, 999999);
            $_SESSION['accountId'] = $user['accountId'];
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $user['Email'];

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'bcpclinicmanagement@gmail.com';
                $mail->Password   = 'fvzf ldba jroq xzjf';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('bcpclinicmanagement@gmail.com', 'Clinic Management System');
                $mail->addAddress($user['Email']);

                $mail->isHTML(true);
                $mail->Subject = '2 Factor Authentication';
                $mail->Body    = 'Your OTP code is <b>' . $otp . '</b>';
                $mail->AltBody = 'Your OTP code is ' . $otp;

                if ($mail->send()) {
                    $_SESSION['otp_sent'] = true;
                } else {
                    $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } catch (Exception $e) {
                $error = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Invalid account credentials!";
        }
    } else {
        $error = "Invalid account ID!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="assets/img/bcp logo.png" rel="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Clinic Management System.">
    <link rel="stylesheet" href="assets/css/index.css">
    <title>Login - CMS</title>
</head>
<body>
    <div class="logo">
        <img src="assets/img/bcp logo.png" alt="Logo">
        <p>Clinic Management System</p>
    </div>

    <div class="login-container">
        <h2>Log Into Your Account</h2>
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red;">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <form id="loginForm" action="index.php" method="post">
            <label for="accountId">Account ID</label>
            <input type="text" id="accountId" name="accountId" required aria-label="Account ID">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required aria-label="Password">

            <div class="forgot-password">
                <a href="#" aria-label="Forgot password?">Forgot your password?</a>
            </div>

            <button type="submit">LOGIN</button>
        </form>

        <div id="otpModal" class="modal">
            <div class="modal-content" id="otpModalContent">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2>Verify Your Account</h2>
                <p>We emailed you a 6-digit OTP code. Enter the code below to confirm your email address.</p>

                <?php if (!empty($_SESSION['otp_error'])): ?>
                    <div class="error-message" style="color: red;">
                        <?= $_SESSION['otp_error']; ?>
                    </div>
                    <?php unset($_SESSION['otp_error']); ?>
                <?php endif; ?>

                <form id="otpForm" action="verify_otp.php" method="post">
                    <div class="otp-input">
                        <input type="text" name="otp1" maxlength="1" oninput="moveToNext(this)" required>
                        <input type="text" name="otp2" maxlength="1" oninput="moveToNext(this)" required>
                        <input type="text" name="otp3" maxlength="1" oninput="moveToNext(this)" required>
                        <input type="text" name="otp4" maxlength="1" oninput="moveToNext(this)" required>
                        <input type="text" name="otp5" maxlength="1" oninput="moveToNext(this)" required>
                        <input type="text" name="otp6" maxlength="1" oninput="moveToNext(this)" required>
                    </div>
                    <button type="submit">Verify Now</button>
                </form>
            </div>
        </div>

        <script src="js/script.js"></script>

        <script>
            window.onload = function() {
                <?php if (isset($_SESSION['otp_sent']) && $_SESSION['otp_sent']): ?>
                    showModal();
                    <?php unset($_SESSION['otp_sent']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['show_otp_modal']) && $_SESSION['show_otp_modal']): ?>
                    showModal();
                    <?php unset($_SESSION['show_otp_modal']); ?>
                <?php endif; ?>
            };

            function showModal() {
                document.getElementById('otpModal').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('otpModal').style.display = 'none';
            }

            function moveToNext(input) {
                if (input.nextElementSibling) {
                    input.nextElementSibling.focus();
                }
            }
        </script>
    </div>
</body>
</html>
