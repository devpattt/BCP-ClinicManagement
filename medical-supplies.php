<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $description = htmlspecialchars(trim($_POST['description']));
    $quantity = htmlspecialchars(trim($_POST['quantity']));
    $unit = htmlspecialchars(trim($_POST['unit']));
    $stmt = $conn->prepare("INSERT INTO bcp_sms3_supplies (name, description, quantity, unit) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $name, $description, $quantity, $unit);

      if ($stmt->execute()) {
          echo json_encode(['status' => 'success', 'message' => 'Record inserted successfully.']);
      } else {
          echo json_encode(['status' => 'error', 'message' => 'Error inserting record: ' . $stmt->error]);
      }

    $stmt->close();
    $conn->close();
    exit; 
}
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
            <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">K. Anderson</span>
          </a><!-- End Profile Image Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>Kevin Anderson</h6>
              <span>Web Designer</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="pages-faq.html">
                <i class="bi bi-question-circle"></i>
                <span>Need Help?</span>
              </a>
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
            <a href="medical-supplies.php" class="active">
              <i class="bi bi-circle"></i><span>Medical Supplies</span>
            </a>
          </li>
          <li>
            <a href="blankanomaly.php">
              <i class="bi bi-circle"></i><span>A.I Anomaly</span>
            </a>
          </li>
        </ul>
      </li>

      <hr class="sidebar-divider">
  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Medical Supplies Management</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="blankindex.php">Home</a></li>
          <li class="breadcrumb-item active">Medical Supplies</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Manage Medical Supplies</h5>

              <form id="supply-form">
                <div class="row mb-3">
                  <label for="name" class="col-sm-2 col-form-label">Supply Name</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="description" class="col-sm-2 col-form-label">Description</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" id="description" required></textarea>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="quantity" class="col-sm-2 col-form-label">Quantity</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" id="quantity" required>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Add Supply</button>
                </div>
              </form>

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
                    <th scope="col">Treatment</th> 
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

                        // Updated SQL query to include both date and time
                        $sql = "SELECT id, fullname, student_number, contact, sgender, age, year_level, conditions, treatment, 
                                DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') AS formatted_created_at 
                                FROM bcp_sms3_patients";

                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["student_number"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["contact"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["sgender"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["age"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["year_level"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["conditions"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["treatment"]) . "</td>";
                                echo "<td>" . htmlspecialchars($row["formatted_created_at"]) . "</td>"; // Use the formatted timestamp here
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11'>No records found</td></tr>";
                        }
                        $conn->close();
                        ?>
                      </tbody>
              </table>  
            </div>
          </div>

        </div>
      </div>
    </section>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <a href="#" class="btn btn-primary btn-floating"><i class="bi bi-plus"></i></a>
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/js/main.js"></script>
  <script>
   
  </script>
</body>

</html>
