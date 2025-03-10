<?php 
session_start();

include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $fullname = htmlspecialchars(trim($_POST['fullname']));
  $email = htmlspecialchars(trim($_POST['email']));
  $username = htmlspecialchars(trim($_POST['username']));
  $password = htmlspecialchars(trim($_POST['password']));
  $cpassword = htmlspecialchars(trim($_POST['cpassword']));

  if ($password !== $cpassword) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
    exit;
  }

  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO bcp_sms3_admin (fullname, email, username, password) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $fullname, $email, $username, $hashed_password);

  if ($stmt->execute()) {
    header("Location: index.php");
    exit; 
} 
  $stmt->close();
  $conn->close();
}
?>