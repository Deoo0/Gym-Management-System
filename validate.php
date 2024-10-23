<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_phase2_qr_db";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get member ID from request
$memberId = $_GET['memberId']; // Get the member ID from the query parameter

// Prepare and execute SQL query
$sql = "SELECT membership_expiry FROM gym_members WHERE qr_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $memberId);
$stmt->execute();
$result = $stmt->get_result();

// Check if a valid member was found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $expiryDate = $row['membership_expiry'];
    
    // Check if the membership is still valid
    if (strtotime($expiryDate) > time()) {
        echo json_encode(["valid" => true, "expiry" => $expiryDate]);
    } else {
        echo json_encode(["valid" => false, "message" => "Membership expired."]);
    }
} else {
    echo json_encode(["valid" => false, "message" => "Member not found."]);
}

// Close connection
$conn->close();
?>
