<?php
header('Content-Type: application/json');
include '../connection.php';

// Get the JSON input from the request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['uid']) || empty($input['uid'])) {
    echo json_encode(['success' => false, 'error' => 'No UID provided']);
    exit();
}

if (!isset($input['type']) || !in_array($input['type'], ['edit', 'delete'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid or missing type']);
    exit();
}

$uid = $input['uid'];
$type = $input['type'];

if ($type === 'edit') {
    // Edit action: clear the "action" field in the database
    $stmt = $conn->prepare("UPDATE bcp_sms3_patients SET action = '' WHERE unique_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Database prepare failed: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("s", $uid);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No record updated.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Execution failed: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
} elseif ($type === 'delete') {
    // Delete action: remove the record from the database
    $stmt = $conn->prepare("DELETE FROM bcp_sms3_patients WHERE unique_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Database prepare failed: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("s", $uid);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No record deleted.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Execution failed: ' . $stmt->error]);
    }
    $stmt->close();
    exit();
}
?>
