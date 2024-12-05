<?php
include 'connection.php'; 

$query = "SELECT COUNT(*) AS low_stock_count FROM bcp_sms3_medicalsupplies WHERE quantity <= minimum_stock";
$result = $conn->query($query);

$lowStockCount = 0; 

if ($result && $row = $result->fetch_assoc()) {
    $lowStockCount = $row['low_stock_count'];
}

echo json_encode(['lowStockCount' => $lowStockCount]);
?>
