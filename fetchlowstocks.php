<?php
include 'connection.php';

// Function to update stock when quantity is less than or equal to 0.
// If quantity <= 0 and unit > 0, subtract 1 from unit and add 10 to quantity.
function updateStock($conn) {
    // Select rows using the actual column names: id, unit, and quantity
    $query = "SELECT id, unit, quantity FROM bcp_sms3_medicalsupplies";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $unit = (int)$row['unit'];
        $quantity = (int)$row['quantity'];

        // If quantity is less than or equal to 0 and there is at least one unit available...
        if ($quantity <= 0 && $unit > 0) {
            $newUnit = $unit - 1;
            $newQuantity = $quantity + 10; // Add 10 to quantity
            if ($newQuantity < 0) {
                $newQuantity = 0;
            }
            $updateQuery = "UPDATE bcp_sms3_medicalsupplies 
                            SET unit = $newUnit, 
                                quantity = $newQuantity
                            WHERE id = $id";
            $conn->query($updateQuery);
        }
    }
}

// Function to adjust stock when quantity is greater than or equal to 10.
// For every 10 in quantity, convert that into an additional unit.
function adjustExcessQuantity($conn) {
    $updateQuery = "UPDATE bcp_sms3_medicalsupplies 
                    SET unit = unit + (quantity DIV 10), 
                        quantity = quantity % 10
                    WHERE quantity >= 10";
    $conn->query($updateQuery);
}

// Call the functions before fetching data.
updateStock($conn);
adjustExcessQuantity($conn);

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type == "low") {
    $query = "SELECT code, item_name, unit, quantity 
              FROM bcp_sms3_medicalsupplies 
              WHERE unit < 1 AND NOT (unit = 0 AND quantity = 0)";
} elseif ($type == "out") {
    $query = "SELECT code, item_name, unit, quantity 
              FROM bcp_sms3_medicalsupplies 
              WHERE unit = 0 AND quantity = 0";
} else {
    $query = "SELECT COUNT(*) AS low_stock_count 
              FROM bcp_sms3_medicalsupplies 
              WHERE unit < 1 AND NOT (unit = 0 AND quantity = 0)";
              
    $queryOutOfStock = "SELECT COUNT(*) AS out_of_stock_count 
                        FROM bcp_sms3_medicalsupplies 
                        WHERE unit = 0 AND quantity = 0";
    
    $resultLowStock = $conn->query($query);
    $resultOutOfStock = $conn->query($queryOutOfStock);
    
    $lowStockCount = 0;
    $outOfStockCount = 0;
    
    if ($resultLowStock && $row = $resultLowStock->fetch_assoc()) {
        $lowStockCount = $row['low_stock_count'];
    }
    
    if ($resultOutOfStock && $row = $resultOutOfStock->fetch_assoc()) {
        $outOfStockCount = $row['out_of_stock_count'];
    }
    
    echo json_encode([
        'lowStockCount' => $lowStockCount,
        'outOfStockCount' => $outOfStockCount
    ]);
    exit();
}

$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = [
        'code'      => $row['code'], 
        'item_name' => $row['item_name'],
        'unit'      => $row['unit'],
        'quantity'  => $row['quantity']
    ];
}

echo json_encode($data);
?>
