<?php

$page_title = "Register Email";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/single.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Sanitize user input to prevent SQL injection
  $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));

  // Basic email validation (check for @ and .)
  if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errorMessage = "Please enter a valid email address.";
    echo '<p class="alert">' . $errorMessage . '</p>';
    include ("../components/update_email_form.inc"); // Include email form with error message
    exit; // Exit script after including form
  }

  // Check email availability
  $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $row = mysqli_fetch_assoc($result);

  if ($row['COUNT(*)'] > 0) {
    $errorMessage = "Email already exists. Please use a different email.";
    include ("../components/update_email_form.inc");
  } else {
    session_start();

    // Use user_id from session variable
    $user_id = $_SESSION['user_id'];

    // Prepare SQL statement to update user email
    $sql = "UPDATE users SET email = ? WHERE user_id = ?";

    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "si", $email, $user_id);

    if (mysqli_stmt_execute($stmt)) {
      $successMessage = "Email was registered successfully. Once verified (check your inbox) you will be able to post and edit trail reports.";
    } else {
      $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
  }


  mysqli_free_result($result); // Free the result from the email check
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
} else {
  echo "<p style='color: blue;'>$successMessage</p>";
}

include ("../components/tail.inc"); // closing tags for layout div, body, and html
