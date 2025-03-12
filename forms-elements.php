<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: index.php");
  exit(); 
}

include 'fetchfname.php';
include 'connection.php';
include 'fetch_symptoms.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $fullname = htmlspecialchars(trim($_POST['fullname']));
           $student_number = htmlspecialchars(trim($_POST['student_number']));
             $contact = htmlspecialchars(trim($_POST['contact']));
               $s_gender = htmlspecialchars(trim($_POST['s_gender']));
                 $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
                   $year_level = htmlspecialchars(trim($_POST['year_level']));
                     $condition = htmlspecialchars(trim($_POST['condition']));
                        $treatment = htmlspecialchars(trim($_POST['treatment']));
                          $allowed_genders = ['Male', 'Female', 'Other', 'Prefer_not_to_say'];

            if (!in_array($s_gender, $allowed_genders)) {
                die("Invalid gender selected.");
            }
              $stmt = $conn->prepare("INSERT INTO bcp_sms3_patients (fullname, student_number, contact,s_gender, age, year_level, conditions, treatment) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
              $stmt->bind_param("ssssssss", $fullname, $student_number, $contact, $s_gender, $age, $year_level, $condition, $treatment);

            if ($stmt->execute()) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();
                    });
                </script>";
            } else {
                echo "Error inserting record: " . $stmt->error;
            }
            $stmt->close();
            $conn->close();
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Add Records</title>
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
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/forms.css" rel="stylesheet">
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
                      <a href="forms-elements.php" class="active">
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
                        <a href="forecastingai.php">
                          <i class="bi bi-circle" ></i><span>A.I Anomaly</span>
                        </a>
                      </li>
                
                  </ul>
                </li>


        <hr class="sidebar-divider">
  

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Patint Registration</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Registration Forms</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

  
    <section class="section">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Patient Basic Information</h5>

          <!-- General Form Elements -->
          <form id="form1" method="post" action="forms-elements.php">
            <div class="row mb-3">
              <label for="fullname" class="col-sm-2 col-form-label">Fullname</label>
              <div class="col-sm-10">
                <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Enter Fullname" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="student_number" class="col-sm-2 col-form-label">Patient</label>
              <div class="col-sm-10">
                <!-- BAGO TO -->
              <select class="form-control" id="student_number" name="student_number" required>
                  <option value="">Select</option>
                  <option value="Student">Student</option>
                  <option value="Teacher">Teacher</option>
                  <option value="Janitor">Janitor</option>
                  <option value="Guard">Guard</option>
                  <option value="Vendor">Vendor</option>
                  <option value="Visitor">Visitor</option>
                  <option value="Other">Other</option>
                </select>
              </div>
            </div>
            <!-- HANGGANG DITO -->
            <div class="row mb-3">
              <label for="contact" class="col-sm-2 col-form-label">Contact</label>
              <div class="col-sm-10">
                <input type="text" id="contact" name="contact" class="form-control" placeholder="Enter Contact" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="s_gender" class="col-sm-2 col-form-label">Gender</label>
              <div class="col-sm-10">
                <select class="form-control" id="s_gender" name="s_gender" required>
                  <option value="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                  <option value="Prefer_not_to_say">Prefer not to say</option>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label for="age" class="col-sm-2 col-form-label">Age</label>
              <div class="col-sm-10">
                <input type="number" class="form-control" id="age" name="age" placeholder="Enter Age" min="1" max="100" required>
              </div>
            </div>

            <!-- BAGO TO -->

            <div class="row mb-3" id="yearLevelRow" style="display: none;">
  <label for="year_level" class="col-sm-2 col-form-label">Year Level</label>
  <div class="col-sm-10">
    <select class="form-control" id="year_level" name="year_level">
      <option value="">Select Year Level</option>
      <option value="shs">Senior High School (SHS)</option>
      <option value="1st_year">1st Year</option>
      <option value="2nd_year">2nd Year</option>
      <option value="3rd_year">3rd Year</option>
      <option value="4th_year">4th Year</option>
    </select>
  </div>
</div>
<!-- HANGGANG DITO -->
          </form><!-- End General Form Elements -->
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Additional Diagnostic Informations</h5>

          <!-- Advanced Form Elements -->
          <form id="form2" method="post" action="forms-elements.php">
            <div class="row mb-3">
              <label for="condition" class="col-sm-2 col-form-label">Symptoms</label>
              <div class="col-sm-10">
      <!-- NEW -->
              <input type="text" class="form-control" id="condition" name="condition" placeholder="Enter Symptoms" required onkeyup="fetchSuggestions(this.value)">
                <ul id="suggestions-list" class="list-group" style="position: absolute; z-index: 1000; display: none;"></ul>
      <!-- NEW -->
              </div>
            </div>
            <div class="row mb-3">
              <label for="treatment" class="col-sm-2 col-form-label">Treatment Given</label>
              <div class="col-sm-10">
                <input type="text" id="treatment" name="treatment" class="form-control" placeholder="Enter Treatment" required>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label"></label>
              <div class="col-sm-10">
                <button id="submitBtn" type="submit" class="btn btn-primary" style="background-color: #1e3a8a; border-color: #1e3a8a;">Submit Form</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

 <div id="validationAlert" class="alert alert-danger">
        Please fill all the fields in both forms.
    </div>
