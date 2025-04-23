<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit();
}

include '../connection.php';
include '../fetchfname.php';

// Get record ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  die("No record ID provided.");
}
$id = intval($_GET['id']);

$error = "";
$diagnostic = "";
$success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // grab & validate all three vitals plus diagnostic
  $systolic    = isset($_POST['systolic'])    ? intval($_POST['systolic'])    : null;
  $diastolic   = isset($_POST['diastolic'])   ? intval($_POST['diastolic'])   : null;
  $temperature = isset($_POST['temperature']) ? floatval($_POST['temperature']) : null;
  $diagnostic  = trim($_POST['diagnostic'] ?? '');

  // simple validation
  if ($systolic===null || $diastolic===null || $temperature===null || $diagnostic==='' ) {
      $error = "All fields (systolic, diastolic, temperature, diagnostic) are required.";
  } else {
      // update all four columns in one statement
      $stmt = $conn->prepare(
        "UPDATE bcp_sms3_patients
           SET systolic    = ?,
               diastolic   = ?,
               temperature = ?,
               diagnostic  = ?
         WHERE id = ?"
      );
      $stmt->bind_param(
        "iidsi",
        $systolic,
        $diastolic,
        $temperature,
        $diagnostic,
        $id
      );
      if ($stmt->execute()) {
          $success = "Vitals and diagnostic submitted successfully!";
      } else {
          $error = "Error updating record: " . $stmt->error;
      }
      $stmt->close();
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Diagnostic Entry - Clinic Management System</title>
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
  <!-- Header -->
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
          <i class="bi bi-hospital"></i><span>Clinic Management</span><i class="bi bi-chevron-down ms-auto"></i>
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
  
  <!-- Main Content -->
  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Diagnostic Entry</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="mainpage.php">Home</a></li>
          <li class="breadcrumb-item"><a href="tables-data.php">Patient Medical Records</a></li>
          <li class="breadcrumb-item active">Diagnostic Entry</li>
        </ol>
      </nav>
    </div>
    
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <!-- Card title with matching color -->
              <h5 class="card-title" style="color: #1e3a8a;">Enter Diagnostic Message & Vitals</h5>
              <!-- Optional inline error message -->
              <?php if (!empty($error)) {
                  echo "<div class='alert alert-danger'>" . htmlspecialchars($error) . "</div>"; }
              ?>
              <form method="POST" action="">
                <!-- Blood Pressure Inputs -->
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Blood Pressure</label>
                  <div class="col-sm-5">
                    <input type="number" name="systolic" class="form-control" min="0" max="300" placeholder="Systolic (mmHg)" required>
                    <small class="text-muted">Enter systolic as whole mmHg (e.g., 120)</small>
                  </div>
                  <div class="col-sm-5">
                    <input type="number" name="diastolic" class="form-control" min="0" max="200" placeholder="Diastolic (mmHg)" required>
                    <small class="text-muted">Enter diastolic as whole mmHg (e.g., 80)</small>
                  </div>
                </div>
                <!-- Temperature Input -->
                <div class="row mb-3">
                  <label for="temperature" class="col-sm-2 col-form-label">Temperature (°C)</label>
                  <div class="col-sm-10">
                    <input type="number" name="temperature" step="0.1" class="form-control" placeholder="e.g., 36.7" required>
                    <small class="text-muted">Enter temperature in °C with one decimal (e.g., 36.7)</small>
                  </div>
                </div>
                <!-- Diagnostic Label updated -->
                <div class="form-group mb-3">
                  <label for="diagnostic">Diagnostic</label>
                  <textarea name="diagnostic" id="diagnostic" class="form-control" rows="8" required><?php echo htmlspecialchars($diagnostic); ?></textarea>
                </div>
                <button type="submit" class="btn btn-success mt-3">Submit Diagnostic</button>
                <a href="tables-data.php" class="btn btn-secondary mt-3">Back to Records</a>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  
  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="successModalLabel">Success</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php echo htmlspecialchars($success); ?>
        </div>
        <div class="modal-footer">
          <a href="tables-data.php" class="btn btn-success">OK</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Error Modal -->
  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php echo htmlspecialchars($error); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/js/main.js"></script>
  
  <!-- Trigger modals -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      <?php if (!empty($success)): ?>
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
      <?php elseif (!empty($error) && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
      <?php endif; ?>
    });
  </script>
  <script>
  document.addEventListener('DOMContentLoaded', function(){
    <?php if (!empty($success)): ?>
      new bootstrap.Modal(document.getElementById('successModal')).show();
    <?php elseif (!empty($error)): ?>
      // you already echo $error above in an alert; if you want a modal:
      new bootstrap.Modal(document.getElementById('errorModal')).show();
    <?php endif; ?>
  });
</script>

</body>
</html>
