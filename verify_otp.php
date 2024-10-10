<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = $_POST['otp'];

    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $entered_otp) {
        // OTP is valid, log in the user
        echo "Login successful!";
        unset($_SESSION['otp']);
        header('Location: clinic-dashboard.php');
    } else {
        echo "Invalid OTP!";
    }
}
?>
