<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../superadmin/mainpage.php");
    exit();
}

include '../connection.php';

// Check if unique_id is provided.
if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $uniqueId = $_GET['unique_id'];
    // Retrieve the file name from the bcp_sms3_reqmedreqcord table.
    $stmt = $conn->prepare("SELECT request FROM bcp_sms3_reqmedreqcord WHERE unique_id = ?");
    $stmt->bind_param("s", $uniqueId);
    $stmt->execute();
    $stmt->bind_result($fileName);
    if ($stmt->fetch()) {
        $stmt->close();
        // Adjust the path to your uploads folder relative to this file.
        // For example, if download.php is in /superadmin/integration/ and uploads folder is at /BCP-ClinicManagement/uploads/:
        $filePath = "../uploads/" . $fileName;
        
        if (!file_exists($filePath)) {
            die("File not found at: " . htmlspecialchars($filePath));
        }
        
        // Determine the content type based on file extension.
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        switch ($extension) {
            case "pdf":
                $contentType = "application/pdf";
                break;
            case "doc":
                $contentType = "application/msword";
                break;
            case "docx":
                $contentType = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                break;
            default:
                $contentType = "application/octet-stream";
                break;
        }
        
        // Set headers to force download.
        header("Content-Description: File Transfer");
        header("Content-Type: " . $contentType);
        header("Content-Disposition: attachment; filename=\"" . basename($fileName) . "\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($filePath));
        flush();
        readfile($filePath);
        exit();
    } else {
        $stmt->close();
        die("No record found for the given reference.");
    }
} else {
    die("Invalid ID.");
}

$conn->close();
?>
