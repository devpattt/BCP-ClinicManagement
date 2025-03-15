<?php
// viewreqforinteg.php
include '../connection.php';

$query = "SELECT id, unique_id, request, reason, request_at, action, process 
          FROM bcp_sms3_reqmedreqcord 
          ORDER BY request_at DESC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Store unique_id as a data attribute in addition to id.
        echo "<tr data-id='" . htmlspecialchars($row['id']) . "' data-uniqueid='" . htmlspecialchars($row['unique_id']) . "'>";
        echo "<td>" . htmlspecialchars($row['request']) . "</td>";
        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
        echo "<td>" . htmlspecialchars($row['request_at']) . "</td>";
        echo "<td class='actions-cell'>" . htmlspecialchars($row['action']) . "</td>";
        echo "<td>" . htmlspecialchars($row['process']) . "</td>";
        // Process Buttons Column:
        echo "<td>";
        if (strtolower(trim($row['action'])) === "accepted") {
            // If already accepted, disable Accept and Done; enable Send.
            echo "<button class='btn btn-success btn-sm accept-btn me-1' disabled>Accept</button> ";
            echo "<button class='btn btn-warning btn-sm send-btn me-1' onclick='processRow(this)'>Send</button> ";
            echo "<button class='btn btn-info btn-sm done-btn' disabled>Done</button>";
        } else {
            // Otherwise, only Accept is enabled.
            echo "<button class='btn btn-success btn-sm accept-btn me-1' onclick='processRow(this)'>Accept</button> ";
            echo "<button class='btn btn-warning btn-sm send-btn me-1' onclick='processRow(this)' disabled>Send</button> ";
            echo "<button class='btn btn-info btn-sm done-btn' onclick='processRow(this)' disabled>Done</button>";
        }
        echo "</td>";
        // Download Column:
        echo "<td>";
        echo "<a href='../uploads/" . urlencode($row['request']) . "' download class='btn btn-link btn-sm'><i class='bi bi-download'></i></a>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>Error: " . htmlspecialchars($conn->error) . "</td></tr>";
}
$conn->close();
?>
