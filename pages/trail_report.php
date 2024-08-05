<?php
session_start();
require_once realpath("../../db_connect.php");
require_once realpath("../components/user_roles.php");

$previousPage = $_SERVER['HTTP_REFERER'];
$page_title = "Trail Report";
$stylesheet = "../assets/css/style.css";

$reportId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT tr.*, f.name AS feature_name, 
       COALESCE(tr.title, 'Untitled') AS report_title, u.username 
       FROM trail_reports tr 
       INNER JOIN features f ON tr.feature_id = f.id 
       INNER JOIN users u ON tr.user_id = u.user_id 
       WHERE tr.id = $reportId AND active = 1;";

$result = $mysqli->query($sql);
if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

if ($result->num_rows === 1) {
    $report = $result->fetch_assoc();
} else {
    die("Report not found.");
}
mysqli_close($mysqli);

if (isset($report)) {
    //$page_title = "Trail Report";
}

include_once realpath("../components/head.inc");
include_once realpath("../layouts/bud.inc");

if (isset($report)) {
    include_once realpath("../components/single_report.inc");
} else {
    echo "<p>Report not found.</p>";
}

include_once realpath("../components/tail.inc");
