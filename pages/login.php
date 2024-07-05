<?php

$page_title = "Login";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/single.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

if (isset($_GET['token'])) {

  $token = $_GET['token'];

  // Check if token is valid (exists, not expired)
  $sql = "SELECT user_id, username, account_status FROM users WHERE login_token = ? AND login_token_expiry > NOW()";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $token);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);
  $num_rows = $result->num_rows;

  if ($num_rows === 1) {

    $row = mysqli_fetch_assoc($result);
    $user_id = $row['user_id'];
    $username = $row['username'];

    // **Log in the user:**
    session_start();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;

    // Optional: Delete the used login token for security
    $sql = "UPDATE users SET login_token = NULL, login_token_expiry = NULL WHERE user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $successMessage = "Login successful!";

  } else {
    $errorMessage = "Invalid or expired login link.";
  }

  mysqli_free_result($result);
  mysqli_stmt_close($stmt);
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
} else if (!empty($successMessage)) {
  echo "<p style='color: blue;'>$successMessage</p>";
  // Optional: Redirect to a specific page after successful login
  // header("Location: /index.php");
}

include ("../components/tail.inc"); // closing tags for layout div, body, and html 

