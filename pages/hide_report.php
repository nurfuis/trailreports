<?php
session_start();

// Include database connection and functions (replace with your paths)
require_once realpath("../../config.php");
require_once realpath("../../db_connect.php");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    // Redirect if user is not admin
    header("Location: display_reports.php");
    exit;
}

$reportId = isset($_POST['report_id']) ? intval($_POST['report_id']) : 0;

if (!$reportId) {
    // Handle invalid report ID (e.g., display an error message)
    die("Invalid report ID");
}

$sql = "UPDATE trail_reports SET active = 0 WHERE id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $reportId);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
    header("Location: hidden_reports.php");
    exit;
} else {
    echo "Error hiding report: " . mysqli_error($mysqli);
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
}