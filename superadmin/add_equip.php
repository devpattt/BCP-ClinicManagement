<?php
// Set content type to JSON
header('Content-Type: application/json');

// Include your database connection file
include '../connection.php';

// Retrieve the raw POST data and decode JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input.']);
    exit;
}

// Extract values and trim whitespace
$item    = isset($data['item']) ? trim($data['item']) : '';
$brand   = isset($data['brand']) ? trim($data['brand']) : '';
$quantity= isset($data['quantity']) ? trim($data['quantity']) : '';
$remarks = isset($data['remarks']) ? trim($data['remarks']) : '';

// Validate required fields
if (empty($item) || empty($quantity)) {
    echo json_encode(['success' => false, 'error' => 'Item and Quantity are required.']);
    exit;
}

// Ensure quantity is numeric
if (!is_numeric($quantity)) {
    echo json_encode(['success' => false, 'error' => 'Quantity must be numeric.']);
    exit;
}

// Convert quantity to integer
$quantity = (int)$quantity;

// Prepare the SQL statement to insert the data
$stmt = $conn->prepare("INSERT INTO bcp_sms3_equipments (item, brand, quantity, remarks) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => $conn->error]);
    exit;
}

// Bind parameters (using "ssis": string, string, integer, string)
$stmt->bind_param("ssis", $item, $brand, $quantity, $remarks);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
