<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate inputs
    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $accountId = filter_input(INPUT_POST, 'AccountId', FILTER_SANITIZE_NUMBER_INT);
    $password = $_POST['password'];
    $confirmPassword = $_POST['cpassword'];

    // Check if email is valid
    if (!$email) {
        echo "<script>alert('Invalid email address!');</script>";
        exit;
    }

    // Check if account ID is exactly 6 digits
    if (!preg_match('/^\d{6}$/', $accountId)) {
        echo "<script>alert('Account ID must be exactly 6 digits!');</script>";
        exit;
    }

    // Check if password contains uppercase letters and special characters
    if (!preg_match('/^(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password)) {
        echo "<script>alert('Password must contain at least 8 characters, one uppercase letter, and one special character!');</script>";
        exit;
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
        exit;
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute SQL query using prepared statements to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO heads_db (fullname, email, account_id, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fullname, $email, $accountId, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Registration success!'); window.location.href='head-register.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
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
  <title>Clinic Management System</title>
  <link href="assets/img/bcp_logo.png" rel="icon">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<style> 
  .form-control, .form-check-input {
    border: 1px solid #999797;
  }

  .invalid-feedback {
    position: absolute;
    color: red;
    font-size: 12px;
    top: 100%; /* Adjust position relative to the input */
  }

  .form-group {
    position: relative; /* To contain the error message */
    margin-bottom: 1.5rem; /* Adjust margin to accommodate messages */
  }

  #password-error {
    display: none;
  }

  /* Modal styling */
  .modal-content {
    border-radius: 8px;
  }
</style>

<body>
<main>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Health Department Registrations</h5>
                    <p class="text-center small">Please fill all the forms below</p>
                  </div>

                  <form class="row g-3 needs-validation" novalidate id="registrationForm" method="POST">
                      <div class="col-12">
                        <label for="fullname" class="form-label">Fullname</label>
                        <input type="text" name="fullname" class="form-control" id="fullname" required>
                        <div class="invalid-feedback">Please, enter a name!</div>
                      </div>

                      <div class="col-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email" required oninput="checkEmail()">
                        <div id="emailError" class="invalid-feedback"></div>
                      </div>

                      <div class="col-12">
                        <label for="AccountId" class="form-label">AccountId</label>
                        <input type="text" name="AccountId" class="form-control" id="AccountId" required oninput="checkAccountId()">
                        <div id="accountIdError" class="invalid-feedback"></div>
                      </div>

                      <div class="col-12">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                          <input type="password" name="password" class="form-control" id="password" required>
                          <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                            <i class="fa fa-eye"></i>
                          </button>
                        </div>
                        <div class="invalid-feedback" id="password-error">Password must contain at least 8 characters, one uppercase letter, and one special character!</div>
                      </div>

                      <div class="col-12">
                        <label for="cpassword" class="form-label">Confirm password</label>
                        <div class="input-group">
                          <input type="password" name="cpassword" class="form-control" id="cpassword" required>
                          <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                            <i class="fa fa-eye"></i>
                          </button>
                        </div>
                        <div class="invalid-feedback" id="confirm-password-error">Passwords do not match!</div>
                      </div>

                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                          <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                          <div class="invalid-feedback">You must agree before submitting.</div>
                        </div>
                      </div>

                      <div class="col-12">
                        <button class="btn btn-primary w-100" type="button" id="submitBtn">Create Account</button>
                      </div>
                    </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Modal for confirmation -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmationModalLabel">Confirmation</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to submit the form?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmSubmit">Confirm</button>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("registrationForm");

    // Validate on input
    form.querySelectorAll("input").forEach(function (input) {
        input.addEventListener("input", function () {
            validateField(this);
        });
    });

    // Function to validate fields dynamically
    function validateField(input) {
        var errorDiv = input.nextElementSibling;
        if (input.id === "email") {
            if (!validateEmail(input.value)) {
                input.classList.add("is-invalid");
                errorDiv.textContent = 'Please enter a valid email address!';
                errorDiv.style.display = 'block';
            } else {
                input.classList.remove("is-invalid");
                errorDiv.style.display = 'none';
            }
        }

        if (input.id === "AccountId") {
            var accountIdRegex = /^\d{6}$/;
            if (!accountIdRegex.test(input.value)) {
                input.classList.add("is-invalid");
                errorDiv.textContent = 'Account ID must be exactly 6 digits!';
                errorDiv.style.display = 'block';
            } else {
                input.classList.remove("is-invalid");
                errorDiv.style.display = 'none';
            }
        }

        if (input.id === "password") {
            validatePassword();
        }

        checkPasswordMatch();
    }

    // Check if passwords match
        function checkPasswordMatch() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("cpassword").value;
            var errorDiv = document.getElementById("confirm-password-error");

            // Check if password field is valid before checking match
            if (password && confirmPassword) {
                if (password !== confirmPassword) {
                    document.getElementById("cpassword").classList.add("is-invalid");
                    errorDiv.textContent = "Passwords do not match!";
                    errorDiv.style.display = 'block';
                } else {
                    document.getElementById("cpassword").classList.remove("is-invalid");
                    errorDiv.style.display = 'none';
                }
            } else {
                // If password or confirmPassword is empty, clear error
                document.getElementById("cpassword").classList.remove("is-invalid");
                errorDiv.style.display = 'none';
            }
        }


    // Validate the password format
    function validatePassword() {
        var password = document.getElementById("password").value;
        var errorDiv = document.getElementById("password-error");
        var passwordRegex = /^(?=.*[A-Z])(?=.*[\W_]).{8,}$/;

        if (!passwordRegex.test(password)) {
            document.getElementById("password").classList.add("is-invalid");
            errorDiv.textContent = 'Password must contain at least 8 characters, one uppercase letter, and one special character!';
            errorDiv.style.display = 'block';
        } else {
            document.getElementById("password").classList.remove("is-invalid");
            errorDiv.style.display = 'none';
        }
    }

    // Validation logic on submit button click
    document.getElementById("submitBtn").addEventListener("click", function () {
        validateForm();
    });

    // Validate the entire form before showing the modal
    function validateForm() {
        // Ensure HTML5 form validation passes
        if (form.checkValidity()) {
            // Perform additional password matching check
            validatePassword();
            checkPasswordMatch();

            // If there are no visible errors
            if (!document.querySelector(".is-invalid")) {
                // If all is valid, show the modal
                var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'), {});
                confirmationModal.show();
            } else {
                form.reportValidity(); // Trigger form errors
            }
        } else {
            form.reportValidity(); // Trigger form errors if HTML5 validation fails
        }
    }

    // Modal confirm submit logic
    document.getElementById("confirmSubmit").addEventListener("click", function () {
        form.submit(); // Proceed to submit the form once the user confirms
    });

    // Password toggle visibility logic
    document.getElementById('togglePassword').addEventListener('click', function () {
        togglePasswordVisibility('password', this);
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        togglePasswordVisibility('cpassword', this);
    });

    // Function to toggle password visibility
    function togglePasswordVisibility(inputId, toggleButton) {
        const passwordInput = document.getElementById(inputId);
        const icon = toggleButton.querySelector('i');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
});

