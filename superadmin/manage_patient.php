<?php
header('Content-Type: application/json');
include '../connection.php';

// Get the JSON input from the request body
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($input['uid'])) {
    echo json_encode(['success' => false, 'error' => 'No UID provided']);
    exit();
}
if (empty($input['type']) || !in_array($input['type'], ['edit', 'delete'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid or missing type']);
    exit();
}

$uid   = $input['uid'];
$type  = $input['type'];
$tables = ['bcp_sms3_patients', 'bcp_sms3_other_patients'];

try {
    foreach ($tables as $table) {
        if ($type === 'edit') {
            // Clear the "action" field
            $sql  = "UPDATE `$table` SET action = '' WHERE unique_id = ?";
        } else { // delete
            // Remove the record entirely
            $sql  = "DELETE FROM `$table` WHERE unique_id = ?";
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed on $table: " . $conn->error);
        }
        $stmt->bind_param("s", $uid);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed on $table: " . $stmt->error);
        }
        $stmt->close();
    }

    // If we made it here, both tables were touched
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
