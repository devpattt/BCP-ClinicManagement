<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit(); 
}

include 'connection.php';
include 'fetchfname.php';

// Fetch patient symptoms
$sql_patients = "SELECT id, fullname, symptoms FROM bcp_sms3_patients";
$result_patients = $conn->query($sql_patients);

$patientData = [];
while ($row = $result_patients->fetch_assoc()) {
    $patientData[] = $row;
}

// Fetch illness database (illness name & associated symptoms)
$sql_illness = "SELECT illness_name, symptoms FROM bcp_sms3_illness";
$result_illness = $conn->query($sql_illness);

$illnessData = [];
while ($row = $result_illness->fetch_assoc()) {
    $illnessData[] = $row;
}

$conn->close();

// Return both datasets for AI training
echo json_encode(["patients" => $patientData, "illnesses" => $illnessData]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<style> 
h2 {
    margin-top: 20px !important;
}
.clearfix::after {
    content: "";
    display: block;
    clear: both;
}
#ai-predictions {
    position: relative;
    z-index: 1;
}


</style>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($fullname); ?></span>
          </a>

         
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Administrator</h6>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>lo
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      
    <ul class="sidebar-nav" id="sidebar-nav">
    <div class="logo-container" style="text-align: center; margin-bottom: 10px;">
    <img src="assets/img/bcp logo.png" alt="Logo" style="width: 100px; height: auto;">
    </div>



      <hr class="sidebar-divider">

          <li class="nav-heading">Clinic Management System</li>

           <li class="nav-item">
      <a class="nav-link collapsed" data-bs-target="#system-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-hospital"></i><span>Clinic Management</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="system-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">
      <li>
          <a href="clinic-dashboard.php">
            <i class="bi bi-circle" ></i><span>Report and Analytics</span>
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
            <i class="bi bi-circle" ></i><span>Medical Supplies</span>
          </a>
        </li>
         <li>
          <a href="request.php">
            <i class="bi bi-circle" ></i><span>Request Supply</span>
          </a>
        </li>
          <li>
            <a href="SDforecastingai.php" class="active">
              <i class="bi bi-circle" ></i><span>ForecastingAI</span>
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
    



  </aside><!-- End Sidebar-->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Forecasting Artificial Intelligence</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Forecasting AI</li>
        </ol>
      </nav>
      <h1>AI-Based Illness Prediction</h1>
      <div id="predictionResult">Loading predictions...</div>
  </main>

 
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/js/main.js"></script>

</body>
</html>