<?php
include 'connection.php';

$sql = "SELECT * FROM bcp_sms3_medicalsupplies ORDER BY id ASC";
$result = $conn->query($sql);

if (!$result) {
    // Query failed, return an error message
    echo json_encode(['status' => 'error', 'message' => 'Error fetching data: ' . $conn->error]);
    exit;
}

$supplies = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $supplies[] = $row;
    }
}

echo json_encode($supplies);

// Optionally, close the connection
$conn->close();
?>
