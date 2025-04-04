<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit();
}

include '../fetchfname.php';
include '../connection.php';
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
    /* Table container: smaller width & centered */
    .table-container {
      max-width: 90%;
      margin: 0 auto 1rem auto;
      overflow-x: auto;
    }
    /* Slightly reduce overall table font size */
    .table-container table.table-sm {
      font-size: 0.85rem;
    }
    /* Reduce cell padding */
    .table-sm td, .table-sm th {
      padding: 0.3rem;
      vertical-align: middle;
    }
    /* Prevent wrapping in the Manage column */
    #patientTable td:last-child {
      white-space: nowrap;
    }
    /* Make buttons smaller and inline */
    .btn-sm {
      font-size: 0.75rem;
      padding: 0.25rem 0.4rem;
    }
    /* Search container aligned to the right */
    .search-container {
      text-align: right;
      margin-bottom: 1rem;
    }
    /* Diagnosis Link style */
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

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="header-left d-flex align-items-center">
      <i class="bi bi-list toggle-sidebar-btn"></i>
      <!-- Link to records page -->
      <a href="rec_diag.php" class="diagnosis-link">Back</a>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <!-- Profile Dropdown -->
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
        </li><!-- End Profile Nav -->
      </ul>
    </nav>
  </header>
  <!-- ======= End Header ======= -->

  <!-- ======= Sidebar ======= -->
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
  <!-- ======= End Sidebar ======= -->

  <main id="main" class="main mt-5 pt-5">
    <div class="container">
      <div class="pagetitle">
        <h1>Finish Patient Medical Record</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Finish Patient Medical Record</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <!-- Search input above table -->
      <div class="search-container">
        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search records...">
      </div>

      <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title fw-bold mb-3" style="color: #012970;">Patient Reports</h4>
                <div class="table-container">
                  <table class="table table-sm table-hover table-striped table-bordered align-middle text-center" id="patientTable">
                    <thead class="table-primary">
                      <tr>
                        <th>Reference</th>
                        <th>Patient</th>
                        <th>Name</th>
                        <th>Student Number</th>
                        <th>Contact Number</th>
                        <th>Sex</th>
                        <th>Birth Date</th>
                        <th>Year Level</th>
                        <th>Dept Code</th>
                        <th>Diagnostic</th>
                        <th>Recommendation</th>
                        <th>Meds Given</th>
                        <th>Created</th>
                        <th>Action</th>
                        <th>Manage</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        // Retrieve rows from patients table where action equals "Done"
                        $stmt = $conn->prepare("SELECT unique_id, patient,fullname, student_number, contact_number, sex, birthday, year_level, department_code, diagnostic, recommendation, meds, created_at, action FROM bcp_sms3_patients WHERE action = 'Done'");
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                            // Ensure that action is exactly "Done"
                            if (trim($row["action"]) !== "Done") {
                              continue;
                            }
                            $uniqueId        = htmlspecialchars($row["unique_id"]);
                            $patient        = htmlspecialchars($row["patient"]);
                            $fullname        = htmlspecialchars($row["fullname"]);
                            $studentNumber   = htmlspecialchars($row["student_number"]);
                            $contactNumber   = htmlspecialchars($row["contact_number"]);
                            $sex             = htmlspecialchars($row["sex"]);
                            $birthday        = htmlspecialchars($row["birthday"]);
                            $yearLevel       = htmlspecialchars($row["year_level"]);
                            $deptCode        = htmlspecialchars($row["department_code"]);
                            $diagnostic      = trim($row["diagnostic"]) ? htmlspecialchars($row["diagnostic"]) : "N/A";
                            $recommendation  = trim($row["recommendation"]) ? htmlspecialchars($row["recommendation"]) : "N/A";
                            $meds            = trim($row["meds"]) ? htmlspecialchars($row["meds"]) : "N/A";
                            $createdAt       = htmlspecialchars($row["created_at"]);
                            $action          = htmlspecialchars($row["action"]);

                            echo "<tr id='row-{$uniqueId}'>";
                            echo "<td>{$uniqueId}</td>";
                            echo "<td>{$patient}</td>";
                            echo "<td>{$fullname}</td>";
                            echo "<td>{$studentNumber}</td>";
                            echo "<td>{$contactNumber}</td>";
                            echo "<td>{$sex}</td>";
                            echo "<td>{$birthday}</td>";
                            echo "<td>{$yearLevel}</td>";
                            echo "<td>{$deptCode}</td>";
                            echo "<td>{$diagnostic}</td>";
                            echo "<td>{$recommendation}</td>";
                            echo "<td>{$meds}</td>";
                            echo "<td>{$createdAt}</td>";
                            echo "<td>{$action}</td>";
                            // New Manage column with side-by-side Edit and Delete buttons
                            echo "<td>";
                            echo "<button type='button' class='btn btn-primary btn-sm me-1 edit-btn' data-id='{$uniqueId}'>Edit</button>";
                            echo "<button type='button' class='btn btn-danger btn-sm delete-btn' data-id='{$uniqueId}'>Delete</button>";
                            echo "</td>";
                            echo "</tr>";
                          }
                        } else {
                          echo "<tr><td colspan='14' class='text-center'>No records found</td></tr>";
                        }
                        $stmt->close();
                      ?>
                    </tbody>
                  </table>
                </div><!-- End table-container -->
              </div><!-- End card-body -->
            </div><!-- End card -->
          </div><!-- End col -->
        </div><!-- End row -->
      </section>
    </div><!-- End container -->
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Edit Confirmation Modal -->
  <div class="modal fade" id="editConfirmModal" tabindex="-1" aria-labelledby="editConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="editConfirmModalLabel">Confirm Edit</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to edit this data?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" id="editConfirmBtn" class="btn btn-primary btn-sm">Confirm</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Edit Confirmation Modal -->

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this data?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          <button type="button" id="deleteConfirmBtn" class="btn btn-primary btn-sm">Confirm</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Delete Confirmation Modal -->

  <!-- Alert Modal for messages -->
  <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="alertModalLabel">Message</h5>
          <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="alertModalBody">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End Alert Modal -->

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>

  <script>
    // Search filter functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      const rows = document.querySelectorAll('#patientTable tbody tr');
      rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.indexOf(filter) > -1 ? '' : 'none';
      });
    });

    // Global variable to store currently selected row id for editing or deletion
    let currentRecordId = '';

    // Create global modal instances
    const editModal = new bootstrap.Modal(document.getElementById('editConfirmModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));

    // Helper function to display messages in the Alert Modal
    function showAlert(message) {
      document.getElementById('alertModalBody').innerText = message;
      alertModal.show();
    }

    // Bind click event to Edit buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
      button.addEventListener('click', function() {
        currentRecordId = this.getAttribute('data-id');
        editModal.show();
      });
    });

    // Bind click event to Delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
      button.addEventListener('click', function() {
        currentRecordId = this.getAttribute('data-id');
        deleteModal.show();
      });
    });

    // Handle Edit confirmation using the combined endpoint
    document.getElementById('editConfirmBtn').addEventListener('click', function() {
      fetch('manage_patient.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ uid: currentRecordId, type: 'edit' })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          editModal.hide();
          window.location.reload();
        } else {
          showAlert("Error updating record: " + data.error);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        showAlert("An error occurred: " + error.message);
      });
    });

    // Handle Delete confirmation using the combined endpoint
    document.getElementById('deleteConfirmBtn').addEventListener('click', function() {
      fetch('manage_patient.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ uid: currentRecordId, type: 'delete' })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          deleteModal.hide();
          const row = document.getElementById('row-' + currentRecordId);
          if (row) {
            row.parentNode.removeChild(row);
          }
        } else {
          showAlert("Error deleting record: " + data.error);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        showAlert("An error occurred: " + error.message);
      });
    });
  </script>
</body>
</html>
