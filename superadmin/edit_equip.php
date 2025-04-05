<?php
session_start();
header('Content-Type: application/json');

// Adjust the path based on your folder structure.
include('../connection.php'); 

// Retrieve the JSON input.
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check for valid input and required 'id'.
if (!$data || !isset($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request data.']);
    exit();
}

$id = (int)$data['id'];

// If the request includes an 'item' key, treat it as an edit operation.
if (isset($data['item'])) {
    // Retrieve and sanitize values.
    $item = trim($data['item']);
    $brand = isset($data['brand']) ? trim($data['brand']) : "";
    $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 0;
    $remarks = isset($data['remarks']) ? trim($data['remarks']) : "";

    // Prepare and execute the update statement.
    $stmt = $conn->prepare("UPDATE bcp_sms3_equipments SET item = ?, brand = ?, quantity = ?, remarks = ? WHERE id = ?");
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("ssisi", $item, $brand, $quantity, $remarks, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
} else {
    // Otherwise, treat the request as a deletion.
    $stmt = $conn->prepare("DELETE FROM bcp_sms3_equipments WHERE id = ?");
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
}

$conn->close();
?>
