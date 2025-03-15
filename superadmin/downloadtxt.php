<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../superadmin/mainpage.php");
    exit();
}

include '../connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT request FROM bcp_sms3_send_integ WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($txtContent);
    if ($stmt->fetch()) {
        // Set headers to force download as a TXT file.
        header("Content-Type: text/plain; charset=UTF-8");
        header("Content-Disposition: attachment; filename=StoredTXT_{$id}.txt");
        echo $txtContent;
    } else {
        echo "No record found.";
    }
    $stmt->close();
} else {
    echo "Invalid ID.";
}
$conn->close();
?>