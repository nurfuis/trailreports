<?php

// Include your database connection file
require_once realpath("./db_connect.php");

// Check for connection error
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

// Loop and insert data
for ($i = 0; $i < $numReports; $i++) {
    $featureId = rand(1, 10); // Replace with logic to choose a valid feature ID
    $userId = rand(1, 5); // Replace with logic to choose a valid user ID
    $rating = rand(1, 5); // Generate random rating between 1 and 5
    $summary = generateLoremIpsum(); // Generate lorem ipsum summary
    $title = "Report " . ($i + 1); // Set a basic title

    // Construct the SQL query
    $sql = "INSERT INTO trail_reports (feature_id, user_id, rating, summary, created_at, title) 
            VALUES ($featureId, $userId, $rating, '$summary', NOW(), '$title')";

    // Execute the query
    if ($mysqli->query($sql) === TRUE) {
        echo "Report " . ($i + 1) . " inserted successfully.\n";
    } else {
        echo "Error inserting report: " . $mysqli->error . "\n";
    }
}

// Close the connection
$mysqli->close();

?>