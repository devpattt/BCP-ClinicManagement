
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

<style> 
  .form-control, .form-check-input {
    border: 1px solid #999797;
  }

  .form-check-label a {
      color: black;
  }

  .logo-img {
    max-width: 75px; /* Change this value to increase or decrease the logo size */
    height: auto;     /* Maintain the aspect ratio */
  }

  .card-title {
    margin-top: -60px; /* Optional: adjust negative margin to pull the text closer */
  }

  .robot-container {
  position: absolute;
  bottom: 0;
  left: 0;
  }

  .walking-robot {
    width: 100px; /* Adjust size */
  }


  .robot {
    font-size: 50px; /* Adjust size for your robot */
    position: absolute;
    bottom: 20px;
    animation: walk 10s infinite linear;
  }

  @keyframes walk {
    0% {
      left: -100px;
    }
    100% {
      left: 100%;
    }
  }



</style>

<body>
  <main>
    <div class="login-container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
      <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
              <img src="assets/img/bcp logo.png" alt="Logo" class="logo-img">
            </div>

              <div class="card mb-3">
                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your Account ID & Password to login</p>
                  </div>
                  

                  <form id="loginForm" action="pages-login.php" method="post">

                    <div class="col-12">
                      <label for="accountId" class="form-label">Account ID</label>
                      <div class="input-group has-validation">
                        <input type="text" name="accountId" class="form-control" id="accountId" required>
                        <div class="invalid-feedback">Please enter your username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="password" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have account? <a href="pages-register.html">Create an account</a></p>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
<!--
  <div class="robot-container">
  <video autoplay loop muted class="walking-robot">
  <source src="assets/img/cute ai.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>
  </div> -->

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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.4/gsap.min.js"></script>
<script>
// Set the distance to move to 80% of the viewport width to keep it within frame
//const moveDistance = window.innerWidth * 0.8; // Move 80% of the viewport width
//const duration = 8; // Increase duration for a slower movement

//gsap.to(".walking-robot", {
  //x: moveDistance,
  //duration: duration,
  //repeat: -1,
  //yoyo: true,
//});

</script>


</body>
</html>