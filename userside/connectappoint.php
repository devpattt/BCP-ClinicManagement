<?php

include '../connection.php';

if (!isset($_SESSION['username'])) {
    die("User is not logged in.");
}

$username = $_SESSION['username']; 

// Fetch fullname from the users table
$sql = "SELECT fullname FROM bcp_sms3_users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = $row['fullname'];
} else {
    $fullname = "Unknown"; 
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['email']));
    $reason = htmlspecialchars(trim($_POST['reason']));
    $date = htmlspecialchars(trim($_POST['date']));
    $formatted_time = date("h:i A", strtotime($row['time']));
    // Store current time in 12-hour format with AM/PM

    if (empty($email) || empty($reason) || empty($date)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields']);
        exit;
    }

    // Insert data into database
    $stmt = $conn->prepare("INSERT INTO bcp_sms3_appointment (name, email, reason, date, time) VALUES (?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("sssss", $fullname, $email, $reason, $date, $current_time);
        
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: userside.php"); // Redirect after successful booking
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to insert data']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }

    $conn->close();
}
?>
