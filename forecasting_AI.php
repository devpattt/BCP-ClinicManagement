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
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
            <li><hr class="dropdown-divider"></li>
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
          <li><a href="clinicdashboard.php"><i class="bi bi-circle"></i><span>Report and Analytics</span></a></li>
          <li><a href="forms-elements.php"><i class="bi bi-circle"></i><span>Patient Registration</span></a></li>
          <li><a href="tables-data.php"><i class="bi bi-circle"></i><span>Patient Medical Records</span></a></li>
          <li><a href="medical-supplies.php"><i class="bi bi-circle"></i><span>Medical Supplies</span></a></li>
          <li><a href="blankanomaly.php" class="active"><i class="bi bi-circle"></i><span>A.I Anomaly</span></a></li>
        </ul>
      </li>
      <hr class="sidebar-divider">
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Forecasting Artificial Intelligence</h1>
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
    curl_setopt($ch, CURLOPT_POST, true);
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

  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
  function fetchPredictions() {
      fetch('http://localhost:5000/predict', { method: 'POST' })
      .then(response => response.json())
      .then(data => {
          let resultsDiv = document.getElementById("predictionResults");
          resultsDiv.innerHTML = ""; // Clear previous results

          if (data.length === 0) {
              resultsDiv.innerHTML = "<p>No new predictions available.</p>";
              return;
          }

          let output = "<ul class='list-group'>";
          data.forEach(patient => {
              output += `<li class='list-group-item'><strong>${patient.fullname}</strong> might have: <span style="color:red;">${patient.predicted_disease}</span></li>`;
          });
          output += "</ul>";

          resultsDiv.innerHTML = output;

          // Optional: Trigger an alert for new predictions
          alert("New health predictions received!");
      })
      .catch(error => {
          console.error("Error fetching predictions:", error);
          document.getElementById("predictionResults").innerHTML = "<p class='text-danger'>Error fetching data.</p>";
      });
  }

  // Fetch predictions every 10 seconds
  setInterval(fetchPredictions, 10000);
  </script>

</body>
</html>
