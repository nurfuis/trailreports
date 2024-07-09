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

    // Convert name to sentence case and replace underscores with spaces
    $name = ucfirst(strtolower(str_replace('_', ' ', $row['name'])));

    // Convert collection name to sentence case and replace underscores with spaces (if applicable)
    $collection_name = ucfirst(strtolower(str_replace('_', ' ', $row['collection_name'])));

    echo "<td>" . $name . "</td>";
    function get_first_coord($geometry_string)
    {
        if (empty($geometry_string)) {
            return null; // Handle empty string
        }

        // Split by colon (assuming it separates name and coordinates)
        $parts = explode(':', $geometry_string, 2);
        if (count($parts) < 2) {
            return null; // Invalid format if no colon separator
        }

        // Extract the part potentially containing coordinates (after the colon)
        $coords_part = trim($parts[1]);

        // Check if coordinates are enclosed in square brackets
        if (strpos($coords_part, '[') === 0 && strrpos($coords_part, ']') === strlen($coords_part) - 1) {
            // Extract coordinates within brackets
            $coords_part = trim(substr($coords_part, 1, -1));

            // Assuming comma as delimiter within coordinates (adjust if different)
            $coords_array = explode(',', $coords_part);
            if (count($coords_array) > 0) {
                return trim($coords_array[0]); // Return first coordinate
            }
        }

        // Default return if parsing fails
        return null;
    }
    if (is_array($row['geometry'])) {
        $first_coord = $row['geometry'][0];
    } else {
        $first_coord = get_first_coord($row['geometry']);
    }
    echo "<td>" . $first_coord . "</td>";
    echo "<td>" . $collection_name . "</td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($mysqli);
include ("../components/add_feature_form.inc");

include ("../components/tail.inc");

