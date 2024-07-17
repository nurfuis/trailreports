<?php

require_once realpath("./db_connect.php");

if ($mysqli->connect_error) {
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

// Prepare the insert statement
$sql = "INSERT INTO trail_reports (feature_id, user_id, rating, summary, created_at, title) 
        VALUES (:feature_id, :user_id, :rating, :summary, NOW(), :title)";

$stmt = $mysqli->prepare($sql);

// Loop and insert data
for ($i = 0; $i < $numReports; $i++) {
    $featureId = rand(1, 10); // Replace with logic to choose a valid feature ID
    $userId = rand(1, 20); // Replace with logic to choose a valid user ID
    $rating = rand(1, 5); // Generate random rating between 1 and 5
    $summary = generateLoremIpsum(); // Generate lorem ipsum summary
    $title = "Report " . ($i + 1); // Set a basic title

    // Bind parameters to the statement
    $stmt->bindParam(':feature_id', $featureId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':summary', $summary);
    $stmt->bindParam(':title', $title);

    // Execute the insert query
    $stmt->execute();
}

// Close the connection
$mysqli = null;

echo "Successfully inserted $numReports trail reports.";

?>