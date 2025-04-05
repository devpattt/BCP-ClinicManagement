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
  <title>Clinic Management System / Processed Files</title>

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
            <a href="request.php">
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
            <a href="integ.php" class="active">
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
        <h1>Processed Files</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          </ol>
        </nav>
      </div><!-- End Page Title -->

      <section class="section">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <!-- Table header with a title and a Back button -->
                <div class="table-header mb-3">
                  <h4 class="card-title fw-bold" style="color:rgb(255, 255, 255);">..</h4>
                  <button type="button" onclick="window.location.href='integ.php'" class="btn btn-primary">Back</button>
                </div>
                <!-- Table: show all columns except id, and only rows with a value in status -->
                <table class="table table-sm table-hover table-striped table-bordered align-middle text-center">
                  <thead class="table-primary">
                    <tr>
                      <th>Reference #</th>
                      <th>PDF File</th>
                      <th>Status</th>
                      <th>Date</th>
                      <th>Actions</th>
                      <th>Download</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      // Retrieve records from bcp_sms3_send_integ table where status is not empty
                      $stmt = $conn->prepare("SELECT unique_id, request, status, date FROM bcp_sms3_send_integ WHERE status <> ''");
                      $stmt->execute();
                      $result = $stmt->get_result();

                      if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                          $uniqueId = htmlspecialchars($row["unique_id"]);
                          $pdfFile  = htmlspecialchars($row["request"]);
                          $status   = htmlspecialchars($row["status"]);
                          $date     = htmlspecialchars($row["date"]);

                          echo "<tr>";
                          echo "<td>" . $uniqueId . "</td>";
                          echo "<td>" . $pdfFile . "</td>";
                          echo "<td>" . $status . "</td>";
                          echo "<td>" . $date . "</td>";
                          // Actions column for Edit button only
                          echo "<td>";
                          echo "<button type='button' class='btn btn-primary btn-sm btn-responsive' onclick='openEditModal(\"$uniqueId\")'>Edit</button>";
                          echo "</td>";
                          // New column for Download icon
                          echo "<td>";
                          echo "<a href='../" . $pdfFile . "' download title='Download PDF'><i class='bi bi-download' style='font-size: 1.5rem; color: #012970;'></i></a>";
                          echo "</td>";
                          echo "</tr>";
                        }
                      } else {
                        echo "<tr><td colspan='6' class='text-center'>No records found</td></tr>";
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

  <!-- Modal for confirming edit (which will delete the row) -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal header -->
        <div class="modal-header" style="background-color: blue; color: white;">
          <h5 class="modal-title" id="editModalLabel">Confirm Edit</h5>
          <button type="button" class="btn btn-primary btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
          Are you sure you want to edit this file?
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
        <div class="modal-header">
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
    // Global variable to hold the current row's unique_id for editing
    var currentUniqueId = "";

    // Function to display messages in a modal
    function showMessageModal(message) {
      $('#messageModalBody').text(message);
      var modal = new bootstrap.Modal(document.getElementById('messageModal'));
      modal.show();
    }

    // Placeholder function for viewing the PDF (if needed)
    function viewPdf(fileUrl) {
      window.open('../' + fileUrl, '_blank');
    }

    // Open the edit modal and store the unique_id
    function openEditModal(uniqueId) {
      currentUniqueId = uniqueId;
      var modal = new bootstrap.Modal(document.getElementById('editModal'));
      modal.show();
    }

    // Function to call when "Confirm" is clicked in the edit modal
    function confirmEdit() {
      $.ajax({
        url: 'delete_file.php',
        type: 'POST',
        data: { unique_id: currentUniqueId },
        success: function(response) {
          showMessageModal('File deleted successfully!');
          // Hide the edit modal
          var modalEl = document.getElementById('editModal');
          var modal = bootstrap.Modal.getInstance(modalEl);
          modal.hide();
          // Reload the page to update the table
          location.reload();
        },
        error: function(xhr, status, error) {
          showMessageModal('An error occurred: ' + error);
        }
      });
    }
  </script>
</body>
</html>
