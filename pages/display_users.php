<?php

$page_title = "Display Users";
$page_css = "/assets/css/style.css";

include ("/components/head.inc"); // Top section up to and including body tag
include ("/layouts/secondary.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

// Write your query here
$sql = "SELECT user_id, username, registration_date, email, verified, password_hash FROM users ORDER BY registration_date ASC";

$result = mysqli_query($mysqli, $sql);

// Check for errors
if (!$result) {
  echo "Error: " . mysqli_error($mysqli);
  exit;
}

// Process results
echo "<table><tr><th>ID</th><th>users</th><th>email</th><th>verified</th><th>registered</th><th>hash</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
  echo "<tr><td>" . $row["user_id"] . "</td><td>" . $row["username"] . "</td><td>" . $row["email"] . "</td><td>" . $row["verified"] . "</td><td>" . $row["registration_date"] . "</td><td>" . $row["password_hash"] . "</td></tr>";
}
echo "</table>";

mysqli_close($mysqli);

include ("/components/tail.inc"); // closing tags for layout div, body, and html
