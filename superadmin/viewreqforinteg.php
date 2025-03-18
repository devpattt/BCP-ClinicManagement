<?php
// viewreqforinteg.php
include '../connection.php';

// Determine the current page based on the script name.
$current_file = basename($_SERVER['PHP_SELF']);

$query = "SELECT id, unique_id, request, reason, request_at, action, process 
          FROM bcp_sms3_reqmedreqcord 
          ORDER BY request_at DESC";
$result = $conn->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Normalize the values for comparison.
        $action  = strtolower(trim($row['action']));
        $process = strtolower(trim($row['process']));
        
        // If the current page is viewdone.php, show only rows with Accepted & Done.
        if ($current_file === 'viewdone.php') {
            if (!($action === "accepted" && $process === "done")) {
                continue; // Skip rows not matching criteria.
            }
        } else {
            // For integ.php, skip rows that have been completed (Accepted and Done).
            if ($action === "accepted" && $process === "done") {
                continue;
            }
        }
        
        // Render the row.
        echo "<tr data-id='" . htmlspecialchars($row['id']) . "' data-uniqueid='" . htmlspecialchars($row['unique_id']) . "'>";
        echo "<td>" . htmlspecialchars($row['request']) . "</td>";
        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
        echo "<td>" . htmlspecialchars($row['request_at']) . "</td>";
        echo "<td class='actions-cell'>" . htmlspecialchars($row['action']) . "</td>";
        echo "<td>" . htmlspecialchars($row['process']) . "</td>";
        // Process Buttons Column:
        echo "<td>";
        if ($action === "accepted") {
            if ($process === "done") {
                // If action is Accepted and process is Done: disable both buttons.
                echo "<button class='btn btn-success btn-sm accept-btn me-1' disabled>Accept</button> ";
                echo "<button class='btn btn-warning btn-sm send-btn me-1' disabled>Send</button> ";
            } else {
                // If Accepted but process is Pending: disable Accept; enable Send.
                echo "<button class='btn btn-success btn-sm accept-btn me-1' disabled>Accept</button> ";
                echo "<button class='btn btn-warning btn-sm send-btn me-1' onclick='processRow(this)'>Send</button> ";
            }
        } else {
            // For pending actions: only Accept is enabled.
            echo "<button class='btn btn-success btn-sm accept-btn me-1' onclick='processRow(this)'>Accept</button> ";
            echo "<button class='btn btn-warning btn-sm send-btn me-1' onclick='processRow(this)' disabled>Send</button> ";
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Clinic Management System / Patient Reports</title>
</head>
<body>
  <!-- Your page content continues here -->
  
  <!-- JavaScript to handle Process button state transitions -->
  <script>
    function processRow(btn) {
      // Get the closest row element.
      var row = btn.closest('tr');
      // Retrieve record id and unique_id from data attributes.
      var recordId = row.getAttribute('data-id');
      var uniqueId = row.getAttribute('data-uniqueid');
      
      // Select the buttons within that row.
      var acceptBtn = row.querySelector('.accept-btn');
      var sendBtn   = row.querySelector('.send-btn');
      
      // Get the Actions cell (assumed to be the 4th column).
      var actionsCell = row.querySelector('td.actions-cell');

      if (btn.classList.contains('accept-btn')) {
          if (actionsCell.innerText.trim() !== "Accepted") {
              // Update database via AJAX to set action to "Accepted".
              fetch('updateAction.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: 'id=' + encodeURIComponent(recordId) + '&newAction=' + encodeURIComponent('Accepted')
              })
              .then(response => response.text())
              .then(data => {
                  if (data.trim() === "success") {
                      actionsCell.innerText = "Accepted";
                      acceptBtn.disabled = true;
                      sendBtn.disabled = false;
                  } else {
                      alert("Update error: " + data);
                  }
              })
              .catch(error => {
                  console.error('Error:', error);
                  alert("An error occurred while updating the record.");
              });
          } else {
              // Already accepted.
              acceptBtn.disabled = true;
              sendBtn.disabled = false;
          }
      } else if (btn.classList.contains('send-btn')) {
          // When Send is clicked, disable both buttons.
          acceptBtn.disabled = true;
          sendBtn.disabled = true;
          // Navigate to the next page (e.g., generatePDF.php).
          window.location.href = 'generatePDF.php?unique_id=' + encodeURIComponent(uniqueId);
      }
    }
  </script>
</body>
</html>
