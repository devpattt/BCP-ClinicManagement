<?php  
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session with proper cookie parameters
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '',
    'secure' => false,  // Set to true if using HTTPS
    'httponly' => true
]);
session_start();

// Adjust redirection paths if needed
require_once "../connection.php"; // This file should set up $conn

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Redirect if user is not logged in or not a "user"
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];

// Check if the questionnaire has already been completed
$query = "SELECT completed_questionnaire FROM bcp_sms3_users WHERE username = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($completed_questionnaire);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Error preparing query: " . $conn->error);
}

if ($completed_questionnaire) {
    header("Location: userside.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function sanitize_input($data) {
        return htmlspecialchars(trim($data));
    }

    $allergies           = sanitize_input($_POST['allergies']);
    $medical_conditions  = sanitize_input($_POST['medical_conditions']);
    $medication          = sanitize_input($_POST['medication']);
    $hospitalization     = sanitize_input($_POST['hospitalization']);
    $symptoms            = isset($_POST['symptoms']) ? implode(", ", $_POST['symptoms']) : 'None';
    $contact_with_sick   = sanitize_input($_POST['contact_with_sick']);
    $sleep_hours         = sanitize_input($_POST['sleep_hours']);
    $physical_activity   = sanitize_input($_POST['physical_activity']);
    $health_seminars     = sanitize_input($_POST['health_seminars']);

    $sql = "INSERT INTO bcp_sms3_health_questionnaire 
            (username, allergies, medical_conditions, medication, hospitalization, symptoms, contact_with_sick, sleep_hours, physical_activity, health_seminars) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssssss", 
            $username, 
            $allergies, 
            $medical_conditions, 
            $medication, 
            $hospitalization, 
            $symptoms, 
            $contact_with_sick, 
            $sleep_hours, 
            $physical_activity, 
            $health_seminars
        );

        if ($stmt->execute()) {
            $update_query = "UPDATE bcp_sms3_users SET completed_questionnaire = 1 WHERE username = ?";
            if ($update_stmt = $conn->prepare($update_query)) {
                $update_stmt->bind_param("s", $username);
                $update_stmt->execute();
                $update_stmt->close();
            } else {
                error_log("Error preparing update: " . $conn->error);
            }
            echo "<script>
                    alert('Thank you! Your health questionnaire has been submitted.');
                    window.location.href = 'userside.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Error submitting form: " . $stmt->error . "');</script>";
            error_log("Error executing insert: " . $stmt->error);
        }
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
        error_log("Error preparing insert: " . $conn->error);
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Health Questionnaire</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
      body {
          background-color: #f8f9fa;
      }
      /* Questionnaire container (hidden initially) */
      #questionnaire {
          background: #fff;
          padding: 30px;
          border-radius: 10px;
          box-shadow: 0 0 10px rgba(0,0,0,0.1);
          max-width: 600px;
          margin: 50px auto;
          display: none;
      }
  </style>
</head>
<body>

<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Agreement Modal using Bootstrap -->
<div class="modal fade" id="agreementModal" tabindex="-1" aria-labelledby="agreementModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="agreementModalLabel">Health Questionnaire Agreement</h5>
      </div>
      <div class="modal-body">
        <!-- Terms and Conditions Content -->
        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 15px;">
          <p>
            Below is a sample Terms and Conditions document for a student who is about to fill out a health conditions form. This sample is provided for informational purposes only and is not legal advice. It is strongly recommended that you have this document reviewed and tailored by a qualified legal professional.
          </p>
          <h5>Terms and Conditions for Student Health Information Form</h5>
          <p><strong>Effective Date:</strong> [Insert Date]</p>
          <h6>Acceptance of Terms</h6>
          <p>
            By completing and submitting this Health Information Form, you ("Student") acknowledge that you have read, understood, and agree to be bound by these Terms and Conditions.
          </p>
          <h6>Purpose of Data Collection</h6>
          <p>
            The information collected on this form is intended solely for understanding and supporting your health needs while you are enrolled as a student.
          </p>
          <h6>Consent to Provide Health Information</h6>
          <p>
            You voluntarily provide accurate, complete, and current information regarding your health conditions. False or misleading information may affect the support or services you receive.
          </p>
          <h6>Confidentiality and Data Security</h6>
          <p>
            <strong>a. Confidentiality:</strong> Your personal and health-related information is confidential and will only be disclosed to authorized personnel as required by law or for care purposes.
          </p>
          <p>
            <strong>b. Data Security:</strong> Industry-standard measures are used to protect your data; however, no method is entirely secure.
          </p>
          <h6>Use and Disclosure of Health Information</h6>
          <p>
            Your data will be used in accordance with applicable regulations and may be shared with health professionals or advisors when necessary.
          </p>
          <h6>Accuracy and Updates</h6>
          <p>
            You are responsible for the accuracy of the information provided. Please update any changes to your health condition promptly.
          </p>
          <h6>Voluntary Participation</h6>
          <p>
            Completing this form is voluntary. However, providing complete health information is encouraged to ensure proper support.
          </p>
          <h6>Limitation of Liability</h6>
          <p>
            The institution will use reasonable efforts to secure your information. It is not liable for damages from unauthorized access unless due to gross negligence.
          </p>
          <h6>Modifications to Terms and Conditions</h6>
          <p>
            The institution may modify these terms at any time. Continued use of the form indicates acceptance of the new terms.
          </p>
          <h6>Governing Law and Jurisdiction</h6>
          <p>
            These terms are governed by the laws of [Insert Jurisdiction]. Disputes will be subject to the exclusive jurisdiction of the courts in [Insert Location].
          </p>
          <h6>Contact Information</h6>
          <p>
            For questions regarding your health information or these Terms and Conditions, please contact:
          </p>
          <ul class="list-unstyled">
            <li><strong>Office:</strong> Bestlink College of the Philippines</li>
            <li><strong>Email:</strong> Bestlink@Gmail.com</li>
          </ul>
          <h6>Entire Agreement &amp; Acknowledgment</h6>
          <p>
            These Terms and Conditions constitute the entire agreement regarding your health information. By clicking "I Agree" or submitting the form, you acknowledge your understanding and consent.
          </p>
        </div>
        <!-- Agreement Acceptance Section -->
        <div class="mt-3 text-center">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="agreement" id="agreeYes" value="yes">
            <label class="form-check-label" for="agreeYes">I Agree</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="agreement" id="agreeNo" value="no">
            <label class="form-check-label" for="agreeNo">I Decline</label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="processAgreement()">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Disagreement Confirmation Modal using Bootstrap -->
