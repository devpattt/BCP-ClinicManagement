<?php
session_start();

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['username'])) {
  header("Location: superadmin/mainpage.php");
  exit(); 
}

include '../fetchfname.php';
include '../connection.php';
include 'patientrec.php'; // Include the patientrec.php file to use the generateUniqueId function

// PROCESS FORM SUBMISSION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize POST data
    $patient_selection = htmlspecialchars(trim($_POST['patient_selection']));
    
    // Retrieve name fields (middle name is optional)
    $first_name  = htmlspecialchars(trim($_POST['first_name']));
    $middle_name = htmlspecialchars(trim($_POST['middle_name'])); // optional field
    $last_name   = htmlspecialchars(trim($_POST['last_name']));
    
    // Create fullname: if middle name is provided, include it; otherwise, omit it.
    if (!empty($middle_name)) {
        $fullname = $last_name . ", " . $first_name . " " . $middle_name;
    } else {
        $fullname = $last_name . ", " . $first_name;
    }
    
    $contact_number = htmlspecialchars(trim($_POST['contact_number']));
    $birthday       = htmlspecialchars(trim($_POST['birthday']));
    
    // If the patient is a Student, then process additional student fields
    if ($patient_selection === "Student") {
        $student_number = isset($_POST['student_number']) ? htmlspecialchars(trim($_POST['student_number'])) : "";
        $year_level     = htmlspecialchars(trim($_POST['year_level']));
        $sex            = htmlspecialchars(trim($_POST['sex']));
        $department_code = htmlspecialchars(trim($_POST['department_code']));
        
        // Validate allowed gender values
        $allowed_genders = ['Male', 'Female'];
        if (!in_array($sex, $allowed_genders)) {
            die("Invalid gender selected.");
        }
        
        // Generate a unique ID
        $unique_id = generateUniqueId($conn);
        
        // Prepare and execute the SQL statement for student patients
        $stmt = $conn->prepare("INSERT INTO bcp_sms3_patients (unique_id, fullname, student_number, contact_number, sex, birthday, year_level, department_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("SQL Prepare Error: " . $conn->error);
        }
        $stmt->bind_param("ssssssss", $unique_id, $fullname, $student_number, $contact_number, $sex, $birthday, $year_level, $department_code);
    
        if ($stmt->execute()) {
            echo "success";
        } else {
            die("Error inserting record: " . $stmt->error);
        }
        $stmt->close();
    } else {
        // For non-student patients, use the other patients table.
        $sex            = htmlspecialchars(trim($_POST['sex']));
        $department_code = htmlspecialchars(trim($_POST['department_code']));
        
        // Validate allowed gender values
        $allowed_genders = ['Male', 'Female'];
        if (!in_array($sex, $allowed_genders)) {
            die("Invalid gender selected.");
        }
        
        // Generate a unique ID and set the creation timestamp
        $unique_id = generateUniqueId($conn);
        $created_at = date('Y-m-d H:i:s');
        
        // Prepare and execute the SQL statement for other patients
        $stmt = $conn->prepare("INSERT INTO bcp_sms3_other_patients (unique_id, fullname, contact, birthdate, sex, department, create_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("SQL Prepare Error: " . $conn->error);
        }
        $stmt->bind_param("sssssss", $unique_id, $fullname, $contact_number, $birthday, $sex, $department_code, $created_at);
    
        if ($stmt->execute()) {
            echo "success";
        } else {
            die("Error inserting record: " . $stmt->error);
        }
        $stmt->close();
    }
    
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Add Records</title>
  <link href="../assets/img/bcp logo.png" rel="icon">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/forms.css" rel="stylesheet">
  <style>
    /* Style for the suggestion dropdown */
    #studentSuggestions {
      border: 1px solid #ddd;
      border-top: none;
      max-height: 200px;
      overflow-y: auto;
      position: absolute;
      width: 100%;
      background-color: #fff;
      z-index: 1000;
    }
    #studentSuggestions li {
      padding: 8px;
      cursor: pointer;
    }
    #studentSuggestions li:hover {
      background-color: #f0f0f0;
    }
  </style>
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
          <i class="bi bi-hospital"></i><span>Clinic Management</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="system-nav" class="nav-content collapse show" data-bs-parent="#sidebar-nav">
          <li>
            <a href="clinic-dashboard.php">
              <i class="bi bi-circle"></i><span>Report and Analytics</span>
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
      <h1>Form Elements</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="clinic-dashboard.php">Dashboard</a></li>
          <li class="breadcrumb-item active">Registration Forms</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <!-- Left Card: Patient Basic Information -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Patient Basic Information</h5>
              <form id="form1" method="post" action="forms-elements.php">
                <!-- Patient Selection -->
                <div class="row mb-3">
                  <label for="patient_selection" class="col-sm-2 col-form-label">Patient</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="patient_selection" name="patient_selection" required>
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
                <!-- Student Number (only visible if Student is selected) -->
                <div class="row mb-3" id="studentNumberRow" style="display:none; position: relative;">
                  <label for="student_number" class="col-sm-2 col-form-label">Student Number</label>
                  <div class="col-sm-10">
                    <input type="text" id="student_number" name="student_number" class="form-control" placeholder="Enter Student Number">
                    <!-- Suggestion List -->
                    <ul id="studentSuggestions" style="display:none;"></ul>
                  </div>
                </div>
                <!-- Recommendation Message (if no match) -->
                <div class="row mb-3" id="recommendationMessage" style="display:none;">
                  <div class="col-sm-12">
                    <div class="alert alert-info" role="alert"></div>
                  </div>
                </div>
                <!-- First Name, Middle Name, Last Name, Contact Number -->
                <div class="row mb-3">
                  <label for="first_name" class="col-sm-2 col-form-label">First Name</label>
                  <div class="col-sm-10">
                    <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Enter First Name" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="middle_name" class="col-sm-2 col-form-label">Middle Name</label>
                  <div class="col-sm-10">
                    <input type="text" id="middle_name" name="middle_name" class="form-control" placeholder="Enter Middle Name">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="last_name" class="col-sm-2 col-form-label">Last Name</label>
                  <div class="col-sm-10">
                    <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Enter Last Name" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="contact_number" class="col-sm-2 col-form-label">Contact Number</label>
                  <div class="col-sm-10">
                    <input type="text" id="contact_number" name="contact_number" class="form-control" placeholder="Enter Contact Number" required>
                  </div>
                </div>
                <!-- Birthday Input -->
                <div class="row mb-3">
                  <label for="birthday" class="col-sm-2 col-form-label">Birth Date</label>
                  <div class="col-sm-10">
                    <input type="date" id="birthday" name="birthday" class="form-control" required>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- Right Card: Additional Diagnostic Informations -->
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Additional Diagnostic Informations</h5>
              <form id="form2" method="post" action="forms-elements.php">
                <!-- Year Level (for Student only) -->
                <div class="row mb-3" id="yearLevelRow">
                  <label for="year_level" class="col-sm-2 col-form-label">Year Level</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="year_level" name="year_level" required>
                      <option value="">Select Year Level</option>
                      <option value="shs">Senior High School (SHS)</option>
                      <option value="1st_year">1st Year</option>
                      <option value="2nd_year">2nd Year</option>
                      <option value="3rd_year">3rd Year</option>
                      <option value="4th_year">4th Year</option>
                    </select>
                  </div>
                </div>
                <!-- Sex (always visible) -->
                <div class="row mb-3">
                  <label for="sex" class="col-sm-2 col-form-label">Sex</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="sex" name="sex" required>
                      <option value="">Select Gender</option>
                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                    </select>
                  </div>
                </div>
                <!-- Department Code (for Student/Teacher only) -->
                <div class="row mb-3" id="departmentCodeRow">
                  <label for="department_code" class="col-sm-2 col-form-label">Department Code</label>
                  <div class="col-sm-10">
                    <input type="text" id="department_code" name="department_code" class="form-control" placeholder="Enter Department Code" required>
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

    <!-- Alerts -->
    <div id="validationAlert" class="alert alert-danger" style="display:none;">Please fill all the fields in both forms.</div>
    <div id="successAlert" class="alert alert-success" style="display:none;">Forms submitted successfully!</div>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- JavaScript Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        var patientSelection = document.getElementById("patient_selection");
        var studentNumberRow = document.getElementById("studentNumberRow");
        var yearLevelRow = document.getElementById("yearLevelRow");
        var departmentCodeRow = document.getElementById("departmentCodeRow");

        // Show/hide fields based on Patient selection
        patientSelection.addEventListener("change", function() {
          var selected = this.value;
          if (selected === "Student") {
            studentNumberRow.style.display = "flex";
            yearLevelRow.style.display = "flex";
            departmentCodeRow.style.display = "flex";
          } else if (selected === "Teacher") {
            studentNumberRow.style.display = "none";
            yearLevelRow.style.display = "none";
            departmentCodeRow.style.display = "flex";
          } else {
            studentNumberRow.style.display = "none";
            yearLevelRow.style.display = "none";
            departmentCodeRow.style.display = "none";
          }
        });

        // Auto-suggestion on Student Number input
        var studentNumberInput = document.getElementById("student_number");
        var suggestionList = document.getElementById("studentSuggestions");
        var recDiv = document.getElementById("recommendationMessage");

        studentNumberInput.addEventListener("input", function() {
          var query = this.value.trim();
          recDiv.style.display = "none";

          // Only search if at least 2 characters
          if(query.length < 2) {
            suggestionList.style.display = "none";
            return;
          }
          // Fetch suggestions from get_student.php (which queries the database)
          fetch("get_student.php?student_number=" + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
              suggestionList.innerHTML = "";
              if(data.length > 0) {
                recDiv.style.display = "none";
                data.forEach(function(student) {
                  var li = document.createElement("li");
                  li.textContent = student.student_number + " - " + student.first_name + " " + student.last_name;
                  
                  li.addEventListener("click", function() {
                    // Fill form fields with the suggestion data
                    studentNumberInput.value = student.student_number;
                    document.getElementById("first_name").value = student.first_name || "";
                    document.getElementById("middle_name").value = student.middle_name || "";
                    document.getElementById("last_name").value = student.last_name || "";
                    document.getElementById("contact_number").value = student.contact_number || "";
                    
                    // Format the birthday value correctly
                    var rawDate = student.birthday || "";
                    var birthdayValue = "";
                    if (rawDate) {
                      // Check if it's already in ISO format (YYYY-MM-DD)
                      var isoMatch = rawDate.match(/^(\d{4})-(\d{2})-(\d{2})$/);
                      if (isoMatch) {
                        birthdayValue = rawDate;
                      } else {
                        // Try MM/DD/YYYY format
                        var slashParts = rawDate.split("/");
                        if (slashParts.length === 3) {
                          var mm = slashParts[0].padStart(2, '0');
                          var dd = slashParts[1].padStart(2, '0');
                          var yyyy = slashParts[2];
                          birthdayValue = yyyy + "-" + mm + "-" + dd;
                        } else {
                          // Fallback: use Date constructor
                          var dateObj = new Date(rawDate);
                          if (!isNaN(dateObj.getTime())) {
                            var year = dateObj.getFullYear();
                            var month = String(dateObj.getMonth() + 1).padStart(2, '0');
                            var day = String(dateObj.getDate()).padStart(2, '0');
                            birthdayValue = year + "-" + month + "-" + day;
                          }
                        }
                      }
                    }
                    document.getElementById("birthday").value = birthdayValue;
                    
                    // Map the returned year_level to the select options if necessary.
                    var yearMapping = {
                      "shs": "shs",
                      "1st": "1st_year",
                      "1st_year": "1st_year",
                      "2nd": "2nd_year",
                      "2nd_year": "2nd_year",
                      "3rd": "3rd_year",
                      "3rd_year": "3rd_year",
                      "4th": "4th_year",
                      "4th_year": "4th_year"
                    };
                    if(document.getElementById("year_level")){
                      var returnedYear = student.year_level;
                      document.getElementById("year_level").value = yearMapping[returnedYear] || returnedYear || "";
                    }
                    if(document.getElementById("sex")){
                      document.getElementById("sex").value = student.sex || "";
                    }
                    if(document.getElementById("department_code")){
                      document.getElementById("department_code").value = student.department_code || "";
                    }
                    
                    // Hide suggestions and recommendation message
                    suggestionList.style.display = "none";
                    recDiv.style.display = "none";
                  });
                  suggestionList.appendChild(li);
                });
                suggestionList.style.display = "block";
              } else {
                suggestionList.style.display = "none";
                recDiv.style.display = "block";
                recDiv.querySelector(".alert").innerText = "No matching student found. Please verify your student number or create a new record.";
              }
            })
            .catch(error => console.error("[DEBUG] Error fetching suggestions:", error));
        });
      });

      // Validate only visible fields
  function areAllInputsFilled(form) {
    for (var i = 0; i < form.elements.length; i++) {
      var element = form.elements[i];
      // If the element is hidden (offsetParent === null), skip
      if (element.offsetParent === null) continue;

      // Skip middle_name specifically (allow it to be empty)
      if (element.name === 'middle_name') continue;

      // For all other inputs or selects, check if there's a value
      if ((element.tagName === 'INPUT' || element.tagName === 'SELECT') && !element.value) {
        return false;
      }
    }
    return true;
  }

  // Show validation alert
  function showValidationAlert(message) {
    const validationAlert = document.getElementById('validationAlert');
    validationAlert.innerText = message;
    validationAlert.style.display = 'block';
    setTimeout(() => {
      validationAlert.style.display = 'none';
    }, 3000);
  }

  // Show success alert
  function showSuccessAlert() {
    const successAlert = document.getElementById('successAlert');
    successAlert.style.display = 'block';
    setTimeout(() => {
      successAlert.style.display = 'none';
      window.location.href = 'forms-elements.php';
    }, 3000);
  }

  // Handle form submission using fetch
  document.getElementById('submitBtn').addEventListener('click', function(event) {
    event.preventDefault();
    var form1 = document.getElementById('form1');
    var form2 = document.getElementById('form2');

    // Check only the visible & required fields (excluding middle_name)
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
      console.log('Server response:', result);
      if(result.trim() === "success") {
        showSuccessAlert();
      } else {
        showValidationAlert("Error: " + result);
      }
    })
    .catch(error => showValidationAlert('An error occurred: ' + error));
  });
    </script>
  </main>
</body>
</html>
