<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit();
}

include '../connection.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("No record ID provided.");
}

$id = intval($_GET['id']);

// Prepare and execute deletion query
$stmt = $conn->prepare("DELETE FROM bcp_sms3_patients WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
  header("Location: tables-data.php?msg=deleted");
  exit();
} else {
  echo "Error deleting record: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
