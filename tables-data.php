<?php
session_start();

if (!isset($_SESSION['accountId'])) {
  header("Location: index.php");
  exit(); 
}

include 'connection.php';
include 'fetchfname.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link href="assets/img/bcp logo.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<header id="header" class="header fixed-top d-flex align-items-center">

<div class="d-flex align-items-center justify-content-between">
  <i class="bi bi-list toggle-sidebar-btn"></i>
</div><!-- End Logo -->

<nav class="header-nav ms-auto">
  <ul class="d-flex align-items-center">

    <li class="nav-item dropdown pe-3">

      <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
        <img src="assets/imG/default profile.jpg" alt="Profile" class="rounded-circle">
        <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($fullname); ?></span>
      </a>

         
      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Administrator</h6>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
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

  <!-- ======= Sidebar ======= -->
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
        <a href="tables-data.php" class="active">
          <i class="bi bi-circle"></i><span>Patient Medical Records</span>
        </a>
      </li>
      <li>  
      <a href="medical-supplies.php">
          <i class="bi bi-circle" ></i><span>Medical Supplies</span>
        </a>
      </li>
      <li>
          <a href="blankanomaly.php">
            <i class="bi bi-circle" ></i><span>A.I Anomaly</span>
          </a>
        </li>
    </ul>
  </li>

      <hr class="sidebar-divider">
    

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Data Tables</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Patient Medical Records</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

        <table class="datatable table">
              <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Student Number</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Gender</th> 
                    <th scope="col">Age</th> 
                    <th scope="col">Year level</th> 
                    <th scope="col">Conditions</th>
                    <th scope="col">Recorded at</th> 

                  </tr>
                </thead>
                <tbody>
                        <?php
                        $servername = "localhost"; 
                        $username = "root"; 
                        $password = ""; 
                        $dbname = "bcp_sms3_cms"; 

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        $stmt = $conn->prepare("SELECT id, fullname, student_number, contact, s_gender, age, year_level, conditions, treatment, DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') AS formatted_created_at FROM bcp_sms3_patients");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["student_number"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["contact"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["s_gender"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["age"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["year_level"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["conditions"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["formatted_created_at"]) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11'>No records found</td></tr>";
                        }
                        $stmt->close();
                        
                        ?>
                      </tbody>


              </table>  
            </div>
          </div>

        </div>
      </div>
    </section>
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