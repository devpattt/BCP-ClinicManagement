<?php


if (!isset($_SESSION['username'])) {
    die("User is not logged in."); 
}

include 'connection.php';

// Create a new connection instance
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['username'];

$sql = "(SELECT fullname, username, password, email, 'user' AS user_type FROM bcp_sms3_users WHERE username = ?)
    UNION ALL
    (SELECT fullname, username, password, email, 'admin' AS user_type FROM bcp_sms3_admin WHERE username = ?)
    UNION ALL
    (SELECT fullname, username, password, email, 'super_admin' AS user_type FROM bcp_sms3_super_admin WHERE username = ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user_id, $user_id, $user_id);    
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
