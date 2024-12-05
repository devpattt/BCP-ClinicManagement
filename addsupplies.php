<?php
include 'connection.php';

$item_name = $_POST['item_name'];
$category = $_POST['category'];
$quantity = $_POST['quantity'];
$minimum_stock = $_POST['minimum_stock'];

$sql = "INSERT INTO bcp_sms3_medicalsupplies(item_name, category, quantity, minimum_stock) 
        VALUES ('$item_name', '$category', $quantity, $minimum_stock)";

if ($conn->query($sql) === TRUE) {
    echo "Supply added successfully.";
} else {
    echo "Error: " . $conn->error;
}
?>
