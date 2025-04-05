<?php
session_start();
include '../connection.php';

header("Content-Type: application/json");

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit();
}

// Check required parameters
if (!isset($_POST['id']) || !isset($_POST['type'])) {
    echo json_encode(["status" => "error", "message" => "Missing parameters."]);
    exit();
}

$id = intval($_POST['id']);
$type = $_POST['type'];

if ($type === 'accept') {
    // Update the request: set action to "Accepted"
    $stmt = $conn->prepare("UPDATE bcp_sms3_req SET action = 'Accepted' WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Request accepted."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update request."]);
    }
    $stmt->close();
} elseif ($type === 'deny') {
    // For deny, make sure a remarks value is provided.
    if (!isset($_POST['rem2']) || trim($_POST['rem2']) === "") {
        echo json_encode(["status" => "error", "message" => "Remarks required for denial."]);
        exit();
    }
    $rem2 = trim($_POST['rem2']);
    // Update the request: set rem2 with provided remarks.
    $stmt = $conn->prepare("UPDATE bcp_sms3_req SET rem2 = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("si", $rem2, $id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Request denied."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update request."]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request type."]);
}

$conn->close();
?>
