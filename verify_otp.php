<?php
session_start();
if (isset($_SESSION['otp'])) {
    $entered_otp = '';
    for ($i = 1; $i <= 6; $i++) {
        $entered_otp .= $_POST['otp' . $i]; 
    }

    if ($_SESSION['otp'] == $entered_otp) {
        $_SESSION['loggedin'] = true; 
        unset($_SESSION['otp']); 
        header('Location: clinic-dashboard.php'); 
        exit();
    } else {
        $_SESSION['otp_error'] = "Invalid OTP. Please try again.";
        $_SESSION['show_otp_modal'] = true; 
        header('Location: index.php');
        exit();
    }
} else {
    $_SESSION['otp_error'] = "No OTP sent!";
    $_SESSION['show_otp_modal'] = true; 
    header('Location: index.php');
    exit();
}
