<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Clinic Management System</title>
  <link href="assets/img/bcp logo.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<style> 
  .form-control, .form-check-input {
    border: 1px solid #999797;
  }

  .form-check-label a {
      color: black;
  }

  #password-error {
      display: none; /* Hide error message initially */
  }
</style>
<body>
  <main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">  
                <a href="blankindex.html" class="logo d-flex align-items-center w-auto">
                  <img src="https://elc-public-images.s3.ap-southeast-1.amazonaws.com/bcp-olp-logo-mini2.png" alt="Logo">
                  <span class="d-none d-lg-block">Staff Registrations</span>
                </a>
              </div>
              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create Staff Access Account</h5>
                    <p class="text-center small">Please fill all the forms below</p>
                  </div>

                  <form class="row g-3 needs-validation" novalidate onsubmit="return checkPasswordMatch()">
                    <div class="col-12">
                      <label for="fullname" class="form-label">Fullname</label>
                      <input type="text" name="fullname" class="form-control" id="fullname" required>
                      <div class="invalid-feedback">Please, enter a name!</div>
                    </div>

                    <div class="col-12">
                      <label for="email" class="form-label">Email</label>
                      <input type="email" name="email" class="form-control" id="email" required>
                      <div class="invalid-feedback">Please enter a valid email address!</div>
                    </div>

                    <div class="col-12">
                      <label for="AccountId" class="form-label">AccountId</label>
                      <div class="input-group has-validation">
                        <input type="text" name="AccountId" class="form-control" id="AccountId" required>
                        <div class="invalid-feedback">Please choose a username.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" name="password" class="form-control" id="password" required>
                      <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                      <label for="cpassword" class="form-label">Confirm password</label>
                      <input type="password" name="cpassword" class="form-control" id="cpassword" required>
                      <div class="invalid-feedback" id="password-error">Passwords do not match!</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Create Account</button>
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

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    function checkPasswordMatch() {
      var password = document.getElementById("password").value;
      var confirmPassword = document.getElementById("cpassword").value;
      var errorDiv = document.getElementById("password-error");

      if (password !== confirmPassword) {
          errorDiv.style.display = 'block';
          return false; // Prevent form submission
      } else {
          errorDiv.style.display = 'none'; // Hide error message if passwords match
      }
      return true; // Allow form submission
    }
  </script>
</body>
</html>
