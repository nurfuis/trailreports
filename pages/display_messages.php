<?php

$page_title = "Received Messages";
$stylesheet = "/assets/css/style.css";
session_start();

require_once realpath("../components/is_admin.inc");

require_once realpath("../../db_connect.php");

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

$sql = "SELECT id, email, message, ip, created_at FROM contact_messages ORDER BY created_at DESC"; // Order messages by creation date (DESC = descending)

$result = mysqli_query($mysqli, $sql);
if (!$result) {
  echo "Error: " . mysqli_error($mysqli);
  exit;
}

if (mysqli_num_rows($result) > 0) {
  echo "<h2>Received Messages</h2>";
  echo "<table>";
  echo "<tr><th>ID</th><th>Email</th><th>Message</th><th>IP</th><th>Received On</th></tr>";

  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["email"] . "</td>";
    echo "<td>" . $row["message"] . "</td>";
    echo "<td>" . $row["ip"] . "</td>";
    echo "<td>" . $row["created_at"] . "</td>";
    echo "</tr>";
  }

  echo "</table>";
} else {
  echo "<h2>No messages received yet!</h2>";
}

mysqli_close($mysqli);

include_once realpath("../components/tail.inc");

?>