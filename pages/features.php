<?php

$page_title = "Features";
$page_css = "../assets/css/style.css";

session_start();

include ("../components/head.inc");
include ("../layouts/wide.inc");

include_once ("../../db_connect.php");

echo "<h2>Features</h2>";


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
echo "<th>ID</th>";

echo "<th>Trail Name</th>";
echo "<th>Coords</th>";
echo "<th>Shape</th>";
echo "<th>Collection</th>";
// Add a header for image if you want to display it
// echo "<th>Image</th>";
echo "</tr>";

// Process results and display data in table rows
while ($row = mysqli_fetch_assoc($result)) {

    // Convert name to sentence case and replace underscores with spaces
    $name = ucfirst(strtolower(str_replace('_', ' ', $row['name'])));

    // Convert collection name to sentence case and replace underscores with spaces (if applicable)
    $collection_name = ucfirst(strtolower(str_replace('_', ' ', $row['collection_name'])));

    $feature_id = $row['id'];
    $geometry_type = $row['geometry_type'];
    $geometry_string = "";

    switch ($geometry_type) {
        case 'Point':
            $sql = "SELECT ST_X(geometry) AS latitude, ST_Y(geometry) AS longitude
          FROM points 
          WHERE feature_id=$feature_id;";
            $points_result = mysqli_query($mysqli, $sql);

            if ($points_result) {
                $coords = mysqli_fetch_assoc($points_result);
                $latitude = $coords['latitude'];
                $longitude = $coords['longitude'];

                $geometry_string = $latitude . "," . $longitude;
            }
            break;

        case 'LineString':
            $geometry_string = "poopsnach";
            break;

        default:
            $geometry_string = "NA";
            break;
    }








    echo "<tr>";
    echo "<td>" . $feature_id . "</td>";

    echo "<td>" . $name . "</td>";
    echo "<td>" . $geometry_string . "</td>";
    echo "<td>" . $geometry_type . "</td>";

    echo "<td>" . $collection_name . "</td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($mysqli);
include ("../components/add_feature_form.inc");

include ("../components/tail.inc");

