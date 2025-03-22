<?php
// download.php

// Start session only if not already active.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: ../superadmin/mainpage.php");
    exit();
}

include '../../connection.php'; // Adjust the path if necessary

// Get the unique_id from the URL.
$uniqueId = isset($_GET['unique_id']) ? htmlspecialchars($_GET['unique_id']) : "";
if (empty($uniqueId)) {
    die("Invalid request.");
}

// Query the database for the file name stored in the 'request' column.
$sql = "SELECT request FROM bcp_sms3_send_integ WHERE unique_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uniqueId);
$stmt->execute();
$stmt->bind_result($fileName);
if ($stmt->fetch()) {
    $stmt->close();
    // Adjust the path to your uploads folder.
    // For example, if download.php is in /superadmin/integration and uploads folder is in /BCP-ClinicManagement/uploads:
    $filePath = "../../uploads/" . $fileName;
    
    if (file_exists($filePath)) {
        // Force file download.
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($fileName) . "\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($filePath));
        flush();
        readfile($filePath);
        exit();
    } else {
        die("File not found at: " . htmlspecialchars($filePath));
    }
} else {
    $stmt->close();
    die("No file found for the given reference.");
}
?>
