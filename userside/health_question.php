<?php 
session_start();
require_once "../connection.php"; // Ensure this file contains your database connection

// Redirect if user is not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'user') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Check if user has already completed the questionnaire
$query = "SELECT completed_questionnaire FROM bcp_sms3_users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($completed_questionnaire);
$stmt->fetch();
$stmt->close();

if ($completed_questionnaire) {
    header("Location: userside.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    function sanitize_input($data) {
        return htmlspecialchars(trim($data));
    }

    $allergies = sanitize_input($_POST['allergies'] ?? 'None');
    $medical_conditions = sanitize_input($_POST['medical_conditions'] ?? 'None');
    $medication = sanitize_input($_POST['medication'] ?? 'None');
    $hospitalization = sanitize_input($_POST['hospitalization'] ?? 'None');
    $symptoms = isset($_POST['symptoms']) ? implode(", ", $_POST['symptoms']) : 'None';
    $contact_with_sick = sanitize_input($_POST['contact_with_sick'] ?? 'No');
    $sleep_hours = sanitize_input($_POST['sleep_hours'] ?? 'Unknown');
    $physical_activity = sanitize_input($_POST['physical_activity'] ?? 'Unknown');
    $health_seminars = sanitize_input($_POST['health_seminars'] ?? 'No');

    // Insert responses into database
    $sql = "INSERT INTO bcp_sms3_health_questionnaire 
            (username, allergies, medical_conditions, medication, hospitalization, symptoms, contact_with_sick, sleep_hours, physical_activity, health_seminars) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssssss", 
            $username, $allergies, $medical_conditions, $medication, $hospitalization, $symptoms, 
            $contact_with_sick, $sleep_hours, $physical_activity, $health_seminars
        );

        if ($stmt->execute()) {
            // Mark questionnaire as completed
            $update_query = "UPDATE bcp_sms3_users SET completed_questionnaire = 1 WHERE username = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("s", $username);
            $update_stmt->execute();
            $update_stmt->close();

            echo "<script>alert('Thank you! Your health questionnaire has been submitted.'); window.location.href='userside.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error submitting form. Please try again.');</script>";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Questionnaire</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin-top: 50px;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
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
                <label class="form-label">How often are you exercise??</label>
                <select name="physical_activity" class="form-select" required>
                    <option value="">Select</option>
                    <option value="Never">Never</option>
                    <option value="Less than 2 times">Less than 2 times</option>
                    <option value="2 to 4 times">2 to 4 times</option>
                    <option value="4 and above">4 and above</option>
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

            <button type="submit" class="btn btn-success w-100">Submit</button>
        </form>
    </div>
</body>
</html>
