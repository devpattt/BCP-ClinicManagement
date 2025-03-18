<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit(); 
}

include '../fetchfname.php';
include '../connection.php';
include 'updateAction.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Done Transactions</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="../https://fonts.gstatic.com" rel="preconnect">
  <link href="../https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/forms.css" rel="stylesheet">
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="../assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($fullname); ?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
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
          <i class="bi bi-hospital"></i><span>Clinic Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="system-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="clinic-dashboard.php">
              <i class="bi bi-circle"></i><span>Report and Analytics</span>
            </a>
          </li>
          <li>
            <a href="request.php">
              <i class="bi bi-circle"></i><span>Request Supply</span>
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
            <a href="blankanomaly.php">
              <i class="bi bi-circle"></i><span>A.I Anomaly</span>
            </a>
          </li>
          <li>
            <a href="integ.php" class="active">
              <i class="bi bi-circle"></i><span>Patient Reports</span>
            </a>
          </li>
          <li></li>
        </ul>
      </li>
      <hr class="sidebar-divider">
    </ul>
  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Done Transactions</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Done Transactions</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Completed Transaction Details</h5>
              <!-- Back Button -->
              <div class="mb-3">
                <a href="integ.php" class="btn btn-secondary">Back</a>
              </div>
              <!-- Search Input -->
              <div class="mb-3">
                <input type="text" class="form-control" id="searchInput" placeholder="Search by Unique ID or Date...">
              </div>
              <!-- Table (the rows are rendered from viewreqforinteg.php) -->
              <div class="container mt-3">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Request</th>
                      <th>Reason</th>
                      <th>Request Date</th>
                      <th>Actions</th>
                      <th>Process</th>
                      <th>Process Buttons</th>
                      <th>Download</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php include 'viewreqforinteg.php'; ?>
                  </tbody>
                </table>
              </div>
              <!-- JavaScript to filter table rows by unique_id and date -->
              <script>
                document.getElementById('searchInput').addEventListener('keyup', function() {
                  var filter = this.value.toUpperCase();
                  var rows = document.querySelectorAll("table tbody tr");
                  rows.forEach(function(row) {
                    // Get unique_id from the data attribute.
                    var uniqueId = row.getAttribute('data-uniqueid') || "";
                    // Get the date from the 3rd cell (index 2).
                    var dateCell = row.cells[2] ? row.cells[2].innerText : "";
                    // Combine both values.
                    var combinedText = (uniqueId + " " + dateCell).toUpperCase();
                    row.style.display = combinedText.indexOf(filter) > -1 ? "" : "none";
                  });
                });
              </script>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
</body>
</html>
