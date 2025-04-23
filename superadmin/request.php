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

  <!-- Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/forms.css" rel="stylesheet">

  <style>
    /* Slightly reduce table font size on smaller screens */
    @media (max-width: 768px) {
      table.table {
        font-size: 0.9rem;
      }
    }
    /* Responsive flex for action buttons on small screens */
    @media (max-width: 576px) {
      .btn-responsive {
        margin-bottom: 0.5rem;
      }
    }
    .table-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
  </style>
</head>
<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <!-- Profile Dropdown -->
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">
              <?php echo htmlspecialchars($fullname); ?>
            </span>
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
          <li>
            <a href="clinic-dashboard.php">
              <i class="bi bi-circle"></i><span>Report and Analytics</span>
            </a>
          </li>
          <li>
            <a href="forms-elements.php">
              <i class="bi bi-circle"></i><span>Patient Registration</span>
            </a>
          </li>
          <li>
            <a href="tables-data.php">
              <i class="bi bi-circle"></i><span>Patient Medical Records</span>
            </a>
          </li>
          <li>
            <a href="medical-supplies.php">
              <i class="bi bi-circle"></i><span>Medical Supplies</span>
            </a>
          </li>
          <li>
            <a href="request.php" class="active">
              <i class="bi bi-circle"></i><span>Request Supply</span>
            </a>
          </li>
          <li>
            <a href="SDforecastingai.php">
              <i class="bi bi-circle"></i><span>ForecastingAI</span>
            </a>
          </li>
          <li>
            <a href="admission.php">
              <i class="bi bi-circle"></i><span>Student Data</span>
            </a>
          </li>
          <li>
            <a href="integ.php">
              <i class="bi bi-circle"></i><span>Medical Requests</span>
            </a>
          </li>
        </ul>
      </li>
      <hr class="sidebar-divider">
    </ul>
  </aside>
  <!-- ======= End Sidebar ======= -->

  <main id="main" class="main mt-5 pt-5">
    <div class="container">
      <div class="pagetitle">
        <h1>Request Report</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Request Data</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <!-- Table header with a title and the Accepted Request Button -->
                <div class="table-header mb-3">
                  <h4 class="card-title fw-bold" style="color: #012970;">Request Reports from Student Affair</h4>
                  <button type="button" onclick="window.location.href='acc.php'" class="btn btn-primary">Accepted Request</button>
                </div>
                <!-- Updated table for showing pending requests from bcp_sms3_req -->
                <table class="table table-sm table-hover table-striped table-bordered align-middle text-center">
                  <thead class="table-primary">
                    <tr>
                      <th>Student</th>
                      <th>Remarks</th>
                      <th>Request Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      // Retrieve records that have not been processed (both action and rem2 are empty)
                      $stmt = $conn->prepare("SELECT id, students, remarks, request_at FROM bcp_sms3_req WHERE (action IS NULL OR action = '') AND (rem2 IS NULL OR rem2 = '') ORDER BY request_at DESC");
                      $stmt->execute();
                      $result = $stmt->get_result();

                      if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                          $id = $row["id"];
                          $student = htmlspecialchars($row["students"]);
                          $remarks = htmlspecialchars($row["remarks"]);
                          $requestDate = htmlspecialchars($row["request_at"]);

                          echo "<tr id='row_$id'>";
                          echo "<td>" . $student . "</td>";
                          echo "<td>" . $remarks . "</td>";
                          echo "<td>" . $requestDate . "</td>";
                          echo "<td>
                                  <button class='btn btn-primary btn-sm' onclick='acceptRequest($id)'>Accept</button>
                                  <button class='btn btn-primary btn-sm' onclick='openDenyModal($id)'>Deny</button>
                                </td>";
                          echo "</tr>";
                        }
                      } else {
                        echo "<tr><td colspan='4' class='text-center'>No records found</td></tr>";
                      }
                      $stmt->close();
                    ?>
                  </tbody>
                </table>
              </div><!-- End card-body -->
            </div><!-- End card -->
          </div><!-- End col -->
        </div><!-- End row -->
      </section>
    </div><!-- End container -->
  </main>

  <!-- Deny Modal -->
  <div class="modal fade" id="denyModal" tabindex="-1" aria-labelledby="denyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal header with blue background -->
        <div class="modal-header" style="background-color: blue; color: white;">
          <h5 class="modal-title" id="denyModalLabel">Deny Request</h5>
          <button type="button" class="btn btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Modal body with larger, required remarks input -->
        <div class="modal-body">
          <div class="mb-3">
            <label for="denyRemarks" class="form-label">Reason</label>
            <textarea id="denyRemarks" class="form-control" style="height: 200px;" placeholder="Enter your denial reason here..." required></textarea>
          </div>
        </div>
        <!-- Modal footer with Close and Submit buttons -->
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="submitDeny()">Submit</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Message Modal for notifications -->
  <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background-color: blue; color: white;">
          <h5 class="modal-title" id="messageModalLabel">Notification</h5>
          <button type="button" class="btn btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="messageModalBody">
          <!-- Message content will be inserted here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
  <!-- jQuery (for AJAX) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    // Global variable to hold the current request ID for Deny action
    var currentDenyId = null;

    // Function to display messages in the message modal
    function showMessageModal(message) {
      $('#messageModalBody').text(message);
      var modal = new bootstrap.Modal(document.getElementById('messageModal'));
      modal.show();
    }

    // Function to accept a request
    function acceptRequest(id) {
    // 1) grab the student-number from the table row
    let studentNumber = $("#row_" + id + " td:first").text().trim();

    // 2) first, ask check_patient.php if that student exists
    $.ajax({
      url: 'check_patient.php',
      type: 'POST',
      dataType: 'json',
      data: { student_number: studentNumber },
      success: function(checkResp) {
        if (checkResp.exists) {
          // 3a) if exists → call your existing update_request.php to mark Done
          $.ajax({
            url: 'update_request.php',
            type: 'POST',
            dataType: 'json',
            data: { id: id, type: 'accept' },
            success: function(updateResp) {
              // remove row and show success
              $("#row_" + id).remove();
              showMessageModal('Request accepted successfully!');
            },
            error: function() {
              showMessageModal('Error marking request done. Please try again.');
            }
          });
        } else {
          // 3b) if not exists → show “deny” message right away
          showMessageModal("The request has been denied. This student doesn't have a Clinic Medical Record.");
        }
      },
      error: function() {
        showMessageModal('Error checking student record. Please try again.');
      }
    });
  }

    // Function to open the Deny modal
    function openDenyModal(id) {
      currentDenyId = id;
      $("#denyRemarks").val(""); // Clear previous remarks
      var modal = new bootstrap.Modal(document.getElementById('denyModal'));
      modal.show();
    }

    // Function to submit Deny action via AJAX
    function submitDeny() {
      var remarks = $("#denyRemarks").val().trim();
      if (remarks === "") {
        alert("Please fill up the remarks.");
        return;
      }
      $.ajax({
        url: 'update_request.php',
        type: 'POST',
        data: { id: currentDenyId, type: 'deny', rem2: remarks },
        success: function(response) {
          // On success, remove the corresponding table row
          $("#row_" + currentDenyId).remove();
          showMessageModal('Request denied successfully!');
          // Hide the Deny modal
          var modalEl = document.getElementById('denyModal');
          var modal = bootstrap.Modal.getInstance(modalEl);
          modal.hide();
        },
        error: function(xhr, status, error) {
          showMessageModal('An error occurred while denying the request: ' + error);
        }
      });
    }
  </script>
</body>
</html>
