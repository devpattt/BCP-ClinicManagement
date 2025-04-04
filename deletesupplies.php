<?php
header("Content-Type: application/json");

// Include your existing DB connection
include 'connection.php';

// Check if POST 'code' is set
if (isset($_POST['code'])) {
    $code = $_POST['code'];

    // Use the correct table name from your DB
    $stmt = $conn->prepare("DELETE FROM bcp_sms3_medicalsupplies WHERE code = ?");
    $stmt->bind_param("s", $code);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete supply."]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Missing supply code."]);
}

$conn->close();
?>
