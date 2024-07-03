<?php

$page_title = "Register Email";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/secondary.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Sanitize user input to prevent SQL injection
  $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));

  // Basic email validation (check for @ and .)
  if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errorMessage = "Please enter a valid email address.";
    echo $errorMessage;
    include ("../components/register-email-form.inc"); // Include email form with error message
    exit; // Exit script after including form
  }

  // Check email availability
  echo "<p>Check email availability</p>";

  $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  if ($result) {
    echo "<p>1</p>";
  } else {
    echo "<p>0</p>";
  }
  $row = mysqli_fetch_assoc($result);

  if ($row['COUNT(*)'] > 0) {


    $errorMessage = "Email already exists. Please use a different email.";
    include ("../components/register-email-form.inc");
  } else {

    echo $_SESSION['username'];
    // Prepare SQL statement to insert user with email

    // $sql = "INSERT INTO users (email) VALUES (?)";

    // $stmt = mysqli_prepare($mysqli, $sql);
    // mysqli_stmt_bind_param($stmt, "s", $email);

    // if (mysqli_stmt_execute($stmt)) {
    //   $successMessage = "Registration successful! You can now log in.";
    // } else {
    //   $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
    // }

    // mysqli_stmt_close($stmt);
  }

  mysqli_free_result($result); // Free the result from the email check
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
} else {
  echo "<p style='color: blue;'>$successMessage</p>";
}

include ("../layouts/tail.inc"); // closing tags for layout div, body, and html
