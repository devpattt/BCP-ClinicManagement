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
  <title>Clinic Management System</title>
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
  <link href="../assets/css/style.css" rel="stylesheet">
  <script type="javascript" src="../assets/js/darkmode.js" defer></script>
</head>
<body>

  <!-- ======= Header ======= -->
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
              <ul id="system-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">
              <li>
          <a href="mainpage.php">
            <i class="bi bi-circle" ></i><span>Home</span>
          </a>
        </li>
        <li>
          <a href="request.php">
            <i class="bi bi-circle" ></i><span>Request Supply</span>
          </a>
        </li>
      <li>
              <li>
                  <a href="clinic-dashboard.php"  class="active">
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
                    <a href="blankanomaly.php">
                      <i class="bi bi-circle" ></i><span>A.I Anomaly</span>
                    </a>
                  </li>
                  <li></li>
              </ul>
            </li>
          <hr class="sidebar-divider">
      </aside><!-- End Sidebar-->

    <main id="main" class="main">
      <div class="pagetitle">
        <h1>Dashboard</h1>
        <nav>
          <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="mainpage.php">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </nav>
      </div>

    <section class="section dashboard">
    <div class="row">
      <div class="col-lg-24">
        <div class="row d-flex justify-content-between">

          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card sales-card">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>
                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Patient <span>| Today</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6 id="today-count"></h6>
                    <span id="today-increase" class="text-success small pt-1 fw-bold">100%</span>
                    <span id="today-increase" class="text-muted small pt-2 ps-1">increase</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card revenue-card">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>
                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Patient <span>| This Month</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6 id="month-count"></h6>
                    <span id="month-increase" class="text-success small pt-1 fw-bold">100%</span>
                    <span id="month-increase" class="text-muted small pt-2 ps-1">increase</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 mb-4">
            <div class="card info-card customers-card">
              <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                  <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                  </li>
                  <li><a class="dropdown-item" href="#">Today</a></li>
                  <li><a class="dropdown-item" href="#">This Month</a></li>
                  <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
              </div>

              <div class="card-body">
                <h5 class="card-title">Patient <span>| This Year</span></h5>
                <div class="d-flex align-items-center">
                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-people"></i>
                  </div>
                  <div class="ps-3">
                    <h6 id="year-count"></h6>
                    <span id="year-increase" class="text-success small pt-1 fw-bold">100%</span>
                    <span id="year-increase" class="text-muted small pt-2 ps-1">increase</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
</section>
  <div class="col-12">
    <div class="card">
        <div class="filter">
            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                    <h6>Filter</h6>
                </li>
                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
            </ul>
        </div>
        <div class="card-body">
            <h5 class="card-titles"><span>Monthly Patient Metrics</span></h5>
            <div id="reportsChart"></div>
            <script>
                document.addEventListener("DOMContentLoaded", () => {
                    fetch('../fetch_visits.php') 
                        .then(response => response.json())
                        .then(data => {
                            const categories = data.map(entry => entry.month); 
                            const counts = data.map(entry => entry.count); 

                            new ApexCharts(document.querySelector("#reportsChart"), {
                                series: [{
                                    name: 'Student Visits',
                                    data: counts
                                }],
                                chart: {
                                    height: 350,
                                    type: 'bar', 
                                    toolbar: {
                                        show: false
                                    },
                                },
                                colors: ['#1e3a8a'], 
                                dataLabels: {
                                    enabled: true
                                },
                                xaxis: {
                                    categories: categories, 
                                    title: {
                                        text: 'Months' 
                                    },
                                },
                                yaxis: {
                                    title: {
                                        text: 'Number of Visits' 
                                    },
                                },
                                tooltip: {
                                    shared: true,
                                    intersect: false,
                                },
                            }).render();
                        })
                        .catch(error => console.error('Error fetching data:', error));
                });
            </script>
        </div>
    </div>
</div>
</section>
  </main>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
  <script src="../assets/js/main.js"></script>

  <script> 
        document.addEventListener('DOMContentLoaded', function() {
            function fetchData(filter) {
                fetch(`../hdfetch.php?filter=${filter}`)
                    .then(response => response.json())
                    .then(data => {
                        const count = data.count;
                        const percentage = data.percentage;
                        const increaseClass = data.increase;
                        
                        const elementId = filter === 'today' ? 'today' :
                                          filter === 'month' ? 'month' : 'year';
                        
                        document.getElementById(`${elementId}-count`).textContent = count;
                        const increaseElement = document.getElementById(`${elementId}-increase`);
                        increaseElement.textContent = `${percentage}%`;
                        increaseElement.className = `${increaseClass} small pt-1 fw-bold`;
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }
            fetchData('today');
            fetchData('month');
            fetchData('year');

            document.querySelectorAll('.dropdown-menu .dropdown-menu-end .dropdown-menu-arrow').forEach(item => {
                item.addEventListener('click', function(event) {
                    event.preventDefault();
                    const filter = this.getAttribute('data-filter');
                    fetchData(filter);
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            function updateProfileName() {
                fetch('../fetch_uname.php') 
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const nameSpan = document.getElementById('fname');
                        if (data.fname) {
                            nameSpan.textContent = data.fname; 
                        } else if (data.error) {
                            console.error('Error from PHP script:', data.error);
                        } else {
                            console.error('Unexpected response format');
                        }
                    })
                    .catch(error => console.error('Error fetching name:', error));
            }

            updateProfileName(); 
        });
  </script>

</body>

</html>