<?php
session_start();
if (empty($_POST['report_id']) || !isset($_SESSION['user_id'])) {
  header("Location: ./user_reports.php?error=unauthorized");
  exit; // Terminate script execution
}
require_once realpath("../../db_connect.php");

$reportId = (int) $_POST['report_id'];
$userId = (int) $_SESSION['user_id'];

$sql = "UPDATE trail_reports SET active = 0 WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "ii", $reportId, $userId);

if (mysqli_stmt_execute($stmt)) {
  ?>
  <script type="text/javascript">
    window.location.href = "./user_reports.php?success=deleted" 
  </script>

  <?php
  header("Location: ./user_reports.php?success=deleted");
} else {
  ?>
  <script type="text/javascript">
    window.location.href = "./user_reports.php?error=delete_failed" 
  </script>

  <?php
  header("Location: ./user_reports.php?error=delete_failed");
}

mysqli_stmt_close($stmt);
mysqli_close($mysqli);

?>