<div class="modal fade" id="disagreeModal" tabindex="-1" aria-labelledby="disagreeModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="disagreeModalLabel">Are you sure?</h5>
      </div>
      <div class="modal-body">
        <p>If you disagree, you will be logged out.</p>
        <div class="mt-2 text-center">
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="disagreeConfirm" id="disagreeYes" value="yes">
            <label class="form-check-label" for="disagreeYes">Yes, log me out</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="disagreeConfirm" id="disagreeNo" value="no">
            <label class="form-check-label" for="disagreeNo">No, go back</label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="handleDisagreeChoice()">Submit</button>
      </div>
    </div>
  </div>
</div>

<!-- Questionnaire Form -->
<div id="questionnaire" class="container">
  <h2 class="text-center">Health Questionnaire</h2>
  <form action="" method="POST">
    <div class="mb-3">
      <label class="form-label">Do you have any known allergies?</label>
      <input type="text" name="allergies" class="form-control" placeholder="Specify or type 'None'" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Do you have any existing medical conditions?</label>
      <input type="text" name="medical_conditions" class="form-control" placeholder="Specify or type 'None'" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Are you currently taking any medication?</label>
      <input type="text" name="medication" class="form-control" placeholder="Specify or type 'None'" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Have you ever been hospitalized?</label>
      <input type="text" name="hospitalization" class="form-control" placeholder="Specify or type 'None'" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Have you experienced any of these symptoms recently?</label><br>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="symptoms[]" value="Fever"> Fever<br>
        <input class="form-check-input" type="checkbox" name="symptoms[]" value="Cough"> Cough<br>
        <input class="form-check-input" type="checkbox" name="symptoms[]" value="Shortness of breath"> Shortness of breath<br>
        <input class="form-check-input" type="checkbox" name="symptoms[]" value="Sore throat"> Sore throat<br>
        <input class="form-check-input" type="checkbox" name="symptoms[]" value="Loss of taste/smell"> Loss of taste/smell<br>
        <input class="form-check-input" type="checkbox" name="symptoms[]" value="None"> None<br>
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">Have you been in contact with someone sick?</label>
      <select name="contact_with_sick" class="form-select" required>
        <option value="">Select</option>
        <option value="No">No</option>
        <option value="Yes">Yes</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">How many hours of sleep do you get?</label>
      <select name="sleep_hours" class="form-select" required>
        <option value="">Select</option>
        <option value="Less than 4 hours">Less than 4 hours</option>
        <option value="4-6 hours">4-6 hours</option>
        <option value="7-9 hours">7-9 hours</option>
        <option value="More than 9 hours">More than 9 hours</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">How often do you exercise?</label>
      <select name="physical_activity" class="form-select" required>
        <option value="">Select</option>
        <option value="Never">Never</option>
        <option value="Once a week">Once a week</option>
        <option value="2-3 times a week">2-3 times a week</option>
        <option value="4+ times a week">4+ times a week</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Would you be interested in health seminars?</label>
      <select name="health_seminars" class="form-select" required>
        <option value="">Select</option>
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select>
    </div>
    <button type="submit" class="btn btn-success w-100" style="background-color: #1e3a8a;">Submit</button>
  </form>
</div>

<script>
// When the document is ready, initialize and show the agreement modal
document.addEventListener('DOMContentLoaded', function () {
    var agreementModalEl = document.getElementById('agreementModal');
    var agreementModal = new bootstrap.Modal(agreementModalEl, { backdrop: 'static', keyboard: false });
    agreementModal.show();
});

function processAgreement() {
    let agreement = document.querySelector('input[name="agreement"]:checked');
    if (!agreement) {
        alert("Please select an option.");
        return;
    }
    // Use Bootstrap's modal methods to hide and dispose the modal
    var modalEl = document.getElementById('agreementModal');
    var modalInstance = bootstrap.Modal.getInstance(modalEl);
    if (!modalInstance) {
        modalInstance = new bootstrap.Modal(modalEl);
    }
    if (agreement.value === "yes") {
        modalInstance.hide();
        // Wait for the fade-out animation to complete before disposing
        setTimeout(function() {
            modalInstance.dispose();
            // Show the questionnaire form
            document.getElementById("questionnaire").style.display = "block";
        }, 500);
    } else {
        modalInstance.hide();
        // Show the disagree modal using custom CSS (or you can similarly use Bootstrap)
        document.getElementById("disagreeModal").style.display = "flex";
    }
}

function handleDisagreeChoice() {
    let disagree = document.querySelector('input[name="disagreeConfirm"]:checked');
    if (!disagree) {
        alert("Please select an option.");
        return;
    }
    if (disagree.value === "yes") {
        window.location.href = "../index.php";
    } else {
        document.getElementById("disagreeModal").style.display = "none";
        // Re-show the agreement modal if needed (or you can reinitialize it)
        document.getElementById("agreementModal").style.display = "flex";
    }
}
</script>

</body>
</html>
