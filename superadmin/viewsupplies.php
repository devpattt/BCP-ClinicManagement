<?php 
include '../connection.php';

$stmt = $conn->prepare("SELECT item, category, quantity, action, DATE_FORMAT(request_date, '%Y-%m-%d %h:%i %p') AS formatted_request_date FROM bcp_sms3_reqsupplies ORDER BY request_date DESC"); // Sort by newest requests first
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["item"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["category"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["quantity"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["formatted_request_date"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["action"]) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='12'>No records found</td></tr>";
}

$stmt->close();
?>
