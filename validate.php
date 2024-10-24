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

/// Prepare and execute SQL query to find the most recent valid membership
$sql = "
SELECT registration_info.end_date, registration_info.date_created, registration_info.status, 
members.firstname, members.middlename, members.lastname
FROM registration_info
JOIN members ON registration_info.member_id = members.id
WHERE registration_info.member_id = ? 
AND registration_info.status = 1
AND registration_info.end_date > NOW()
ORDER BY registration_info.end_date DESC
LIMIT 1
";

$sql2 = "
SELECT registration_info.end_date, registration_info.date_created, registration_info.status, 
members.firstname, members.middlename, members.lastname
FROM registration_info
JOIN members ON registration_info.member_id = members.id
WHERE registration_info.member_id = ? 
AND registration_info.status = 1
AND registration_info.end_date < NOW()
ORDER BY registration_info.end_date DESC
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $memberId);  // Bind the memberId from the URL or request
$stmt->execute();
$result = $stmt->get_result();

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param("s", $memberId);  // Bind the memberId from the URL or request2
$stmt2->execute();
$result2 = $stmt2->get_result();

// Check if a valid membership was found
if ($result->num_rows > 0) {
$row = $result->fetch_assoc();
$expiryDate = $row['end_date'];
$firstName = $row['firstname'] ?? 'Unknown';
$middleName = $row['middlename'] ?? ''; // If no middle name, default to empty string
$lastName = $row['lastname'] ?? 'Unknown';
$fullName = "$firstName $middleName $lastName"; // Concatenate the name

// Return the valid membership
echo json_encode([
    "valid" => true, 
    "expiry" => $expiryDate, 
    "name" => $fullName
]);
} 
elseif ($result2->num_rows > 0){
$row = $result2->fetch_assoc();
$expiryDate = $row['end_date'];
$firstName = $row['firstname'] ?? 'Unknown';
$middleName = $row['middlename'] ?? ''; // If no middle name, default to empty string
$lastName = $row['lastname'] ?? 'Unknown';
$fullName = "$firstName $middleName $lastName"; // Concatenate the name
// Return the valid membership
echo json_encode([
    "valid" => false, 
    "expiry" => $expiryDate, 
    "name" => $fullName
    ]);
}else {
// No valid memberships found
echo json_encode([
    "valid" => false, 
    "message" => "No valid membership found."
]);
}

// Close connection
$conn->close();
?>