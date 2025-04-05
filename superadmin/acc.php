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
    /* Add some margin to push the content down */
    main.main {
      margin-top: 70px; /* Adjust as needed to move content further down from the header */
    }
    /* Slightly reduce table font size on smaller screens */
    @media (max-width: 768px) {
      table.table {
        font-size: 0.9rem;
      }
    }
    .table-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }
    /* Simple styling for the search input */
    #tableSearch {
      width: 300px;
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

  <main id="main" class="main">
    <div class="container">
      <div class="pagetitle">
        <h1>Accepted Request</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <!-- Add margin-top to push the card further down -->
            <div class="card mt-4">
              <div class="card-body">
                <!-- Table header: search bar on left, Back button on right -->
                <div class="table-header mt-3 mb-3">
                  <input type="text" id="tableSearch" placeholder="Search..." class="form-control me-2">
                  <button type="button" onclick="window.location.href='request.php'" class="btn btn-primary">Back</button>
                </div>
                <table class="table table-sm table-hover table-striped table-bordered align-middle text-center" id="dataTable">
                  <thead class="table-primary">
                    <tr>
                      <th>Student</th>
                      <th>Remarks</th>
                      <th>Request Date</th>
                      <th>Process</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      // Retrieve only records that have a non-empty "action" value
                      $stmt = $conn->prepare("SELECT id, students, remarks, request_at, action FROM bcp_sms3_req WHERE action IS NOT NULL AND action <> '' ORDER BY request_at DESC");
                      $stmt->execute();
                      $result = $stmt->get_result();

                      if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                          $id = $row["id"];
                          $student = htmlspecialchars($row["students"]);
                          $remarks = htmlspecialchars($row["remarks"]);
                          $requestDate = htmlspecialchars($row["request_at"]);
                          $process = htmlspecialchars($row["action"]);

                          echo "<tr id='row_$id'>";
                          echo "<td>" . $student . "</td>";
                          echo "<td>" . $remarks . "</td>";
                          echo "<td>" . $requestDate . "</td>";
                          echo "<td>" . $process . "</td>";
                          echo "<td>
                                  <button class='btn btn-primary btn-sm' onclick='openEditModal($id)'>Edit</button>
                                </td>";
                          echo "</tr>";
                        }
                      } else {
                        echo "<tr><td colspan='5' class='text-center'>No records found</td></tr>";
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

  <!-- Edit Confirmation Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal header -->
        <div class="modal-header" style="background-color: blue; color: white;">
          <h5 class="modal-title" id="editModalLabel">Edit Row</h5>
          <button type="button" class="btn btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Modal body with confirmation message -->
        <div class="modal-body">
          <p>Are you sure you want to edit this row?</p>
        </div>
        <!-- Modal footer with Close and Confirm buttons -->
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="confirmEdit()">Confirm</button>
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
  <!-- jQuery (for AJAX and search filtering) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    var currentEditId = null;

    // Show message in the modal
    function showMessageModal(message) {
      $('#messageModalBody').text(message);
      var modal = new bootstrap.Modal(document.getElementById('messageModal'));
      modal.show();
    }

    // Open the Edit modal
    function openEditModal(id) {
      currentEditId = id;
      var modal = new bootstrap.Modal(document.getElementById('editModal'));
      modal.show();
    }

    // Confirm edit (clear 'action' in DB, remove row)
    function confirmEdit() {
      $.ajax({
        url: 'up.php',
        type: 'POST',
        data: { id: currentEditId, type: 'edit' },
        success: function(response) {
          // On success, remove the corresponding table row
          $("#row_" + currentEditId).remove();
          showMessageModal('Row updated successfully!');
          // Hide the Edit modal
          var modalEl = document.getElementById('editModal');
          var modal = bootstrap.Modal.getInstance(modalEl);
          modal.hide();
        },
        error: function(xhr, status, error) {
          showMessageModal('An error occurred while editing the row: ' + error);
        }
      });
    }

    // Search filter
    $(document).ready(function(){
      $("#tableSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#dataTable tbody tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
      });
    });
  </script>
</body>
</html>
