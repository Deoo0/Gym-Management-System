<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gym_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get member ID from request
$memberId = $_GET['memberId']; // Get the member ID from the query parameter

// Prepare and execute SQL query to join 'registration_info' and 'members'
$sql = "
    SELECT registration_info.end_date, members.firstname, members.middlename, members.lastname
    FROM registration_info
    JOIN members ON registration_info.member_id = members.id
    WHERE registration_info.member_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $memberId);
$stmt->execute();
$result = $stmt->get_result();

// Check if a valid member was found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $expiryDate = $row['end_date'];
    $firstName = $row['firstname'];
    $middleName = $row['middlename'];
    $lastName = $row['lastname'];
    $fullName = "$firstName $middleName $lastName"; // Concatenate the name

    // Check if the membership is still valid
    if (strtotime($expiryDate) > time()) {
        echo json_encode([
            "valid" => true, 
            "expiry" => $expiryDate, 
            "name" => $fullName // Include member's full name
        ]);
    } else {
        echo json_encode([
            "valid" => false, 
            "message" => "Membership expired.",
            "name" => $fullName // Include member's full name even if expired
        ]);
    }
} else {
    echo json_encode([
        "valid" => false, 
        "message" => "Member not found."
    ]);
}

// Close connection
$conn->close();
?>