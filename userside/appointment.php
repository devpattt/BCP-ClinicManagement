<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: userside/userside.php");
  exit(); 
}

include '../fetchfname.php';
include '../connection.php';
include 'connectappoint.php';

// Fetch appointments from the database
$sql = "SELECT name, email, reason, date, time FROM bcp_sms3_appointment ORDER BY date DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) { 

    while ($row = $result->fetch_assoc()) {
        // Convert the stored time to 12-hour format
        $formatted_time = date("h:i A", strtotime($row['time']));
        
    }
} else {
    echo "<p>No appointments found.</p>";
}

$conn->close();
?>


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System / Add Records</title>
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
  <link href="../https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <link href="../assets/css/forms.css" rel="stylesheet">
  <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>

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
            <img src="../assets/img/default profile.jpg" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($fullname); ?></span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
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
                      <a href="userside.php" > 
                        <i class="bi bi-circle" ></i><span>Home</span>
                      </a>
                    </li>
                    <li>
                      <a href="record-history.php" class="active">
                        <i class="bi bi-circle"></i><span>Appointment</span>
                      </a>
                    </li>
                    <li>
                      <a href="feedback.php"  > 
                        <i class="bi bi-circle" ></i><span>Feedback</span>
                      </a>
                    </li>
                  </ul>
                </li>


        <hr class="sidebar-divider">
  

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Appointment</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="userside.php">Home</a></li>
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

          <script>
        function validateForm(event) {
            let name = document.forms["appointmentForm"]["name"].value;
            let email = document.forms["appointmentForm"]["email"].value;
            let reason = document.forms["appointmentForm"]["reason"].value;
            let date = document.forms["appointmentForm"]["date"].value;
          
            if (name == "" || email == ""|| reason == "" || date == "") {
                alert("All fields must be filled out");
                event.preventDefault();
            }
        }
    </script>

<div class="container">
        <h2>Book an Appointment</h2>
        <form id="appointmentForm" action="appointment.php" method="POST" onsubmit="validateForm(event)">
   
    <input type="email" name="email" placeholder="Your Email" required>
    <input type="text" name="reason" placeholder="Your Reason" required>
    <input type="date" name="date" required>
    <button type="submit">Book Now</button>
</form>
        <p id="confirmation" style="color: green;"></p>
    </div>

    


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
            window.location.href = '../forms-elements.php'; 
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


</body>

</html>