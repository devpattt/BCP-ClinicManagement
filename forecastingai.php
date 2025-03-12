<?php
session_start();

if (!isset($_SESSION['username'])) {
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
                <a href="clinicdashboard.php">
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
                  <a href="blankanomaly.php" class="active">
                    <i class="bi bi-circle" ></i><span>A.I Anomaly</span>
                  </a>
                </li>
            </ul>
          </li>

    
      <hr class="sidebar-divider">
    


  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Forecasting Artificial Intellegence</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Forecasting AI</li>
        </ol>
      </nav>
    </div>
    <div class="container">
      <h2>AI Health Predictions</h2>
      <div id="predictionResults" class="alert alert-info">Fetching predictions...</div>
    </div>

    <?php
    $url = "http://127.0.0.1:5000/predict";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POST, false); // Use GET request if no payload is required
    $response = curl_exec($ch);

    if ($response === false) {
        die("<div class='alert alert-danger'>cURL Error: " . curl_error($ch) . "</div>");
    }

    curl_close($ch);


    // Debugging: Show API response
    echo "<pre style='display: none;'>API Response: " . htmlspecialchars($response) . "</pre>";

    $data = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        die("<div class='alert alert-danger'>JSON Decode Error: " . json_last_error_msg() . "</div>");
    }

    if (!is_array($data)) {
        die("<div class='alert alert-warning'>Invalid API response format.</div>");
    }

    echo "<ul class='list-group'>";
    foreach ($data as $result) {
        echo "<li class='list-group-item'><strong>" . htmlspecialchars($result['fullname']) . "</strong> might have: <span class='text-danger'>" . htmlspecialchars($result['predicted_disease']) . "</span></li>";
    }
    echo "</ul>";
    ?>

    <?php
    $conn = new mysqli("localhost", "root", "", "bcp_sms3_cms");
    if ($conn->connect_error) {
        die("<div class='alert alert-danger'>Database Connection Failed: " . $conn->connect_error . "</div>");
    }

    $result = $conn->query("SELECT * FROM bcp_sms3_predictions ORDER BY id DESC LIMIT 5");

    echo "<h3>Recent Predictions</h3>";
    while ($row = $result->fetch_assoc()) {
        echo "<p><b>{$row['fullname']}</b> might have: <span style='color:red;'>{$row['predicted_disease']}</span></p>";
    }

    $conn->close();

    ?>


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
  <script>
      function fetchPredictions() {
        let resultsDiv = document.getElementById("predictionResults");
        resultsDiv.innerHTML = "<p>Loading predictions...</p>"; // Show loading state

        fetch('http://localhost:5000/predict', { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            resultsDiv.innerHTML = ""; // Clear previous results

            if (!Array.isArray(data) || data.length === 0) {
                resultsDiv.innerHTML = "<p>No new predictions available.</p>";
                return;
            }

            let output = "<ul class='list-group'>";
            data.forEach(patient => {
                output += `<li class='list-group-item'><strong>${patient.fullname}</strong> might have: <span style="color:red;">${patient.predicted_disease}</span></li>`;
                showNotification(patient.fullname, patient.predicted_disease);
            });
            output += "</ul>";

            resultsDiv.innerHTML = output;
        })
        .catch(error => {
            console.error("Error fetching predictions:", error);
            resultsDiv.innerHTML = "<p class='text-danger'>Error fetching data.</p>";
        });
    }

    setInterval(fetchPredictions, 10000);
    </script>



    <?php
    $conn = new mysqli("localhost", "root", "", "bcp_sms3_cms");
    $result = $conn->query("SELECT * FROM bcp_sms3_predictions ORDER BY id DESC LIMIT 1"); 
    $row = $result->fetch_assoc();
    ?>

    <script>
    function showNotification(name, disease) {
        if (Notification.permission === "granted") {
            new Notification("Health Alert", {
                body: name + " might have: " + disease,
                icon: "alert_icon.png"
            });
        } else {
            Notification.requestPermission();
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        let latestName = "<?php echo $row['fullname']; ?>";
        let latestDisease = "<?php echo $row['predicted_disease']; ?>";
        showNotification(latestName, latestDisease);
    });
    </script>


</body>
</html>