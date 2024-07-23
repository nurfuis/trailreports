<?php

$page_title = "Display Users";
$page_css = "../assets/css/style.css";
session_start();
require_once realpath("../components/is_admin.inc");

include_once realpath("../components/head.inc"); // Top section up to and including body tag
include_once realpath("../layouts/wide.inc"); // An open div with layout class
require_once realpath("../../db_connect.php"); // $msqli connection

$sql = "SELECT user_id, username, email, verified, account_status, login_attempts, last_login_attempt, email_login_attempts, last_email_login_attempt, registration_date 
        FROM users 
        ORDER BY registration_date ASC";

$result = mysqli_query($mysqli, $sql);
if (!$result) {
  echo "Error: " . mysqli_error($mysqli);
  exit;
}

echo "<table>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Verified</th><th>Registered</th><th>Login Attempts</th><th>Last Login Attempt</th><th>Email Attempts</th><th>Last Email Attempt</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
  echo "<tr>";
  echo "<td>" . $row["user_id"] . "</td>";
  echo "<td>" . $row["username"] . "</td>";
  echo "<td>" . $row["email"] . "</td>";
  echo "<td>" . ($row["verified"] ? "Yes" : "No") . "</td>"; // Convert verified to Yes/No
  echo "<td>" . $row["registration_date"] . "</td>";
  echo "<td>" . $row["login_attempts"] . "</td>";
  echo "<td>" . $row["last_login_attempt"] . "</td>";
  echo "<td>" . $row["email_login_attempts"] . "</td>"; // Display new values from added columns
  echo "<td>" . $row["last_email_login_attempt"] . "</td>"; // Display new values from added columns
  echo "</tr>";
}

echo "</table>";

mysqli_close($mysqli);

include_once ("../components/tail.inc"); // closing tags for layout div, body, and html
