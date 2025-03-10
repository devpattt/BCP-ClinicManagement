<?php
include '../connection.php';

$query = "SELECT ID, name, item, category, quantity, action FROM bcp_sms3_reqsupplies ORDER BY ID DESC";
$result = $conn->query($query);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>
