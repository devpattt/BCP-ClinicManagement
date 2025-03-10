<?php

if (!isset($_SESSION['username'])) {
    die("User is not logged in."); 
}

include 'connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['username'];

$sql = "SELECT fullname FROM bcp_sms3_users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = $row['fullname'];
} else {
    $fullname = "User"; 
}

$stmt->close();
$conn->close();
?>

