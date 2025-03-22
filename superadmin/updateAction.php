<?php
// Only start the session if it's not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../connection.php";

// (Optional) Check if the user is logged in.
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $newAction = $_POST['newAction'] ?? '';
    // newProcess is optional.
    $newProcess = $_POST['newProcess'] ?? null;

    if ($id && $newAction) {
        if ($newProcess !== null) {
            // Update both the action and process columns.
            $query = "UPDATE bcp_sms3_reqmedreqcord SET action = ?, process = ? WHERE id = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("ssi", $newAction, $newProcess, $id);
                if ($stmt->execute()){
                    echo "success";
                } else {
                    echo "error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "error: " . $conn->error;
            }
        } else {
            // Update only the action column.
            $query = "UPDATE bcp_sms3_reqmedreqcord SET action = ? WHERE id = ?";
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param("si", $newAction, $id);
                if ($stmt->execute()){
                    echo "success";
                } else {
                    echo "error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "error: " . $conn->error;
            }
        }
    } else {
        echo "Missing parameters";
    }
}
$conn->close();
?>