<div id="successAlert" class="alert alert-success" style="display: none;">
    Forms submitted successfully!
</div>


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
  document.addEventListener('DOMContentLoaded', function() {
    var dateInput = document.getElementById('date');
    
    var today = new Date();
    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0');  
    var day = String(today.getDate()).padStart(2, '0');

    var todayDate = year + '-' + month + '-' + day;
    dateInput.setAttribute('min', todayDate);
  });

  document.getElementById('age').addEventListener('input', function() {
    var ageInput = document.getElementById('age');
    var ageValue = ageInput.value;
    }
  );

  document.addEventListener('DOMContentLoaded', function() {

    var dateInput = document.getElementById('date');
    if (dateInput) {
        var today = new Date();
        var year = today.getFullYear();
        var month = String(today.getMonth() + 1).padStart(2, '0');  
        var day = String(today.getDate()).padStart(2, '0');
        var todayDate = year + '-' + month + '-' + day;
        dateInput.setAttribute('min', todayDate);
    }

    var ageInput = document.getElementById('age');
    if (ageInput) {
        ageInput.addEventListener('input', function() {
            var ageValue = this.value;
        });
    }
});

document.getElementById('submitBtn').addEventListener('click', function(event) {
    event.preventDefault(); 

    var form1 = document.getElementById('form1');
    var form2 = document.getElementById('form2');

    function areAllInputsFilled(form) {
        for (var i = 0; i < form.elements.length; i++) {
            var element = form.elements[i];
            if (element.tagName === 'INPUT' && element.type !== 'button' && !element.value) {
                return false;
            }
        }
        return true;
    }

    function showValidationAlert(message) {
        const validationAlert = document.getElementById('validationAlert');
        validationAlert.innerText = message;
        validationAlert.style.display = 'block'; 

        setTimeout(() => {
            validationAlert.style.display = 'none'; 
        }, 3000);
    }

    function showSuccessAlert() {
        const successAlert = document.getElementById('successAlert');
        successAlert.style.display = 'block'; 
        setTimeout(() => {
            successAlert.style.display = 'none'; 
            window.location.href = 'forms-elements.php'; 
        }, 3000);
    }

    if (!areAllInputsFilled(form1) || !areAllInputsFilled(form2)) {
        showValidationAlert('Please fill all the fields in both forms.');
        return;
    }

    var combinedData = new FormData();

    for (var i = 0; i < form1.elements.length; i++) {
        var element = form1.elements[i];
        if (element.name) {
            combinedData.append(element.name, element.value);
        }
    }

    for (var i = 0; i < form2.elements.length; i++) {
        var element = form2.elements[i];
        if (element.name) {
            combinedData.append(element.name, element.value);
        }
    }

    fetch('forms-elements.php', {
        method: 'POST',
        body: combinedData
    })
    .then(response => response.text())
    .then(result => {
        showSuccessAlert(); 
    })
    .catch(error => showValidationAlert('An error occurred: ' + error)); 
});


</script>
<!-- BAGO TO -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    var studentNumberDropdown = document.getElementById("student_number");
    var yearLevelRow = document.getElementById("yearLevelRow");

    studentNumberDropdown.addEventListener("change", function() {
        if (this.value === "Student") {
            yearLevelRow.style.display = "flex";  // Show Year Level
        } else {
            yearLevelRow.style.display = "none";  // Hide Year Level
        }
    });
});
</script>
<!-- RECOMMENDATION GALING SA DATABASE -->
<script>
function fetchSuggestions(query) {
    if (query.length < 2) {
        document.getElementById("suggestions-list").style.display = "none";
        return;
    }

    fetch("fetch_symptoms.php?query=" + query)
    .then(response => response.json())
    .then(data => {
        let suggestionsList = document.getElementById("suggestions-list");
        suggestionsList.innerHTML = ""; // Clear previous suggestions
        suggestionsList.style.display = "block";

        data.forEach(symptom => {
            let item = document.createElement("li");
            item.classList.add("list-group-item");
            item.textContent = symptom;
            item.onclick = function() {
                document.getElementById("condition").value = symptom;
                suggestionsList.style.display = "none";
            };
            suggestionsList.appendChild(item);
        });
    })
    .catch(error => console.error("Error fetching symptoms:", error));
}
</script>

</body>

</html>