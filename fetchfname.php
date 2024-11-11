<?php

if (!isset($_SESSION['accountId'])) {
    die("User is not logged in."); 
}

include 'connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['accountId'];

$sql = "SELECT Fname FROM bcp_sms3_users WHERE accountId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = $row['Fname'];
} else {
    $fullname = "User"; 
}

$stmt->close();
$conn->close();
?>

