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
<style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        table {
            margin-top: 20px;
        }
    </style>
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
            <a href="SDforecastingai.php">
              <i class="bi bi-circle" ></i><span>ForecastingAI</span>
            </a>
          </li>
          <li>
          <a href="admission.php" class="active">
            <i class="bi bi-circle"></i><span>Student Data</span>
          </a>
        </li>
        <li>
          <a href="integ.php">
            <i class="bi bi-circle"></i><span>Medical Requests</span>
          </a>
        </li>
          <li></li>
      </ul>
    </li>
          <hr class="sidebar-divider">
      </aside><!-- End Sidebar-->
      
    <main id="main" class="main">
         <div class="container">
        <h1 class="text-center mb-4">Student Records</h1>
        <button id="save-btn" class="btn btn-primary mb-3">Save</button>
        <div id="students-container">
            <p class="text-center">Loading student data...</p>
        </div>
        <p id="status-message" class="text-center mt-3"></p>
    </div>

           <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="confirmModalLabel">Confirm Action</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="fs-5">Are you sure you want to save student records to the database?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button id="confirm-save-btn" class="btn btn-success">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    <script>
        let studentsData = [];

        async function fetchStudents() {
            const container = document.getElementById('students-container');

            try {
                const response = await fetch('https://admission.bcpsms3.com/api_students.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const students = await response.json();

                if (!Array.isArray(students) || students.length === 0) {
                    container.innerHTML = '<p class="text-danger text-center">No students found.</p>';
                    return;
                }

                studentsData = students;
                displayStudents(students);
            } catch (error) {
                container.innerHTML = `<p class="text-danger text-center">Error fetching students data: ${error.message}</p>`;
            }
        }

        function displayStudents(students) {
            const container = document.getElementById('students-container');
            container.innerHTML = '';

            const table = document.createElement('table');
            table.className = 'table table-striped table-bordered text-center';

            const thead = document.createElement('thead');
            thead.innerHTML = `
                <tr class="table-dark">
                    <th>Student Number</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Contact Number</th>
                    <th>Year Level</th>
                    <th>Sex</th>
                    <th>Department Code</th>
                </tr>
            `;
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            students.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.student_number}</td>
                    <td>${student.first_name}</td>
                    <td>${student.middle_name || 'N/A'}</td>
                    <td>${student.last_name}</td>
                    <td>${student.contact_number}</td>
                    <td>${student.year_level}</td>
                    <td>${student.sex}</td>
                    <td>${student.department_code}</td>
                `;
                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            container.appendChild(table);
        }

        async function saveStudents() {
            const statusMessage = document.getElementById('status-message');

            if (studentsData.length === 0) {
                statusMessage.innerHTML = '<span class="text-danger">No student data available to save.</span>';
                return;
            }

            try {
                const response = await fetch('insert_students.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ students: studentsData }),
                });

                const result = await response.json();
                if (result.success) {
                    statusMessage.innerHTML = '<span class="text-success">Students successfully stored in the database!</span>';
                } else {
                    throw new Error(result.message || 'Failed to save students.');
                }
            } catch (error) {
                statusMessage.innerHTML = `<span class="text-danger">Error: ${error.message}</span>`;
            }
        }

        window.onload = fetchStudents;

        document.getElementById('save-btn').addEventListener('click', () => {
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
        });
        
        document.getElementById('confirm-save-btn').addEventListener('click', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
            modal.hide();
            saveStudents();
        });

    </script>
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

</body>

</html>