<?php
// viewsend.php – displays records from bcp_sms3_send_integ with Download and Next buttons
include '../connection.php';

$query = "SELECT id, unique_id, request, reason, sent_date 
          FROM bcp_sms3_send_integ 
          ORDER BY sent_date DESC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-id='" . htmlspecialchars($row['id']) . "' data-uniqueid='" . htmlspecialchars($row['unique_id']) . "'>";
        echo "<td>" . htmlspecialchars($row['request']) . "</td>";
        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sent_date']) . "</td>";
        // Download column – link to downloadTxt.php (ensure that file exists)
        echo "<td><a href='downloadTxt.php?id=" . urlencode($row['id']) . "' class='btn btn-link btn-sm'><i class='bi bi-download'></i></a></td>";
        // Next column – button that redirects to nextPage.php with unique_id (and record_id if needed)
        echo "<td><a href='nextPage.php?unique_id=" . urlencode($row['unique_id']) . "&record_id=" . urlencode($row['id']) . "' class='btn btn-info btn-sm'>Next</a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
}
$conn->close();
?>
