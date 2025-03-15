<?php
$api_url = "https://your-clinic-system.com/admission.php"; // Replace with actual API URL

$data = [
    "student_number" => "20241234",
    "fullname" => "John Doe",
    "contact" => "09123456789",
    "s_gender" => "Male",
    "age" => 18,
    "year_level" => "1st_year"
];

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>



<?php
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "bcp_sms3_cms");

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Accept only POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate required fields
    if (!isset($data["student_number"], $data["fullname"], $data["contact"], $data["s_gender"], $data["age"], $data["year_level"])) {
        echo json_encode(["error" => "Missing required fields"]);
        exit;
    }

    // Sanitize input
    $student_number = $conn->real_escape_string($data["student_number"]);
    $fullname = $conn->real_escape_string($data["fullname"]);
    $contact = $conn->real_escape_string($data["contact"]);
    $s_gender = $conn->real_escape_string($data["s_gender"]);
    $age = (int) $data["age"];
    $year_level = $conn->real_escape_string($data["year_level"]);

    // Insert into database
    $query = "INSERT INTO students (student_number, fullname, contact, s_gender, age, year_level) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $student_number, $fullname, $contact, $s_gender, $age, $year_level);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Student admission received"]);
    } else {
        echo json_encode(["error" => "Failed to save student data"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
