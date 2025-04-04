<?php
include 'connection.php';

$id = $_POST['code'];
//$item_name = $_POST['item_name'];
//$category = $_POST['category'];
$quantity = $_POST['quantity'];
$unit = $_POST['unit'];

$sql = "UPDATE bcp_sms3_medicalsupplies
        SET quantity=$quantity, unit=$unit
        WHERE code='$id'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>
