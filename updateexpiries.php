<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Make sure the user is logged in
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Make sure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// Make sure the expiration data exists
if (!isset($_POST['expiration']) || !is_array($_POST['expiration'])) {
    echo json_encode(['status' => 'error', 'message' => 'No expiration data provided']);
    exit;
}

$expirations = $_POST['expiration'];
$errors = [];

foreach ($expirations as $code => $date) {
    $date = trim($date);
    if ($date === '') continue;

    // Format date to YYYY-MM-DD
    $formattedDate = date('Y-m-d', strtotime($date));

    $stmt = $conn->prepare(
      "UPDATE bcp_sms3_medicalsupplies
          SET expiration   = ?,
              last_updated = NOW()
        WHERE code         = ?"
    );
    if ($stmt) {
        $stmt->bind_param("si", $formattedDate, $code);
        if (!$stmt->execute()) {
            $errors[] = "Failed to update code $code: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errors[] = "Failed to prepare statement for code $code: " . $conn->error;
    }
}

// — send JSON response back to the caller —
if (empty($errors)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode([
      'status'  => 'error',
      'message' => implode('; ', $errors)
    ]);
}
exit;
