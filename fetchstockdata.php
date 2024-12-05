<?php
include 'connection.php'; 

$response = [];

$out_of_stock_query = "SELECT COUNT(*) AS out_of_stock_count FROM bcp_sms3_medicalsupplies WHERE quantity = 0";
$out_of_stock_result = $conn->query($out_of_stock_query);

if ($out_of_stock_result && $row = $out_of_stock_result->fetch_assoc()) {
    $response['out_of_stock'] = $row['out_of_stock_count'];
} else {
    $response['out_of_stock'] = 0;
}

$low_stock_query = "SELECT COUNT(*) AS low_stock_count FROM bcp_sms3_medicalsupplies WHERE quantity <= minimum_stock";
$low_stock_result = $conn->query($low_stock_query);

if ($low_stock_result && $row = $low_stock_result->fetch_assoc()) {
    $response['low_stock'] = $row['low_stock_count'];
} else {
    $response['low_stock'] = 0;
}

echo json_encode($response);
?>
