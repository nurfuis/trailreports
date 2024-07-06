<?php

$page_title = "Trail Reports";
$page_css = "../assets/css/style.css";

session_start();

include ("../components/head.inc");
include ("../layouts/details.inc");

include_once ("../../db_connect.php");

// Check if form is submitted (using POST method)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize user input
    $trail_name = htmlspecialchars($_POST['trail_name']);
    $location = htmlspecialchars($_POST['location']);
    $distance = floatval($_POST['distance']); // convert to float for distance
    $elevation_gain = intval($_POST['elevation_gain']); // convert to integer
    $elevation_loss = intval($_POST['elevation_loss']); // convert to integer
    $description = htmlspecialchars($_POST['description']);

    // Optional: Image handling (if applicable)
    // ... (code to handle image upload and store path)

    // Build the INSERT query
    $sql = "INSERT INTO trails (trail_name, location, distance, elevation_gain, elevation_loss, description, image) 
            VALUES ('$trail_name', '$location', '$distance', '$elevation_gain', '$elevation_loss', '$description', '$image_path')"; // replace '$image_path' with actual path if applicable

    $result = mysqli_query($mysqli, $sql);

    // Check for errors and provide feedback
    if ($result) {
        echo "Trail report added successfully!";
    } else {
        echo "Error adding report. Please try again.";
    }
}

// Write the query to select all trail reports
$sql = "SELECT * FROM trails";

$result = mysqli_query($mysqli, $sql);

// Check for errors
if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

// Start the HTML table
echo "<table>";

// Create table headers
echo "<tr>";
echo "<th>Trail Name</th>";
echo "<th>Location</th>";
echo "<th>Distance (miles)</th>";
echo "<th>Elevation Gain (ft)</th>";
echo "<th>Elevation Loss (ft)</th>";
echo "<th>Description</th>";
// Add a header for image if you want to display it
// echo "<th>Image</th>";
echo "</tr>";

// Process results and display data in table rows
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['trail_name'] . "</td>";
    echo "<td>" . $row['location'] . "</td>";
    echo "<td>" . $row['distance'] . "</td>";
    echo "<td>" . $row['elevation_gain'] . "</td>";
    echo "<td>" . $row['elevation_loss'] . "</td>";
    echo "<td>" . $row['description'] . "</td>";
    // Add code to display image if needed (replace 'image_path' with actual path)
    // echo "<td><img src='" . 'image_path' . $row['image'] . "' alt='" . $row['trail_name'] . " image'></td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($mysqli);
include ("../components/add_trail_form.inc");

include ("../components/tail.inc");
