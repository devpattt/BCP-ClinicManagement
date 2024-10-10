<?php
session_start();
include 'connection.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accountId = $_POST['accountId']; 
    $password = $_POST['password'];

    $sql = "SELECT * FROM bcp_sms3_users WHERE accountId = ?";
    $stmt = $conn->prepare($sql);

    // Check if prepare failed
    if (!$stmt) {
        die("SQL Prepare Error: " . $conn->error);
    }

    $stmt->bind_param("i", $accountId); // Binding accountId as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            $otp = rand(100000, 999999);  // 6-digit OTP
            $_SESSION['otp'] = $otp;      // Save OTP in session
            $_SESSION['email'] = $user['Email'];  // Save user email for OTP sending

            // Send OTP via email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'bcpclinicmanagement@gmail.com';  // Your Gmail email
                $mail->Password   = 'fvzf ldba jroq xzjf'; // Your app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('bcpclinicmanagement@gmail.com', 'Clinic Management System');
                $mail->addAddress($user['Email']);  // Send OTP to user's email

                // Email content
                $mail->isHTML(true);
                $mail->Subject = '2 Factor Authentication';
                $mail->Body    = 'Your OTP code is <b>' . $otp . '</b>';
                $mail->AltBody = 'Your OTP code is ' . $otp;

                $mail->send();
                echo "<script>showModal();</script>"; // Show OTP modal
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            // Invalid login attempt
            $error = "Invalid account credentials!";
        }
    } else {
        $error = "Invalid account ID!";
    }

    // Close statement and connection
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
    <title>Login - CMS</title>
</head>
<style>
    body {
    background-color: #f4f4f4;
    /*background-image: url('assets/img/bcp\ bg.jpg');*/
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    margin: 0;
}

.logo {
    text-align: center;
    margin-bottom: 5px;
}

.logo img {
    max-width: 30%;
    height: auto;
}

.logo p {
    margin-top: 10px;
    font-size: 1.2em;
    color: #333; 
}

.login-container {  
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 300px;
    text-align: center;
    border: 1px solid #999797;
    margin: 0 auto;
}

h2 {
    color: #333;
    margin-bottom: 20px;
}

label {
    display: block;
    text-align: left;
    color: #333;
    margin: 10px 0 5px;
}

#accountId, #password {
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
    color: #007BFF;
    text-decoration: none;
    font-size: 12px;
}

.forgot-password a:hover {
    text-decoration: underline;
}

button {
    background-color: #333;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    width: 100%;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #555;
}

/* Style for the modal background (dimming effect) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
    justify-content: center; /* Center the modal horizontally */
    align-items: center; /* Center the modal vertically */
}

/* Modal content box */
.modal-content {
    background-color: #fefefe;
    padding: 20px;
    border-radius: 10px;
    width: 100%;
    max-width: 400px; /* Modal width */
    margin: auto;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    animation: fadeIn 0.3s; /* Smooth appear animation */
    position: relative;
}

/* Close button in the top-right corner */
.close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    color: #333;
    font-size: 22px;
    font-weight: bold;
    cursor: pointer;
}

.close-btn:hover,
.close-btn:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

/* Label and input styles */
label {
    display: block;
    margin-bottom: 10px;
    font-size: 16px;
    color: #333;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
}

/* Button styling */
button {
    background-color: #333;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-size: 16px;
}

button:hover {
    background-color: #555;
}

/* Animation for smooth appearance */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

</style>
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
        <form id="loginForm" action="login.php" method="post">
            <label for="accountId">Account ID</label>
            <input type="text" id="accountId" name="accountId" required aria-label="Account ID">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required aria-label="Password">

            <div class="forgot-password">
                <a href="#" aria-label="Forgot password?">Forgot your password?</a>
            </div>

            <button type="submit">LOGIN</button>
        </form>

        <!-- OTP Modal -->
        <div id="otpModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2>Enter OTP</h2>
                <form id="otpForm" action="verify_otp.php" method="post">
                    <label for="otp">OTP:</label>
                    <input type="text" id="otp" name="otp" required><br><br>
                    <button type="submit">Verify OTP</button>
                </form>
            </div>
        </div>

        <script src="js/script.js"></script>

        <script>
            function showModal() {
                document.getElementById('otpModal').style.display = 'flex';
            }

            function closeModal() {
                document.getElementById('otpModal').style.display = 'none';
            }
        </script>
    </div>
</body>
</html> 