<?php
session_start();
require_once realpath("../../db_connect.php");

$page_title = "Trail Features";
$stylesheet = "../assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");
?>
<div class="features">
    <h1>Features</h1>
    <h2>Trails Collection</h2>

    <?php
    // Write the query to select all features with collection name
    $sql = "SELECT f.*, c.name AS collection_name
FROM features f
INNER JOIN collections c ON f.collections_id = c.id;";

    $result = mysqli_query($mysqli, $sql);

    // Check for errors
    if (!$result) {
        echo "Error: " . mysqli_error($mysqli);
        exit;
    }

    // Check if any features exist
    if (mysqli_num_rows($result) === 0) {
        echo "<p>There are currently no features listed.</p>";
    } else {

        // Loop through results and display each feature details
        while ($feature = mysqli_fetch_assoc($result)) {
            $feature_id = $feature['id'];
            $name = ucfirst(strtolower(str_replace('_', ' ', $feature['name'])));
            $collection_name = ucfirst(strtolower(str_replace('_', ' ', $feature['collection_name'])));
            $geometry_type = $feature['geometry_type'];
            $geometry_string = ""; // Initialize empty string
    
            // Handle geometry data based on type (similar logic to previous code)
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
                        $geometry_string = "$first_longitude, $first_latitude";

                        // Now you can access the segments array for further processing
                        // Example: print_r($segments); // Prints all segments
                    }
                    break;



                default:
                    $geometry_string = "NA";
                    break;
            }

            // Formatted feature details
            echo "<div class='feature-item'>";
            echo "  <p>Id:" . $feature_id . "</p>";
            echo "  <p>Collection:" . $collection_name . "</p>";
            echo "  <p>" . $name . "</p>";
            $link_url = "./topo_map.php?name=" . $feature['name'] . "&lat=" . $first_latitude . "&long=" . $first_longitude . "&zoom=13";
            if (!empty($source)) {
                $link_url .= "&source=" . $source;
            }
            echo "<p><span>Location:</span> <a href='$link_url'>" . $geometry_string . "</a></p>";
            echo '<div class="nav"><a href="./add_report.php?id=' . $feature_id . '">Submit a Report</a></div>';

            echo '<div class="nav"><a href="./display_reports.php?filter-by-trail=' . $feature_id . '">View Reports</a></div>';
            echo "</div>";
        }
    }
    echo "</div>";
    mysqli_close($mysqli);


    include_once realpath("../components/tail.inc");