<?php

$page_title = "Display Users";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/secondary.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

// Write your query here
$sql = "SELECT username, registration_date FROM users ORDER BY registration_date ASC";

$result = mysqli_query($mysqli, $sql);

// Check for errors
if (!$result) {
  echo "Error: " . mysqli_error($mysqli);
  exit;
}

// Process results
echo "<table><tr><th>Users</th><th>Registered</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
  echo "<tr><td>" . $row["username"] . "</td><td>" . $row["registration_date"] . "</td></tr>";
}
echo "</table>";

mysqli_close($mysqli);

include ("../layouts/tail.inc"); // closing tags for layout div, body, and html
