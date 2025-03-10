<?php
 session_start();
 
   include 'connection.php'; // Include database connection
   if (isset($_SESSION['otp'])) {
    $entered_otp = '';
    for ($i = 1; $i <= 6; $i++) {
        $entered_otp .= $_POST['otp' . $i]; 
    }

    if ($_SESSION['otp'] == $entered_otp) {
        $_SESSION['loggedin'] = true; 
        unset($_SESSION['otp']); 

        // Redirect based on user type
        switch ($_SESSION['user_type']) {
            case 'admin':
                header("Location: clinic-dashboard.php");
                break;
            case 'super':
                header("Location: superadmin/mainpage.php");
                break;
            case 'user':
                header("Location: userside/userside.php");
                break;
            default:
                // Optionally, handle unexpected user types
                header("Location: index.php"); // Change to an appropriate error page
                break;
        }
        
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
?>