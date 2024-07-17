<?php

// Include your database connection file
require_once realpath("./db_connect.php");

// Check for connection error
if (!$conn) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Number of reports to insert (adjust as needed)
$numReports = 20;

// Lorem Ipsum generator function (replace with your preferred method)
function generateLoremIpsum($paragraphs = 2, $sentences = 3)
{
    $url = "https://loripsum.net/api/";
    $params = array(
        "paragraphs" => $paragraphs,
        "sentences" => $sentences,
    );
    $response = file_get_contents($url . http_build_query($params));
    return strip_tags($response);
}

// Prepare the insert statement with named parameters
$sql = "INSERT INTO trail_reports (feature_id, user_id, rating, summary, created_at, title) 
        VALUES (?, ?, ?, ?, NOW(), ?)";

$stmt = $conn->prepare($sql);

// Loop and insert data
for ($i = 0; $i < $numReports; $i++) {
    $featureId = rand(1, 10); // Replace with logic to choose a valid feature ID
    $userId = rand(1, 20); // Replace with logic to choose a valid user ID
    $rating = rand(1, 5); // Generate random rating between 1 and 5
    $summary = generateLoremIpsum(); // Generate lorem ipsum summary
    $title = "Report " . ($i + 1); // Set a basic title

    // Bind parameters to the statement
    $stmt->bindParam(1, $featureId, PDO::PARAM_INT);
    $stmt->bindParam(2, $userId, PDO::PARAM_INT);
    $stmt->bindParam(3, $rating, PDO::PARAM_INT);
    $stmt->bindParam(4, $summary, PDO::PARAM_STR);
    $stmt->bindParam(5, $title, PDO::PARAM_STR);

    // Execute the insert query
    $stmt->execute();
}

// Close the connection (assuming it's closed in db_connect.php)
// $conn = null; // If not closed in db_connect.php, uncomment this line

echo "Successfully inserted $numReports trail reports.";

?>