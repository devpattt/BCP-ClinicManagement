<?php

if (!isset($_SESSION['accountId'])) {
    die("User is not logged in."); 
}

include 'connection.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
$user_id = $_SESSION['accountId']; // Assuming the user's ID is stored in the session

$sql = "SELECT Fname FROM bcp_sms3_users WHERE accountId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Assuming ID is an integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch user data
    $row = $result->fetch_assoc();
    $fullname = $row['Fname'];
} else {
    $fullname = "User"; // Fallback name if not found
}

// Close connections
$stmt->close();
$conn->close();
?>

