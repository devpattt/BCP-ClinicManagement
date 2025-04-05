<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit();
}

include '../fetchfname.php';
include '../connection.php';

// Fetch the list of medicines from the bcp_sms3_medicalsupplies table
$query = "SELECT item_name FROM bcp_sms3_medicalsupplies";
$result = $conn->query($query);
$itemsOptions = "<option value=''>Select a medicine</option>";
if ($result) {
    while ($row = $result->fetch_assoc()) {
         $item_name = htmlspecialchars($row['item_name']);
         $itemsOptions .= "<option value='$item_name'>$item_name</option>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Patient Reports</title>

  <!-- Favicon and Apple Touch Icon -->
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Main CSS Files -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/forms.css" rel="stylesheet">

  <style>
    /* Adjustments for a smaller table and smaller buttons */
    .table-container {
      max-width: 90%;
      margin: 0 auto;
      font-size: 0.8rem;
    }
    .action-btn-group > .btn,
    .btn-edit, 
    .btn-primary, 
    .btn-secondary, 
    .btn-danger, 
    .btn-success {
      font-size: 0.75rem;
      padding: .25rem .5rem;
    }
    /* --- Modal Positioning CSS --- */
    #editDetailsDialog.modal-centered-dialog {
      position: fixed !important;
      top: 50% !important;
      left: 50% !important;
      transform: translate(-50%, -50%) !important;
      margin: 0 !important;
      z-index: 1050 !important;
    }
    .modal-left1 {
      position: fixed !important;
      top: 50% !important;
      left: 10% !important;
      transform: translateY(-50%) !important;
      margin: 0 !important;
      z-index: 1050 !important;
    }
    .modal-left2 .modal-dialog {
      position: fixed !important;
      top: 50% !important;
      right: 10% !important;
      transform: translateY(-50%) !important;
      margin: 0 !important;
      z-index: 1060 !important;
      max-width: 600px;
      width: 100%;
    }
    .input-close-btn {
      cursor: pointer;
      position: absolute;
      right: 0;
      top: 0;
      font-size: 20px;
      color: #dc3545;
      padding: 0 5px;
    }
    .input-header {
      position: relative;
    }
    .meds-row {
      margin-bottom: 0.75rem;
    }
    .meds-row select, .meds-row input {
      width: 100%;
    }
    .meds-add-btn {
      margin-top: 0.5rem;
    }
    /* Position Clear Confirmation Modal at top center */
    #clearConfirmModal .modal-dialog {
      position: fixed;
      top: 10px;
      left: 50%;
      transform: translateX(-50%);
      margin: 0;
    }
    .diagnosis-link {
  text-decoration: none;
  color: #fff;
  background-color: #012970;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}
.diagnosis-link:hover {
  background-color: #011d5c;
  color: #fff;
}

  </style>
</head>
<body>
  <!-- Header -->
  <header id="header" class="header fixed-top d-flex align-items-center">
  <div class="header-left d-flex align-items-center gap-3">
    <i class="bi bi-list toggle-sidebar-btn"></i>
    <a href="tables-data.php" class="diagnosis-link">Back</a>
    <a href="finishrec.php" class="diagnosis-link">Finish Data's</a>
  </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($fullname); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header"></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="../logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>
  <!-- End Header -->

  <!-- Sidebar -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <div class="logo-container" style="text-align: center; margin-bottom: 10px;">
        <img src="../assets/img/bcp logo.png" alt="Logo" style="width: 100px; height: auto;">
      </div>
      <hr class="sidebar-divider">
      <li class="nav-heading">Clinic Management System</li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#system-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-hospital"></i>
          <span>Clinic Management</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="system-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li><a href="clinic-dashboard.php"><i class="bi bi-circle"></i><span>Report and Analytics</span></a></li>
          <li><a href="forms-elements.php"><i class="bi bi-circle"></i><span>Patient Registration</span></a></li>
          <li><a href="tables-data.php" class="active"><i class="bi bi-circle"></i><span>Patient Medical Records</span></a></li>
          <li><a href="medical-supplies.php"><i class="bi bi-circle"></i><span>Medical Supplies</span></a></li>
          <li><a href="request.php"><i class="bi bi-circle"></i><span>Request Supply</span></a></li>
          <li><a href="SDforecastingai.php"><i class="bi bi-circle"></i><span>ForecastingAI</span></a></li>
          <li><a href="admission.php"><i class="bi bi-circle"></i><span>Student Data</span></a></li>
          <li><a href="integ.php"><i class="bi bi-circle"></i><span>Medical Requests</span></a></li>
        </ul>
      </li>
      <hr class="sidebar-divider">
    </ul>
  </aside>
  <!-- End Sidebar -->

  <main id="main" class="main mt-5 pt-5">
    <div class="container">
      <div class="pagetitle">
        <h1>Patient Medical Reports</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Patient Medical Reports</li>
          </ol>
        </nav>
      </div>

      <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title fw-bold mb-3" style="color: #012970;">Records with Complete Diagnosis and Recommendation</h4>
                <div class="table-responsive table-container">
                  <table class="table table-sm table-hover table-striped table-bordered align-middle text-center">
                    <thead class="table-primary">
                      <tr>
                        <th>Reference</th>
                        <th>Patient</th>
                        <th>Name</th>
                        <th>Student Number</th>
                        <th>Contact Number</th>
                        <th>Sex</th>
                        <th>Birth date</th>
                        <th>Year Lvl</th>
                        <th>Dept Code</th>
                        <th>Diagnostic</th>
                        <th>Recommendation</th>
                        <th>Meds Given</th>
                        <th>Actions</th>
                        <th>Send</th>
                        <th>Created</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $stmt = $conn->prepare("
                          SELECT 
                            p.unique_id, p.patient, p.fullname, p.student_number, p.contact_number, p.sex, p.birthday,
                            p.year_level, p.department_code, p.diagnostic, p.recommendation, p.meds, p.action,
                            DATE_FORMAT(p.created_at, '%Y-%m-%d %h:%i %p') AS formatted_created_at,
                            (SELECT COUNT(*) FROM bcp_sms3_send_integ s WHERE s.unique_id = p.unique_id) AS sentCount
                          FROM bcp_sms3_patients p
                          WHERE p.diagnostic <> '' AND p.recommendation <> ''
                        ");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                            // Hide rows that already have action set to "Done"
                            if (trim($row["action"]) === "Done") {
                              continue;
                            }
                            // If any field is empty, display 'N/A'
                            $diagnosticVal     = trim($row["diagnostic"])     ?: "N/A";
                            $recommendationVal = trim($row["recommendation"]) ?: "N/A";
                            $medsVal           = trim($row["meds"])           ?: "N/A";
                            $uid = htmlspecialchars($row["unique_id"]);
                            
                            echo '<tr id="row-'.$uid.'">';
                            echo '<td>' . $uid . '</td>';
                            echo '<td>' . htmlspecialchars($row["patient"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["fullname"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["student_number"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["contact_number"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["sex"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["birthday"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["year_level"]) . '</td>';
                            echo '<td>' . htmlspecialchars($row["department_code"]) . '</td>';
                            echo '<td id="diagnostic-'.$uid.'">' . htmlspecialchars($diagnosticVal) . '</td>';
                            echo '<td id="recommendation-'.$uid.'">' . htmlspecialchars($recommendationVal) . '</td>';
                            echo '<td id="meds-'.$uid.'">' . htmlspecialchars($medsVal) . '</td>';
                            
                            // Existing Actions column for editing
                            echo '<td>';
                            echo '<button type="button" class="btn btn-primary btn-sm btn-edit" ';
                            echo 'data-uid="'.$uid.'" ';
                            echo 'data-diagnostic="'.htmlspecialchars($row["diagnostic"]).'" ';
                            echo 'data-recommendation="'.htmlspecialchars($row["recommendation"]).'" ';
                            echo 'data-meds="'.htmlspecialchars($row["meds"]).'">Edit</button>';
                            echo '</td>';
                            
                            // Send column
                            $sendUrl = "send1.php?uid=" . urlencode($uid);
                            echo '<td>';
                            if ($row["sentCount"] > 0) {
                              echo '<button type="button" class="btn btn-secondary btn-sm w-100" disabled>Sent</button>';
                            } else {
                              echo '<button type="button" class="btn btn-primary btn-sm w-100 send-btn" data-href="' . $sendUrl . '">Send</button>';
                            }
                            echo '</td>';
                            
                            echo '<td>' . htmlspecialchars($row["formatted_created_at"]) . '</td>';
                            
                            // New Action column with "Done" button (same style as Edit)
                            echo '<td>';
                            echo '<button type="button" class="btn btn-primary btn-sm done-btn" data-uid="'.$uid.'">Done</button>';
                            echo '</td>';
                            
                            echo '</tr>';
                          }
                        } else {
                          echo '<tr><td colspan="15" class="text-center">No records found</td></tr>';
                        }
                        $stmt->close();
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- Modal 1: Edit Details Modal (Centered, enlarged with modal-lg) -->
  <div class="modal fade" id="editDetailsModal" tabindex="-1" aria-labelledby="editDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-centered-dialog" id="editDetailsDialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="editDetailsModalLabel">Current Diagnostic, Recommendation, and Meds Given</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close" onclick="hideBothModals()"></button>
        </div>
        <div class="modal-body">
          <!-- Diagnostic row -->
          <div class="mb-3">
            <strong>Diagnostic: </strong>
            <span id="currentDiagnostic"></span>
            <button type="button" class="btn btn-primary btn-sm edit-field-btn" data-field="diagnostic">Edit</button>
            <button type="button" class="btn btn-danger btn-sm clear-field-btn" data-field="diagnostic">Clear</button>
          </div>
          <!-- Recommendation row -->
          <div class="mb-3">
            <strong>Recommendation: </strong>
            <span id="currentRecommendation"></span>
            <button type="button" class="btn btn-primary btn-sm edit-field-btn" data-field="recommendation">Edit</button>
            <button type="button" class="btn btn-danger btn-sm clear-field-btn" data-field="recommendation">Clear</button>
          </div>
          <!-- Meds Given row -->
          <div class="mb-3">
            <strong>Meds Given: </strong>
            <span id="currentMeds"></span>
            <button type="button" class="btn btn-primary btn-sm edit-field-btn" data-field="meds">Edit</button>
            <button type="button" class="btn btn-danger btn-sm clear-field-btn" data-field="meds">Clear</button>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="hideBothModals()">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal 1 -->

  <!-- Modal 2: Update Field Modal (Right Side) -->
  <div class="modal fade modal-left2" id="updateFieldModal" tabindex="-1" aria-labelledby="updateFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="updateFieldModalLabel">Update</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close" onclick="hideBothModals()"></button>
        </div>
        <div class="modal-body">
          <!-- Container for dynamic input fields -->
          <div id="updateFieldsContainer"></div>
        </div>
        <div class="modal-footer">
          <button type="button" id="submitUpdateFieldsBtn" class="btn btn-primary btn-sm">Submit</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Modal 2 -->

  <!-- Customize PDF Name Modal -->
  <div class="modal fade" id="customNameModal" tabindex="-1" aria-labelledby="customNameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="customNameModalLabel">Customize PDF Name</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="customFileName" class="form-label">Enter Custom PDF File Name:</label>
            <input type="text" class="form-control form-control-sm" id="customFileName" placeholder="report.pdf">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="customSaveBtn" class="btn btn-primary btn-sm">Set</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Customize PDF Name Modal -->

  <!-- Send Success Modal -->
  <div class="modal fade" id="sendModal" tabindex="-1" aria-labelledby="sendModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Changed header background to blue using bg-primary -->
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="sendModalLabel">Success</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Send Successfully, Go to Medical Request to Proceed the Transaction
        </div>
        <div class="modal-footer">
          <button type="button" id="modalOkBtn" class="btn btn-primary btn-sm">OK</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Send Success Modal -->

  <!-- Clear Confirmation Modal (positioned at top center) -->
  <div class="modal fade" id="clearConfirmModal" tabindex="-1" aria-labelledby="clearConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="clearConfirmModalLabel">Clear Data</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to clear the data?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" id="clearNoBtn">No</button>
          <button type="button" class="btn btn-primary btn-sm" id="clearYesBtn">Yes</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Clear Confirmation Modal -->

  <!-- Notification Modal (for messages) -->
  <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p id="notificationMessage"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Notification Modal -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Success Modal for Done Button -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Action updated successfully.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="successModalOkBtn">OK</button>
      </div>
    </div>
  </div>
</div>


  <!-- Vendor JS Files -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
<script>
// Function to show a notification message (if needed)
function showNotification(message, title = "Notification") {
  document.getElementById("notificationModalLabel").textContent = title;
  document.getElementById("notificationMessage").textContent = message;
  new bootstrap.Modal(document.getElementById("notificationModal")).show();
}

// Helper function to get meds updates from the current meds text with zero diff.
function getMedsUpdates() {
  const medsText = document.getElementById("currentMeds").textContent.trim();
  let updates = [];
  if (medsText !== "" && medsText !== "N/A") {
    medsText.split(",").forEach(pair => {
      let parts = pair.split("=");
      if (parts.length === 2) {
        let med = parts[0].trim();
        let qty = parseInt(parts[1].trim(), 10);
        updates.push({ med: med, oldQty: qty, newQty: qty, diff: 0 });
      }
    });
  }
  return updates;
}

document.addEventListener("DOMContentLoaded", function () {
  const customNameModal   = new bootstrap.Modal(document.getElementById("customNameModal"));
  const sendModal         = new bootstrap.Modal(document.getElementById("sendModal")); // for PDF sending
  const editDetailsModal  = new bootstrap.Modal(document.getElementById("editDetailsModal"));
  const updateFieldModal  = new bootstrap.Modal(document.getElementById("updateFieldModal"));
  const clearConfirmModal = new bootstrap.Modal(document.getElementById("clearConfirmModal"));
  const successModal      = new bootstrap.Modal(document.getElementById("successModal"));
  const editDetailsDialog = document.getElementById("editDetailsDialog");
  const updateFieldsContainer = document.getElementById("updateFieldsContainer");

  let currentSendUrl = '';
  let customFileName = '';
  let currentUID = ''; // Store the current patient's unique ID
  let fieldToClear = null; // Will store the field name to clear

  // JS variable for meds options (populated by PHP)
  const medsOptions = `<?php echo $itemsOptions; ?>`;

  // --- Bind Send button click (for PDF) ---
  document.querySelectorAll(".send-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
      e.preventDefault();
      currentSendUrl = this.getAttribute("data-href");
      document.getElementById("customFileName").value = "report.pdf";
      customNameModal.show();
    });
  });

  // --- When a table Edit button is clicked, show Modal 1 and store uid ---
  document.querySelectorAll(".btn-edit").forEach(btn => {
    btn.addEventListener("click", function() {
      currentUID = this.getAttribute("data-uid");
      editDetailsDialog.classList.remove("modal-left1");
      editDetailsDialog.classList.add("modal-centered-dialog");
      // Set fields to "N/A" if empty
      const diagVal = this.getAttribute("data-diagnostic").trim() || "N/A";
      const recVal  = this.getAttribute("data-recommendation").trim() || "N/A";
      const medsVal = this.getAttribute("data-meds").trim() || "N/A";
      document.getElementById("currentDiagnostic").textContent = diagVal;
      document.getElementById("currentRecommendation").textContent = recVal;
      document.getElementById("currentMeds").textContent = medsVal;
      editDetailsModal.show();
    });
  });

  // --- When Edit button in Modal 1 is clicked, add dynamic input in Modal 2 ---
  document.querySelectorAll(".edit-field-btn").forEach(btn => {
    btn.addEventListener("click", function() {
      const field = this.getAttribute("data-field");
      editDetailsDialog.classList.remove("modal-centered-dialog");
      editDetailsDialog.classList.add("modal-left1");

      if (!document.getElementById("field-" + field)) {
        const fieldDiv = document.createElement("div");
        fieldDiv.classList.add("mb-3");
        fieldDiv.id = "field-" + field;

        const headerDiv = document.createElement("div");
        headerDiv.classList.add("input-header");

        const label = document.createElement("label");
        label.classList.add("form-label");
        label.textContent = field.charAt(0).toUpperCase() + field.slice(1) + ":";

        const closeBtn = document.createElement("span");
        closeBtn.classList.add("input-close-btn");
        closeBtn.innerHTML = "&times;";
        closeBtn.addEventListener("click", function() {
          fieldDiv.remove();
        });

        headerDiv.appendChild(label);
        headerDiv.appendChild(closeBtn);
        fieldDiv.appendChild(headerDiv);

        if (field === 'meds') {
          const medsRowsContainer = document.createElement("div");
          medsRowsContainer.id = "medsRowsContainer";
          
          const addMedBtn = document.createElement("button");
          addMedBtn.type = "button";
          addMedBtn.classList.add("btn", "btn-secondary", "meds-add-btn");
          addMedBtn.textContent = "Add Medicine";
          
          function updateAddMedBtnVisibility() {
            const currentRows = medsRowsContainer.querySelectorAll(".meds-row").length;
            addMedBtn.style.display = (currentRows >= 3) ? 'none' : 'block';
          }
          
          function createMedsRow() {
            const row = document.createElement("div");
            row.classList.add("row", "mb-3", "meds-row");
            row.innerHTML = `
              <div class="col-6">
                <select name="meds[]" class="form-select" required>
                  ${medsOptions}
                </select>
              </div>
              <div class="col-4">
                <input type="number" name="quantity[]" class="form-control" placeholder="Qty" required>
              </div>
              <div class="col-2 d-flex align-items-center justify-content-center">
                <button type="button" class="btn btn-link text-danger removeMedRowBtn" title="Remove">&times;</button>
              </div>
            `;
            row.querySelector(".removeMedRowBtn").addEventListener("click", function() {
              medsRowsContainer.removeChild(row);
              updateAddMedBtnVisibility();
            });
            return row;
          }
          
          medsRowsContainer.appendChild(createMedsRow());
          updateAddMedBtnVisibility();
          fieldDiv.appendChild(medsRowsContainer);
          
          addMedBtn.addEventListener("click", function() {
            const currentRows = medsRowsContainer.querySelectorAll(".meds-row").length;
            if (currentRows < 3) {
              medsRowsContainer.appendChild(createMedsRow());
              updateAddMedBtnVisibility();
            }
          });
          fieldDiv.appendChild(addMedBtn);
        } else {
          const input = document.createElement("input");
          input.type = "text";
          input.classList.add("form-control");
          input.placeholder = "Enter new " + field;
          input.name = field;
          fieldDiv.appendChild(input);
        }
        updateFieldsContainer.appendChild(fieldDiv);
      }
      updateFieldModal.show();
    });
  });

  // --- Clear Button in Modal 1: open confirmation modal ---
  document.querySelectorAll(".clear-field-btn").forEach(btn => {
    btn.addEventListener("click", function() {
      fieldToClear = this.getAttribute("data-field");
      clearConfirmModal.show();
    });
  });

  // --- Clear Confirmation Modal: No button ---
  document.getElementById("clearNoBtn").addEventListener("click", function() {
    fieldToClear = null;
    clearConfirmModal.hide();
  });

  // --- Clear Confirmation Modal: Yes button ---
  document.getElementById("clearYesBtn").addEventListener("click", function() {
    if (fieldToClear && currentUID) {
      // For diagnostic and recommendation, simply set to "N/A"
      const currentDiag = document.getElementById("currentDiagnostic").textContent.trim() || "N/A";
      const currentRec  = document.getElementById("currentRecommendation").textContent.trim() || "N/A";
      let medsUpdates = [];
      if (fieldToClear === "meds") {
        // Parse current meds string and build updates with newQty = 0
        const medsText = document.getElementById("currentMeds").textContent.trim();
        if(medsText !== "" && medsText !== "N/A") {
          medsText.split(",").forEach(pair => {
            let parts = pair.split("=");
            if(parts.length === 2) {
              let med = parts[0].trim();
              let qty = parseInt(parts[1].trim(),10);
              // When clearing, newQty becomes 0 so diff = 0 - oldQty.
              medsUpdates.push({ med: med, oldQty: qty, newQty: 0, diff: (0 - qty) });
            }
          });
        }
      } else {
        medsUpdates = getMedsUpdates();
      }
      const newDiag = (fieldToClear === "diagnostic") ? "N/A" : currentDiag;
      const newRec  = (fieldToClear === "recommendation") ? "N/A" : currentRec;
      fetch("update_meds.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          uid: currentUID,
          updates: medsUpdates,
          diagnostic: newDiag,
          recommendation: newRec
        })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          // Upon success, close modals and refresh the page.
          hideBothModals();
          window.location.reload();
        } else {
          showNotification("Error clearing field: " + data.error, "Error");
        }
      })
      .catch(error => {
        console.error("Error:", error);
        showNotification("An error occurred: " + error.message, "Error");
      });
    }
    fieldToClear = null;
    clearConfirmModal.hide();
  });

  // --- Submit button for Modal 2: update diagnostic, recommendation, and meds ---
  document.getElementById("submitUpdateFieldsBtn").addEventListener("click", function() {
    // Validate that all visible inputs are not blank

    // For text inputs (diagnostic and recommendation)
    const textInputs = updateFieldsContainer.querySelectorAll("input[type='text']");
    for (let input of textInputs) {
      if (input.value.trim() === "") {
        showNotification("Please fill all input fields before submitting.", "Warning");
        return;
      }
    }

    // For meds rows, if any exist
    const medsFieldDiv = document.getElementById("field-meds");
    if (medsFieldDiv) {
      const medsRows = medsFieldDiv.querySelectorAll(".meds-row");
      for (let row of medsRows) {
        const selectEl = row.querySelector("select[name='meds[]']");
        const qtyInput = row.querySelector("input[name='quantity[]']");
        if (selectEl.value.trim() === "" || qtyInput.value.trim() === "") {
          showNotification("Please fill all medicine selections and quantities.", "Warning");
          return;
        }
      }
    }

    // Only update the fields that are showing in Modal 2.
    // For diagnostic:
    let newDiagnostic;
    const diagInput = updateFieldsContainer.querySelector("input[name='diagnostic']");
    if (diagInput) {
      newDiagnostic = diagInput.value.trim();
    } else {
      newDiagnostic = document.getElementById("currentDiagnostic").textContent;
    }

    // For recommendation:
    let newRecommendation;
    const recInput = updateFieldsContainer.querySelector("input[name='recommendation']");
    if (recInput) {
      newRecommendation = recInput.value.trim();
    } else {
      newRecommendation = document.getElementById("currentRecommendation").textContent;
    }

    // 1. Parse existing meds from the "currentMeds" span
    const currentMedsText = document.getElementById("currentMeds").textContent;
    let oldMeds = {};
    if (currentMedsText.trim() !== "" && currentMedsText.trim() !== "N/A") {
      currentMedsText.split(",").forEach(pair => {
        let parts = pair.split("=");
        if (parts.length === 2) {
          let med = parts[0].trim();
          let qty = parseInt(parts[1].trim(), 10);
          oldMeds[med] = qty;
        }
      });
    }

    // 2. Collect new meds input from Modal 2 if available
    let newMeds = {};
    const medsFieldDivUpdate = document.getElementById("field-meds");
    if (medsFieldDivUpdate) {
      const medsRowsContainer = medsFieldDivUpdate.querySelector("#medsRowsContainer");
      if (medsRowsContainer) {
        const rows = medsRowsContainer.querySelectorAll(".meds-row");
        rows.forEach(row => {
          const selectEl = row.querySelector("select[name='meds[]']");
          const qtyInput = row.querySelector("input[name='quantity[]']");
          if (selectEl && qtyInput && selectEl.value.trim() !== "" && qtyInput.value.trim() !== "") {
            let med = selectEl.value.trim();
            let qty = parseInt(qtyInput.value.trim(), 10);
            newMeds[med] = qty;
          }
        });
      }
    }

    // Combine with old meds for any that might not have been updated
    for (let med in oldMeds) {
      if (!newMeds.hasOwnProperty(med)) {
        newMeds[med] = oldMeds[med];
      }
    }

    let combinedMedSet = new Set([...Object.keys(oldMeds), ...Object.keys(newMeds)]);
    let updates = [];
    combinedMedSet.forEach(med => {
      let oldQtyVal = oldMeds[med] || 0;
      let newQtyVal = newMeds[med] || 0;
      let diffVal = oldQtyVal - newQtyVal;
      updates.push({
        med: med,
        oldQty: oldQtyVal,
        newQty: newQtyVal,
        diff: diffVal
      });
    });

    fetch("update_meds.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        uid: currentUID,
        updates: updates,
        diagnostic: newDiagnostic,
        recommendation: newRecommendation
      })
    })
    .then(response => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        // Update current display and table cells
        document.getElementById("currentDiagnostic").textContent = newDiagnostic;
        document.getElementById("currentRecommendation").textContent = newRecommendation;
        let medsDisplay = Object.keys(newMeds)
          .filter(med => newMeds[med] > 0)
          .map(med => med + " = " + newMeds[med])
          .join(", ");
        if (!medsDisplay) medsDisplay = "N/A";
        document.getElementById("currentMeds").textContent = medsDisplay;

        if (currentUID) {
          document.getElementById("diagnostic-" + currentUID).textContent = newDiagnostic;
          document.getElementById("recommendation-" + currentUID).textContent = newRecommendation;
          document.getElementById("meds-" + currentUID).textContent = medsDisplay;
        }

        // Instead of showing the PDF send modal, we hide the Edit and Update modals and show the Success Modal.
        hideBothModals();
        successModal.show();
      } else {
        showNotification("Error updating record: " + data.error, "Error");
      }
    })
    .catch(error => {
      console.error("Error:", error);
      showNotification("An error occurred: " + error.message, "Error");
    });
  });

  // --- Customize PDF Name Modal: Set button ---
  document.getElementById("customSaveBtn").addEventListener("click", function() {
    let inputName = document.getElementById("customFileName").value.trim();
    if (inputName === "") {
      showNotification("Please enter a file name.", "Warning");
      return;
    }
    // Append .pdf if it doesn't already end with it.
    if (!inputName.toLowerCase().endsWith(".pdf")) {
      inputName += ".pdf";
    }
    customFileName = encodeURIComponent(inputName);
    customNameModal.hide();
    // Use the appropriate separator depending on whether the URL already has a query string.
    let separator = currentSendUrl.indexOf('?') !== -1 ? '&' : '?';
    window.location.href = currentSendUrl + separator + "filename=" + customFileName;
    // Refresh the page after 3 seconds once the process is done
    setTimeout(() => {
      window.location.reload();
    }, 3000);
  });

  // --- Success Modal OK: close Success Modal and refresh the page ---
  document.getElementById("successModalOkBtn").addEventListener("click", function() {
    successModal.hide();
    window.location.reload();
  });

  // --- Bind "Done" Button Clicks ---
  document.querySelectorAll(".done-btn").forEach(btn => {
    btn.addEventListener("click", function() {
      let uid = this.getAttribute("data-uid");
      // Store the uid globally so it can be used after modal OK is clicked.
      window.currentDoneUID = uid;

      // Get current values from the table cells; default to "N/A" if missing.
      const diagEl = document.getElementById("diagnostic-" + uid);
      const recEl = document.getElementById("recommendation-" + uid);
      const medsEl = document.getElementById("meds-" + uid);
      const diagnostic = diagEl ? diagEl.textContent.trim() : "N/A";
      const recommendation = recEl ? recEl.textContent.trim() : "N/A";
      const medsText = medsEl ? medsEl.textContent.trim() : "N/A";

      let updates = [];
      if (medsText !== "" && medsText !== "N/A") {
        medsText.split(",").forEach(pair => {
          let parts = pair.split("=");
          if (parts.length === 2) {
            let med = parts[0].trim();
            let qty = parseInt(parts[1].trim(), 10);
            updates.push({ med: med, oldQty: qty, newQty: qty, diff: 0 });
          }
        });
      }

      fetch("update_meds.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          uid: uid,
          updates: updates,
          diagnostic: diagnostic,
          recommendation: recommendation,
          action: "Done"
        })
      })
      .then(response => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          successModal.show();
        } else {
          showNotification("Error updating action: " + data.error, "Error");
        }
      })
      .catch(error => {
        console.error("Error:", error);
        showNotification("An error occurred: " + error.message, "Error");
      });
    });
  });
});

function hideBothModals() {
  const editDetails = bootstrap.Modal.getInstance(document.getElementById("editDetailsModal"));
  const updateField = bootstrap.Modal.getInstance(document.getElementById("updateFieldModal"));
  if (editDetails) editDetails.hide();
  if (updateField) updateField.hide();
}
</script>





</body>
</html>
