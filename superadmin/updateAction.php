<?php
session_start();
require_once "../connection.php";

// (Optional) Check if the user is logged in.
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $newAction = $_POST['newAction'] ?? '';

    if ($id && $newAction) {
        // Use backticks around the column name 'action'
        $query = "UPDATE bcp_sms3_reqmedreqcord SET `action` = ? WHERE id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("si", $newAction, $id);
            if ($stmt->execute()){
                echo "success";
            } else {
                // Print statement error for debugging
                echo "error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "error: " . $conn->error;
        }
    } else {
        echo "Missing parameters";
    }
}
$conn->close();
?>
