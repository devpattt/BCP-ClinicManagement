<?php
// Include the database connection file
include 'connection.php'; // Ensure this file contains the database connection code

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $student_number = htmlspecialchars(trim($_POST['student_number']));
    $contact = htmlspecialchars(trim($_POST['contact']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $age = filter_var($_POST['age'], FILTER_VALIDATE_INT);
    $year_level = htmlspecialchars(trim($_POST['year_level']));
    $condition = htmlspecialchars(trim($_POST['condition']));
    $treatment = htmlspecialchars(trim($_POST['treatment']));
    $note = htmlspecialchars(trim($_POST['note']));

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO bcp_sms3_patients (fullname, student_number, contact, gender, age, year_level, conditions, treatment, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiisss", $fullname, $student_number, $contact, $gender, $age, $year_level, $condition, $treatment, $note);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Success: redirect or display a success message
        echo '<script>alert("Record inserted successfully."); window.location.href="success_page.php";</script>';
    } else {
        // Error: display an error message
        echo '<script>alert("Error: ' . $stmt->error . '");</script>';
    }

    // Close the statement and connection
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
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

  <!-- ======= Header ======= -->
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
          </a><!-- End Profile Iamge Icon -->

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
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <div class="flex items-center w-full p-1 pl-6" style="display: flex; align-items: center; padding: 3px; width: 40px; background-color: transparent; height: 4rem;">
        <div class="flex items-center justify-center" style="display: flex; align-items: center; justify-content: center;">
            <img src="https://elc-public-images.s3.ap-southeast-1.amazonaws.com/bcp-olp-logo-mini2.png" alt="Logo" style="width: 30px; height: auto;">
        </div>
      </div>

      <div style="display: flex; flex-direction: column; align-items: center; padding: 16px;">
        <div style="display: flex; align-items: center; justify-content: center; width: 96px; height: 96px; border-radius: 50%; background-color: #334155; color: #e2e8f0; font-size: 48px; font-weight: bold; text-transform: uppercase; line-height: 1;">
            LC
        </div>
        <div style="display: flex; flex-direction: column; align-items: center; margin-top: 24px; text-align: center;">
            <div style="font-weight: 500; color: #fff;">
                Name
            </div>
            <div style="margin-top: 4px; font-size: 14px; color: #fff;">
                ID
            </div>
        </div>
    </div>

    <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link " href="blankindex.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->
      <hr class="sidebar-divider">

                <li class="nav-heading">Clinic Management System</li>

                <li class="nav-item">
                  <a class="nav-link collapsed" data-bs-target="#system-nav" data-bs-toggle="collapse" href="#">
                    <i class="bi bi-hospital"></i><span>Clinic Management</span><i class="bi bi-chevron-down ms-auto"></i>
                  </a>
                  <ul id="system-nav" class="nav-content collapse show " data-bs-parent="#sidebar-nav">
                  <li>
                      <a href="clinic-dashboard.php">
                        <i class="bi bi-circle" ></i><span> Clinic Dashboard</span>
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
                      <a href="pages-blank.php">
                        <i class="bi bi-circle" ></i><span>Medical Supplies</span>
                      </a>
                    </li>
                    <li>
                        <a href="blankanomaly.php">
                          <i class="bi bi-circle" ></i><span>A.I Anomaly</span>
                        </a>
                      </li>
                      <li>
                        <a href="">
                          <i class="bi bi-circle" ></i><span>Accounts Monitoring</span>
                        </a>
                      </li>
                  </ul>
                </li>


        <hr class="sidebar-divider">
     
     <li class="nav-heading">Pages</li>

     <li class="nav-item">
       <a class="nav-link collapsed" href="users-profile.html">
         <i class="bi bi-person"></i>
         <span>Profile</span>
       </a>
     </li><!-- End Profile Page Nav -->


     <li class="nav-item">
       <a class="nav-link collapsed" href="head-register.php">
         <i class="bi bi-card-list"></i>
         <span>Head Register</span>
       </a>
     </li>

     <li class="nav-item">
       <a class="nav-link collapsed" href="nurse-register.php">
         <i class="bi bi-card-list"></i>
         <span>Nurse Register</span>
       </a>
     </li>

     <li class="nav-item">
       <a class="nav-link collapsed" href="pages-login.php">
         <i class="bi bi-box-arrow-in-right"></i>
         <span>Login</span>
       </a>
     </li>

  
     <li class="nav-item">
       <a class="nav-link collapsed" href="pages-blank.html">
         <i class="bi bi-file-earmark"></i>
         <span>Blank</span>
       </a>
     </li><!-- End Blank Page Nav -->

   </ul>


  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Form Elements</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Forms</li>
          <li class="breadcrumb-item active">Elements</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

  
    <section class="section">
  <div class="row">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Patient Information</h5>

          <!-- General Form Elements -->
          <form id="form1" method="post" action="forms-elements.php">
            <div class="row mb-3">
              <label for="fullname" class="col-sm-2 col-form-label">Fullname</label>
              <div class="col-sm-10">
                <input type="text" id="fullname" name="fullname" class="form-control" placeholder="Enter Fullname" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="student_number" class="col-sm-2 col-form-label">Student Number</label>
              <div class="col-sm-10">
                <input type="text" id="student_number" name="student_number" class="form-control" placeholder="Enter Student Number" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="contact" class="col-sm-2 col-form-label">Emergency Contact</label>
              <div class="col-sm-10">
                <input type="text" id="contact" name="contact" class="form-control" placeholder="Enter Contact" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="gender" class="col-sm-2 col-form-label">Gender</label>
              <div class="col-sm-10">
                <select class="form-control" id="gender" name="gender" required>
                  <option value="">Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                  <option value="prefer_not_to_say">Prefer not to say</option>
                </select>
              </div>
            </div>
            <div class="row mb-3">
              <label for="age" class="col-sm-2 col-form-label">Age</label>
              <div class="col-sm-10">
                <input type="number" class="form-control" id="age" name="age" placeholder="Enter Age" min="1" max="100" required>
              </div>
            </div>
            <div class="row mb-3">
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
          </form><!-- End General Form Elements -->
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Additional Information</h5>

          <!-- Advanced Form Elements -->
          <form id="form2" method="post" action="forms-elements.php">
            <div class="row mb-3">
              <label for="condition" class="col-sm-2 col-form-label">Condition/Diagnosis</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="condition" name="condition" placeholder="Enter Condition/Diagnosis" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="treatment" class="col-sm-2 col-form-label">Treatment Given</label>
              <div class="col-sm-10">
                <input type="text" id="treatment" name="treatment" class="form-control" placeholder="Enter Treatment" required>
              </div>
            </div>
            <div class="row mb-3">
              <label for="note" class="col-sm-2 col-form-label">Note</label>
              <div class="col-sm-10">
                <textarea class="form-control" style="height: 100px" id="note" name="note" placeholder="Enter Recommendations" required></textarea>
              </div>
            </div>
            <div class="row mb-3">
              <label class="col-sm-2 col-form-label"></label>
              <div class="col-sm-10">
                <button id="submitBtn" type="submit" class="btn btn-primary">Submit Form</button>
              </div>
            </div>
          </form><!-- End Advanced Form Elements -->
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Record inserted successfully.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
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
    // Set the minimum date for the date input
    var dateInput = document.getElementById('date');
    var today = new Date();
    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0');  
    var day = String(today.getDate()).padStart(2, '0');
    var todayDate = year + '-' + month + '-' + day;
    dateInput.setAttribute('min', todayDate);
});


document.getElementById('submitBtn').addEventListener('click', function(event) {
    event.preventDefault(); 

    var form1 = document.getElementById('form1');
    var form2 = document.getElementById('form2');

    // Function to check if all inputs in a form are filled
    function areAllInputsFilled(form) {
        for (var i = 0; i < form.elements.length; i++) {
            var element = form.elements[i];
            if (element.tagName === 'INPUT' && element.type !== 'button' && !element.value) {
                return false;
            }
        }
        return true;
    }

    // Validate both forms
    if (!areAllInputsFilled(form1) || !areAllInputsFilled(form2)) {
        alert('Please fill all the fields in both forms.');
        return;
    }

    var combinedData = new FormData();

    // Append data from form1
    for (var i = 0; i < form1.elements.length; i++) {
        var element = form1.elements[i];
        if (element.name) {
            combinedData.append(element.name, element.value);
        }
    }

    // Append data from form2
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
        alert('Forms submitted successfully!');
        window.location.href = 'forms-elements.php'; // Redirect after successful submission
    })
    .catch(error => alert('An error occurred: ' + error));
});



</script>


</body>

</html>