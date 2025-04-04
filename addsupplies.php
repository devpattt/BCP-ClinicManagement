<?php
session_start();

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

include 'connection.php';
include 'fetchfname.php';

if (!isset($conn)) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection variable not set.']);
    exit();
}

if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection error: ' . mysqli_connect_error()]);
    exit();
}

function generateUniqueCode($conn) {
    do {
        $code = rand(1000, 9999);
        $query = "SELECT id FROM bcp_sms3_medicalsupplies WHERE code = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            error_log("generateUniqueCode: Prepare failed: " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Prepare failed in generateUniqueCode.']);
            exit();
        }
        $stmt->bind_param("i", $code);
        if (!$stmt->execute()) {
            error_log("generateUniqueCode: Execute failed: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Execute failed in generateUniqueCode.']);
            exit();
        }
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
    } while ($exists);
    return $code;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("POST Data: " . print_r($_POST, true));

    $itemName = isset($_POST['item_name']) ? trim($_POST['item_name']) : "";
    $category = isset($_POST['category']) ? trim($_POST['category']) : "";
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $unit = isset($_POST['unit']) ? intval($_POST['unit']) : 0;

    if (empty($itemName) || empty($category) || empty($quantity) || empty($unit)) {
        echo json_encode(['status' => 'error', 'message' => 'Item name, category, quantity, and unit are required.']);
        exit();
    }

    // Check if item already exists
    $checkStmt = $conn->prepare("SELECT id FROM bcp_sms3_medicalsupplies WHERE item_name = ?");
    $checkStmt->bind_param("s", $itemName);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This brand name already exists.']);
        $checkStmt->close();
        exit();
    }
    
    $checkStmt->close();

    $code = generateUniqueCode($conn);

    $stmt = $conn->prepare("INSERT INTO bcp_sms3_medicalsupplies (code, item_name, category, quantity, unit) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("INSERT prepare failed: " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("issii", $code, $itemName, $category, $quantity, $unit);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Item added successfully.']);
    } else {
        error_log("INSERT execute failed: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>
