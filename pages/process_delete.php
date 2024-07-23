<?php

session_start();
require_once realpath("../../db_connect.php");

if (empty($_POST['report_id']) || !isset($_SESSION['user_id'])) {
  header("Location: ./user_reports.php?error=unauthorized");
  exit; // Terminate script execution
}

$reportId = (int) $_POST['report_id'];
$userId = (int) $_SESSION['user_id'];

$sql = "UPDATE trail_reports SET active = 0 WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "ii", $reportId, $userId);

if (mysqli_stmt_execute($stmt)) {
  header("Location: ./user_reports.php?success=deleted"); // Redirect with success message
} else {
  header("Location: ./user_reports.php?error=delete_failed"); // Redirect with error message
}

mysqli_stmt_close($stmt);
mysqli_close($mysqli);

?>