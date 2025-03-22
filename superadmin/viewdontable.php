<?php
include '../connection.php';

// Updated query with aliases to ensure keys exist.
$query = "SELECT 
            id, 
            unique_id AS reference, 
            request, 
            reason, 
            request_at AS request_date, 
            action AS actions, 
            process, 
            '' AS download 
          FROM bcp_sms3_reqmedreqcord 
          ORDER BY request_at DESC";
$result = $conn->query($query);
?>
    <?php
    if ($result) {
        $current_file = basename($_SERVER['PHP_SELF']);
        while ($row = $result->fetch_assoc()) {
            // Normalize values for filtering.
            $action  = strtolower(trim($row['actions']));
            $process = strtolower(trim($row['process']));
            
            // If current page is viewdone.php, only show rows with Accepted & Done.
            if ($current_file === 'viewdone.php') {
                if (!($action === "accepted" && $process === "done")) {
                    continue;
                }
            } else {
                // Otherwise (e.g. for integ.php), skip rows that are completed.
                if ($action === "accepted" && $process === "done") {
                    continue;
                }
            }
            
            echo "<tr data-id='" . htmlspecialchars($row['id']) . "' data-uniqueid='" . htmlspecialchars($row['reference']) . "'>";
            echo "<td>" . htmlspecialchars($row['request']) . "</td>";
            echo "<td>" . htmlspecialchars($row['reference']) . "</td>";
            echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
            echo "<td>" . htmlspecialchars($row['request_date']) . "</td>";
            echo "<td class='actions-cell'>" . htmlspecialchars($row['actions']) . "</td>";
            echo "<td class='process-cell'>" . htmlspecialchars($row['process']) . "</td>";
            // Process Buttons Column: replace Accept/Send with a single Edit button.
            echo "<td><button class='btn btn-primary btn-sm edit-btn' data-id='" . htmlspecialchars($row['id']) . "'>Edit</button></td>";
            // Download Column.
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
  </tbody>
</table>

<!-- Edit Confirmation Modal -->
<div class="modal fade" id="editConfirmModal" tabindex="-1" aria-labelledby="editConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editConfirmModalLabel">Confirm Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to edit this file?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
        <button type="button" class="btn btn-primary" id="confirmEditBtn">Yes</button>
      </div>
    </div>
  </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Message will be injected here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript (requires Bootstrap 5 JS) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    let selectedRecordId = null;
    
    // Function to display a message in the Message Modal.
    function showMessageModal(message) {
        var msgModalEl = document.getElementById("messageModal");
        msgModalEl.querySelector(".modal-body").innerText = message;
        var msgModal = new bootstrap.Modal(msgModalEl);
        msgModal.show();
    }
    
    // Attach click event to all Edit buttons.
    document.querySelectorAll('.edit-btn').forEach(function(btn) {
         btn.addEventListener('click', function(e) {
             e.preventDefault();
             selectedRecordId = this.getAttribute('data-id');
             // Open the Edit Confirmation Modal.
             var myModal = new bootstrap.Modal(document.getElementById('editConfirmModal'));
             myModal.show();
         });
    });
    
    // When Yes is clicked in the Edit Confirmation Modal, update the record.
    document.getElementById('confirmEditBtn').addEventListener('click', function() {
         if (selectedRecordId) {
             // AJAX request to update Action and Process to "Pending".
             fetch('updateAction.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                  body: 'id=' + encodeURIComponent(selectedRecordId) + '&newAction=Pending&newProcess=Pending'
             })
             .then(response => response.text())
             .then(data => {
                 if(data.trim() === "success") {
                     // Update the UI for the row.
                     var row = document.querySelector('tr[data-id="' + selectedRecordId + '"]');
                     if (row) {
                         var actionsCell = row.querySelector('.actions-cell');
                         var processCell = row.querySelector('.process-cell');
                         if (actionsCell) { actionsCell.innerText = "Pending"; }
                         if (processCell) { processCell.innerText = "Pending"; }
                     }
                     showMessageModal("Record updated to Pending.");
                 } else {
                     showMessageModal("Error updating record: " + data);
                 }
             })
             .catch(err => {
                 console.error(err);
                 showMessageModal("An error occurred.");
             });
             // Close the Edit Confirmation Modal.
             var myModalEl = document.getElementById('editConfirmModal');
             var modalInstance = bootstrap.Modal.getInstance(myModalEl);
             modalInstance.hide();
         }
    });
});
</script>
