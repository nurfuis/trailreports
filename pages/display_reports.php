<?php

$page_title = "Trail Reports";
$page_css = "../assets/css/style.css";

session_start();

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

require_once realpath("../../db_connect.php");

echo "<h2>Trail Reports</h2>";

// Write the query to select all reports with user information
$sql = "SELECT tr.*, f.name AS feature_name, u.username
FROM trail_reports tr
INNER JOIN features f ON tr.feature_id = f.id
INNER JOIN users u ON tr.user_id = u.user_id;";

$result = mysqli_query($mysqli, $sql);

// Check for errors
if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

// Check if there are any reports
if (mysqli_num_rows($result) === 0) {
    echo "<p>There are currently no trail reports.</p>";
} else {

    // Start the HTML table
    echo "<table>";

    // Create table headers
    echo "<tr>";
    echo "<th>Submitted By</th>";
    echo "<th>Trail Name</th>";
    echo "<th>Rating</th>";
    echo "<th>Summary</th>";
    // Add a header for date submitted if needed
    echo "<th>Date Submitted</th>";
    echo "</tr>";

    // Process results and display data in table rows
    while ($row = mysqli_fetch_assoc($result)) {

        $username = $row['username'];
        $feature_name = $row['feature_name'];
        $rating = $row['rating'];
        $summary = $row['summary'];

        echo "<tr>";
        echo "<td>" . $username . "</td>";
        echo "<td>" . $feature_name . "</td>";
        echo "<td>" . $rating . "</td>";
        echo "<td>" . $summary . "</td>";
        // Add a table cell for date submitted if included
        echo "<td>" . date("Y-m-d", strtotime($row['created_at'])) . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

mysqli_close($mysqli);

include_once realpath("../components/tail.inc");