function checkEmail() {
    const email = document.getElementById('email').value;
    const emailError = document.getElementById('emailError');

    if (email) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'assets/validation/check_email.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.exists) {
                    emailError.textContent = 'Email is already registered! Please use a different one.';
                    emailError.classList.add('text-danger');
                    document.getElementById('email').classList.add('is-invalid');
                } else {
                    emailError.textContent = '';
                    document.getElementById('email').classList.remove('is-invalid');
                }
            }
        };

        xhr.send('email=' + encodeURIComponent(email));
    } else {
        emailError.textContent = '';
        document.getElementById('email').classList.remove('is-invalid');
    }
}

function checkAccountId() {
    const accountId = document.getElementById('AccountId').value;
    const accountIdError = document.getElementById('accountIdError');

    if (accountId) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'assets/validation/check_account_id.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (response.exists) {
                    accountIdError.textContent = 'AccountId is already taken!';
                    accountIdError.classList.add('text-danger');
                    document.getElementById('AccountId').classList.add('is-invalid');
                } else {
                    accountIdError.textContent = '';
                    document.getElementById('AccountId').classList.remove('is-invalid');
                }
            }
        };

        xhr.send('AccountId=' + encodeURIComponent(accountId));
    } else {
        accountIdError.textContent = '';
        document.getElementById('AccountId').classList.remove('is-invalid');
    }
}
</script>

</body>
</html>  