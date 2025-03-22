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
        echo "<td>" . htmlspecialchars($row['unique_id']) . "</td>";  // Reference column
        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
        echo "<td>" . htmlspecialchars($row['request_at']) . "</td>";
        echo "<td class='actions-cell'>" . htmlspecialchars($row['action']) . "</td>";
        echo "<td>" . htmlspecialchars($row['process']) . "</td>";
        // Process Buttons Column wrapped in a flex container
        echo "<td class='process-buttons'>";
        if ($action === "accepted") {
            if ($process === "done") {
                // If action is Accepted and process is Done: disable both buttons.
                echo "<button class='btn btn-success btn-sm accept-btn' disabled>Accept</button>";
                echo "<button class='btn btn-warning btn-sm send-btn' disabled>Send</button>";
            } else {
                // If Accepted but process is Pending: disable Accept; enable Send.
                echo "<button class='btn btn-success btn-sm accept-btn' disabled>Accept</button>";
                echo "<button class='btn btn-warning btn-sm send-btn' onclick='processRow(this)'>Send</button>";
            }
        } else {
            // For pending actions: only Accept is enabled.
            echo "<button class='btn btn-success btn-sm accept-btn' onclick='processRow(this)'>Accept</button>";
            echo "<button class='btn btn-warning btn-sm send-btn' onclick='processRow(this)' disabled>Send</button>";
        }
        echo "</td>";
        // Download Column:
        echo "<td>";
        echo "<a href='downloadtxt.php?unique_id=" . urlencode($row['unique_id']) . "' class='btn btn-link btn-sm'><i class='bi bi-download'></i></a>";
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
  <style>
    /* Flex container to keep Accept and Send buttons side by side */
    .process-buttons {
      display: flex;
      flex-wrap: nowrap;
      gap: 10px; /* Adjust the gap as needed */
    }
    .process-buttons .btn {
      min-width: 80px;  /* Adjust the minimum width as needed */
      white-space: nowrap;
    }
  </style>
</head>
<body>
  <!-- Your remaining page content -->

  <!-- JavaScript to handle Process button state transitions -->
  <script>
    // Custom function to show a modal alert.
    function showAlert(message) {
      // Set the alert modal's content.
      document.getElementById("alertModalBody").innerText = message;
      // Show the modal.
      var alertModal = new bootstrap.Modal(document.getElementById("alertModal"));
      alertModal.show();
    }
    
    function processRow(btn) {
      // Get the closest row element.
      var row = btn.closest('tr');
      // Retrieve record id and unique_id from data attributes.
      var recordId = row.getAttribute('data-id');
      var uniqueId = row.getAttribute('data-uniqueid');
      
      // Select the buttons within that row.
      var acceptBtn = row.querySelector('.accept-btn');
      var sendBtn   = row.querySelector('.send-btn');
      
      // Get the Actions cell (assumed to be the 5th column).
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
                      showAlert("Update error: " + data);
                  }
              })
              .catch(error => {
                  console.error('Error:', error);
                  showAlert("An error occurred while updating the record.");
              });
          } else {
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
  
  <!-- Alert Modal (Bootstrap) -->
  <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="alertModalLabel">Notice</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="alertModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
  
</body>
</html>
