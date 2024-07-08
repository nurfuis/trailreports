<?php

$page_title = "Features";
$page_css = "../assets/css/style.css";

session_start();

include ("../components/head.inc");
include ("../layouts/details.inc");

include_once ("../../db_connect.php");

echo "<h2>Features</h2>";

// Check if form is submitted (using POST method)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize user input (adjust based on actual form data)
    $feature_name = htmlspecialchars($_POST['feature_name']);
    $geometry_type = $_POST['geometry_type'];  // Assuming dropdown selection
    $geometry = json_encode($_POST['geometry']); // Assuming geometry data comes from the form
    $properties = json_encode($_POST['properties']); // Assuming property data comes from the form
    $management_area_id = intval($_POST['management_area_id']);  // Assuming selection from dropdown
    $collections_id = intval($_POST['collections_id']);  // Assuming selection from dropdown

    // Optional: Image handling (if applicable)
    // ... (code to handle image upload and store path)

    // Check for duplicate feature name (assuming name is unique)
    $sql_check = "SELECT COUNT(*) FROM features WHERE feature_name = '$feature_name'";
    $result_check = mysqli_query($mysqli, $sql_check);

    if (mysqli_fetch_row($result_check)[0] > 0) {
        echo "Error: Feature name already exists. Please choose a unique name.";
    } else {
        // Build the INSERT query
        $sql = "INSERT INTO features (feature_name, geometry_type, properties, management_area_id, collections_id) 
              VALUES ('$feature_name', '$geometry_type', '$geometry', '$properties', $management_area_id, $collections_id)";

        $result = mysqli_query($mysqli, $sql);

        // Check for errors and provide feedback
        if ($result) {
            echo "Feature added successfully!";
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to current page
            exit;
        } else {
            echo "Error adding feature. Please try again.";
        }
    }
    mysqli_free_result($result_check);

}
echo "past post";
// Write the query to select all features
$sql = "SELECT id, feature_name, geometry_type, properties, management_area_id, collections_id FROM features";

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
echo "<th>Feature Name</th>";
echo "<th>Geometry Type</th>";
echo "<th>Management Area ID</th>";
echo "<th>Collection ID</th>";
echo "</tr>";

// Process results and display data in table rows
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['feature_name'] . "</td>";
    echo "<td>" . $row['geometry_type'] . "</td>";
    echo "<td>" . $row['management_area_id'] . "</td>";
    echo "<td>" . $row['collections_id'] . "</td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($mysqli);
include ("../components/add_feature_form.inc");  // Change filename to reflect new table name

include ("../components/tail.inc");

