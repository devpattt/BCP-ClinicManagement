<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: clinic-dashboard.php");
    exit();
}

include 'connection.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT username, password, email, 'user' AS user_type FROM bcp_sms3_users WHERE username = ?
        UNION ALL
        SELECT username, password, email, 'admin' AS user_type FROM bcp_sms3_admin WHERE username = ?
        UNION ALL
        SELECT username, password, email, 'super' AS user_type FROM bcp_sms3_super_admin WHERE username = ?";


    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("sss", $username, $username, $username);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $otp = rand(100000, 999999); 
            $_SESSION['username'] = $user['username'];
            $_SESSION['otp'] = $otp;
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type']; // Store user type in session
            //ito bago din to
            $_SESSION['session_id'] = session_id(); // Store session ID

            echo "<script>
                  localStorage.setItem('session_active', 'true');
                  localStorage.setItem('session_id', '" . session_id() . "');
                  </script>";

                  //hanggang dito sa echo

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
                $mail->addAddress($user['email']);

                $mail->isHTML(true);
                $mail->Subject = 'Your Secure 2-Factor Authentication (2FA) Code';
                $mail->Body    = 'Hello,<br><br>Your One-Time Password (OTP) for secure access is: <b style="font-size: 18px; color: #007BFF;">' . $otp . '</b>.<br><br>'
                               . 'This code is valid for the next 5 minutes. Please do not share it with anyone for your security.<br><br>'
                               . 'If you did not request this code, please ignore this email or contact support immediately.<br><br>'
                               . 'Thank you for using our services.<br>'
                               . '<i>Your Trusted Team</i>';
                $mail->AltBody = 'Hello, '
                               . 'Your One-Time Password (OTP) for secure access is: ' . $otp . '. '
                               . 'This code is valid for the next 5 minutes. Please do not share it with anyone for your security. '
                               . 'If you did not request this code, please ignore this email or contact support immediately. '
                               . 'Thank you for using our services. '
                               . '- Your Trusted Team';
                

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
    <style>
        .login-container {  
        background-color: white;
        padding: 20px;
        border-radius: 25px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 300px;
        text-align: center;
        border: 1px solid #000000;
        margin: 0 auto;
    }
    
    h2 {
        color: #000000;
        margin-bottom: 20px;
    }
    
    label {
        display: block;
        text-align: left;
        color: #000000;
        margin: 10px 0 5px;
    }
    
    .text {
        color: #000000;
    }
    
    #username, #password {
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: 2px;
        border: 1px solid black;
    }
    
    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    
    .forgot-password {
        text-align: right;
        margin-bottom: 20px;
    }
    
    .forgot-password a {
        color: #000000;
        text-decoration: none;
        font-size: 15px;
    }
    
    .forgot-password a:hover {
        text-decoration: none;
    }

    </style>
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
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required aria-label="Account ID">

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
                        <input type="text" name="otp1" maxlength="1" oninput="handleInput(this)" onkeydown="handleKeyDown(this, event)" required>
                        <input type="text" name="otp2" maxlength="1" oninput="handleInput(this)" onkeydown="handleKeyDown(this, event)" required>
                        <input type="text" name="otp3" maxlength="1" oninput="handleInput(this)" onkeydown="handleKeyDown(this, event)" required>
                        <input type="text" name="otp4" maxlength="1" oninput="handleInput(this)" onkeydown="handleKeyDown(this, event)" required>
                        <input type="text" name="otp5" maxlength="1" oninput="handleInput(this)" onkeydown="handleKeyDown(this, event)" required>
                        <input type="text" name="otp6" maxlength="1" oninput="handleInput(this)" onkeydown="handleKeyDown(this, event)" required>
                    </div>
                    <button type="submit">Verify Now</button>
                </form>
            </div>
        </div>

        <script src="js/script.js"></script>

        <script>
            window.onload = function () {
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

            function handleInput(input) {
                if (input.value.length === 1 && input.nextElementSibling) {
                    input.nextElementSibling.focus();
                }
            }

            function handleKeyDown(input, event) {
                if (event.key === 'Backspace') {
                    if (input.value === '' && input.previousElementSibling) {
                        input.previousElementSibling.focus();
                    }
                }
                if (!/^[0-9]$/.test(event.key) && event.key !== 'Backspace') {
                    event.preventDefault();
                }
            }
        </script> 

        
<!-- ITO BAGO TO -->
<script>
window.addEventListener("storage", function(event) {
    if (event.key === "forceLogout") {
        showLogoutModal();
    }
});

function showLogoutModal() {
    let modal = document.createElement("div");
    modal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center;">
            <div style="background: white; padding: 20px; border-radius: 10px; text-align: center;">
                <p style="font-size: 18px;">We've detected that you logged out in another tab.</p>
                <button onclick="redirectToLogin()" style="background: #007BFF; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px;">OK</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function redirectToLogin() {
    window.location.href = "index.php"; // Redirect back to login page
}
</script>



    </div>
</body>
</html>