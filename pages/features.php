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
            $sql = "SELECT ST_AsText(geometry) AS wkt_string
                         FROM polylines 
                         WHERE feature_id=$feature_id;";
            $polylines_result = mysqli_query($mysqli, $sql);

            if ($polylines_result) {
                $row = mysqli_fetch_assoc($polylines_result);
                $wkt_string = $row["wkt_string"];

                // Extract individual coordinates from the WKT string 
                $points = explode(",", substr($wkt_string, strpos($wkt_string, "(") + 1, -1));

                // Get the first coordinate pair
                $first_point = explode(" ", trim($points[0]));
                $first_longitude = $first_point[0];
                $first_latitude = $first_point[1];

                // Generate a unique ID
                $geometry_id = uniqid();

                // Encode segments as JSON
                $encoded_segments = json_encode($segments);

                // JavaScript function to open pop-up window (inline)
                $popup_function = <<<JS
        function openSegmentDetails(longitude, latitude) {
            window.open('segment_details.html?id=$geometry_id&segments=$encoded_segments', '_blank', 'width=400,height=300');
        }
    JS;
                $geometry_string = "$first_longitude, $first_latitude";

                // Initialize an empty array to store segments
                $segments = [];

                foreach ($points as $point) {
                    $point_data = explode(" ", trim($point));
                    $longitude = $point_data[0];
                    $latitude = $point_data[1];

                    // Create an array for each segment (longitude, latitude)
                    $segment = [$longitude, $latitude];
                    $segments[] = $segment; // Add the segment to the array
                }
                echo "<br>";
                echo "<a href='javascript:void(0)' onclick='openSegmentDetails($first_longitude, $first_latitude)'>Click for Segment Details</a>";
                // Now you can access the segments array for further processing
                // Example: print_r($segments); // Prints all segments
            }
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

