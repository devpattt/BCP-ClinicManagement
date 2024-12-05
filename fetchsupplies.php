<?php
include 'connection.php';

$sql = "SELECT * FROM bcp_sms3_medicalsupplies ORDER BY id ASC";
$result = $conn->query($sql);

$supplies = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $supplies[] = $row;
    }
}

echo json_encode($supplies);
?>
