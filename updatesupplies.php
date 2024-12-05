<?php
include 'connection.php';

$id = $_POST['id'];
$item_name = $_POST['item_name'];
$category = $_POST['category'];
$quantity = $_POST['quantity'];
$minimum_stock = $_POST['minimum_stock'];

$sql = "UPDATE bcp_sms3_medicalsupplies
        SET item_name='$item_name', category='$category', quantity=$quantity, minimum_stock=$minimum_stock 
        WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Supply updated successfully.";
} else {
    echo "Error: " . $conn->error;
}
?>
