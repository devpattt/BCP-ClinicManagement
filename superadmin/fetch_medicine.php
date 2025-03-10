<?php
include '../connection.php';

if (isset($_GET['query'])) {
    $query = "%" . $_GET['query'] . "%";

    $stmt = $conn->prepare("SELECT DISTINCT product FROM bcp_sms3_medicine WHERE product LIKE ?");
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $result = $stmt->get_result();

    $medicines = [];
    while ($row = $result->fetch_assoc()) {
        $medicines[] = $row['product'];
    }

    echo json_encode($medicines);
    $stmt->close();
    $conn->close();
}
?>
