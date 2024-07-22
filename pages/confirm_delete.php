<?php

$page_title = "Confirm Report Deletion";
$page_css = "/assets/css/style.css";
session_start();
include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");
require_once realpath("../db_connect.php");

// Check if report ID is provided and user is logged in
if (empty($_GET['id']) || !isset($_SESSION['user_id'])) {
  header("Location: ./user_reports.php?error=unauthorized");
  exit; // Terminate script execution
}

$reportId = (int) $_GET['id'];
$userId = (int) $_SESSION['user_id'];

// Get report details (optional, for confirmation message)
$sql = "SELECT title FROM trail_reports WHERE id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $reportId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 1) {
  $row = mysqli_fetch_assoc($result);
  $reportTitle = $row['title'];
} else {
  // Report not found, redirect with error message
  header("Location: ./user_reports.php?error=report_not_found");
  exit; // Terminate script execution
}

mysqli_stmt_close($stmt);

?>

<main>

  <h2>Confirm Report Deletion</h2>
  <p>Are you sure you want to delete the report titled "<b><?php echo $reportTitle; ?></b>"?</p>

  <form action="./process_delete.php" method="post">
    <input type="hidden" name="report_id" value="<?php echo $reportId; ?>"> <button type="submit" class="btn btn-danger">Delete</button>
    <a href="./user_reports.php" class="btn-secondary">Cancel</a>
  </form>

</main>

</body>

</html>