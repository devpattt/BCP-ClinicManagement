<?php
include 'connection.php';

header('Content-Type: application/json');

$itemName = isset($_POST['item_name']) ? trim($_POST['item_name']) : "";

if (empty($itemName)) {
    echo json_encode(['exists' => false]);
    exit;
}

$query = "SELECT COUNT(*) AS count FROM bcp_sms3_medicalsupplies WHERE item_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $itemName);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['exists' => $row['count'] > 0]);
exit;
?>
