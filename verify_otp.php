<?php
session_start();

// Check if the session contains the OTP
if (isset($_SESSION['otp'])) {
    $entered_otp = '';
    
    // Collect the entered OTP from the form inputs
    for ($i = 1; $i <= 6; $i++) {
        $entered_otp .= $_POST['otp' . $i]; // Concatenate all the OTP inputs
    }

    // Validate the entered OTP
    if ($_SESSION['otp'] == $entered_otp) {
        // OTP is valid, proceed with login or redirect
        $_SESSION['loggedin'] = true; // Set this variable
        unset($_SESSION['otp']); // Clear the OTP from session
        header('Location: clinic-dashboard.php'); // Redirect to the dashboard
        exit();
    } else {
        // OTP is invalid
        echo "Invalid OTP!";
    }
} else {
    // Handle case where OTP session does not exist
    echo "No OTP sent!";
}
?>
