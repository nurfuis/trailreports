<?php

$page_title = "Trail Reports";
$page_css = "/assets/css/style.css";

session_start();

include ("/components/head.inc");
include ("/layouts/secondary.inc");

include_once ("../../db_connect.php");

// Write your query here
$sql = "";

$result = mysqli_query($mysqli, $sql);

// Check for errors
if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

// Process results
echo "<table><tr><th></th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr><td></td></tr>";
}
echo "</table>";

mysqli_close($mysqli);

include ("/components/tail.inc");
