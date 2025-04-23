<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit();
}

include '../connection.php';
include '../fetchfname.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Patient Reports</title>
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  
  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  
  <!-- Vendor CSS -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  
  <!-- Main CSS -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    /* Custom styling for the diagnosis link to match UI */
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
    /* Base button styles */
    .btn {
      font-size: 1rem;
    }
    /* Responsive adjustments for buttons */
    @media (max-width: 576px) {
      .btn {
        font-size: 0.8rem;
      }
      .diagnosis-link {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
      }
    }
    .header-left {
      display: flex;
      align-items: center;
    }
    .header-left .diagnosis-link {
      margin-left: 1rem;
    }
  </style>
</head>
<body>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="header-left d-flex align-items-center">
      <i class="bi bi-list toggle-sidebar-btn"></i>
      <!-- Navigation links -->
      <a href="tables-data.php" class="diagnosis-link">Back</a>
      <a href="rec_diag_other.php" class="diagnosis-link">Medical Records With Diagnosis</a>
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
            <li class="dropdown-header">
              <h6>Administrator</h6>
            </li>
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
            <a href="tables-data.php" class="active">
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
    <div class="pagetitle">
      <h1>Patient Medical Records</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Patient Medical Records</li>
        </ol>
      </nav>
    </div>
    <!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <table class="datatable table">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Patient</th>
                    <th scope="col">Unique ID</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Birth Date</th>
                    <th scope="col">Department</th>
                    <th scope="col">Diagnostic</th>
                    <th scope="col">Recommendation</th>
                    <th scope="col">Recorded At</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Retrieve all records from the bcp_sms3_other_patients table including the patient column
                  $stmt = $conn->prepare("
                    SELECT id, unique_id, patient, fullname, contact, birthdate, sex, department, diagnostic, recommendation,
                           DATE_FORMAT(create_at, '%Y-%m-%d %h:%i %p') AS formatted_created_at
                    FROM bcp_sms3_other_patients
                  ");
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      // Skip row if both diagnostic and recommendation have values
                      if (!empty($row['diagnostic']) && !empty($row['recommendation'])) {
                        continue;
                      }
                      
                      // Determine the buttons based on the diagnostic field
                      if (empty($row['diagnostic'])) {
                        // Diagnostic not entered: clickable Diagnose button directing to diag_other.php and disabled Recommendation button
                        $diagnoseBtn = "<a href='diag_other.php?id=" . urlencode($row['id']) . "' class='btn btn-primary w-100'>Diagnose</a>";
                        $recommendationBtn = "<button class='btn btn-primary w-100' disabled>Recommendation</button>";
                      } else {
                        // Diagnostic entered: disabled Diagnose button and clickable Recommendation button directing to recom_other.php
                        $diagnoseBtn = "<button class='btn btn-primary w-100' disabled>Diagnose</button>";
                        $recommendationBtn = "<a href='recom_other.php?id=" . urlencode($row['id']) . "' class='btn btn-primary w-100'>Recommendation</a>";
                      }
                      
                      echo "<tr>";
                      echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['patient']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['unique_id']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['contact']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['sex']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['birthdate']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                      echo "<td>" . $diagnoseBtn . "</td>";
                      echo "<td>" . $recommendationBtn . "</td>";
                      echo "<td>" . htmlspecialchars($row['formatted_created_at']) . "</td>";
                      echo "</tr>";
                    }
                  } else {
                    echo "<tr><td colspan='11'>No records found</td></tr>";
                  }
                  $stmt->close();
                  ?>
                </tbody>
              </table>
            </div><!-- End card-body -->
          </div><!-- End card -->
        </div>
      </div>
    </section>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/js/main.js"></script>
  <script>
    // Initialize Simple-DataTables
    document.addEventListener('DOMContentLoaded', function() {
      const tableEl = document.querySelector('.datatable');
      if (tableEl) {
        new simpleDatatables.DataTable(tableEl);
      }
    });
  </script>
</body>
</html>
