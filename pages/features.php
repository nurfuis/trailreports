<?php

$page_title = "Features";
$page_css = "../assets/css/style.css";

session_start();

include ("../components/head.inc");
include ("../layouts/wide.inc");

include_once ("../../db_connect.php");




echo "<h2>Features</h2>";

// Check if form is submitted (using POST method)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize user input
    $name = htmlspecialchars($_POST['name']);
    $geometry_type = htmlspecialchars($_POST['geometry_type']);
    $geometry = floatval($_POST['geometry']); // convert to float for geometry
    $properties = intval($_POST['properties']); // convert to integer
    $management_area_id = intval($_POST['management_area_id']); // convert to integer
    $collections_id = htmlspecialchars($_POST['collections_id']);

    // Optional: Image handling (if applicable)
    // ... (code to handle image upload and store path)

    // Check for duplicate trail name
    $sql_check = "SELECT COUNT(*) FROM features WHERE name = '$name'";
    $result_check = mysqli_query($mysqli, $sql_check);

    if (mysqli_fetch_row($result_check)[0] > 0) {
        echo "Error: Trail name already exists. Please choose a unique name.";
    } else {
        // Build the INSERT query
        $sql = "INSERT INTO features (name, geometry_type, geometry, properties, management_area_id, collections_id, image) 
            VALUES ('$name', '$geometry_type', '$geometry', '$properties', '$management_area_id', '$collections_id', '$image_path')"; // replace '$image_path' with actual path if applicable

        $result = mysqli_query($mysqli, $sql);

        // Check for errors and provide feedback
        if ($result) {
            echo "Trail added successfully!";
            header("Type: " . $_SERVER['PHP_SELF']); // Redirect to current page
            exit;
        } else {
            echo "Error adding report. Please try again.";
        }
    }
    mysqli_free_result($result_check);

}

// Write the query to select all features
$sql = "SELECT f.*, c.name AS collection_name
FROM features f
INNER JOIN collections c ON f.collections_id = c.id;";
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
echo "<th>Coords</th>";
echo "<th>Collection</th>";
// Add a header for image if you want to display it
// echo "<th>Image</th>";
echo "</tr>";

// Process results and display data in table rows
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['geometry'] . "</td>";
    echo "<td>" . $row['collections_id'] . "</td>";
    // Add code to display image if needed (replace 'image_path' with actual path)
    // echo "<td><img src='" . 'image_path' . $row['image'] . "' alt='" . $row['name'] . " image'></td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($mysqli);
include ("../components/add_feature_form.inc");

include ("../components/tail.inc");
