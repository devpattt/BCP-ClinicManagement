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

        $user_type = $_SESSION['user_type'];

        // Redirect based on user type
        switch ($user_type) {
        case 'user':
            // Check if the user has completed the health questionnaire
            $query = "SELECT completed_questionnaire FROM bcp_sms3_users WHERE username = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($completed_questionnaire);
            $stmt->fetch();
            $stmt->close();

            if ($completed_questionnaire == 0) {
                header("Location: userside/health_question.php");
                exit();
            }

            // If already completed, redirect to the user dashboard
            header("Location: userside/userside.php");
            exit();

        case 'admin':
            header("Location: clinic-dashboard.php");
            exit();

        case 'super':
            header("Location: superadmin/mainpage.php");
            exit();

        default:
            // If user type is unknown, redirect to login page
            header("Location: index.php");
            exit();
    }
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