<?php

$page_title = "Deactivate Account";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/secondary.inc"); // An open div with layout class

// Check if user is logged in (optional, based on your implementation)
if (!isset($_SESSION['user_id'])) {
  header("Location: /pages/login.php"); // Redirect to login page if not logged in
  exit;
}

include_once ("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['user_id']; // Get user ID from session

  $removal_date = date('Y-m-d', strtotime("+90 days")); // Calculate removal date 90 days from now

  // Deactivation logic with removal date
  $sql = "UPDATE users SET account_status = 'inactive', removal_date = ? WHERE user_id = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "si", $removal_date, $user_id);

  if (mysqli_stmt_execute($stmt)) {
    // Account deactivated successfully
    session_destroy(); // Destroy the session on successful deactivation
    $successMessage = "Your account has been deactivated. You have 90 days to log back in and reactivate it, otherwise it will be permanently deleted.";
  } else {
    // Handle deactivation failure
    $errorMessage = "Account deactivation failed. Please try again later.";
  }

  mysqli_stmt_close($stmt);
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
} else if (!empty($successMessage)) {
  echo "<p style='color: blue;'>$successMessage</p>";
}

include ("../layouts/tail.inc");
