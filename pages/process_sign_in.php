<?php

$page_title = "Login";
$page_css = "/assets/css/style.css";

include realpath("../components/head.inc"); // Top section up to and including body tag
include realpath("../layouts/single.inc"); // An open div with layout class

require_once realpath("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));
  $password = mysqli_real_escape_string($mysqli, trim($_POST['password']));

  $sql = "SELECT user_id, username, password_hash, account_status FROM users WHERE username = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $username);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  $num_rows = $result->num_rows;

  if ($num_rows === 1) {
    $row = mysqli_fetch_assoc($result);

    if (password_verify($password, $row['password_hash'])) {
      $sql_login = "UPDATE users SET last_login_attempt = NOW() WHERE user_id = ?";
      $stmt_login = mysqli_prepare($mysqli, $sql_login);
      mysqli_stmt_bind_param($stmt_login, "i", $row['user_id']);
      mysqli_stmt_execute($stmt_login);
      mysqli_stmt_close($stmt_login);

      session_start();
      $_SESSION['username'] = $row['username'];
      $_SESSION['user_id'] = $row['user_id'];
      $_SESSION['authenticated'] = true;

      if ($row['account_status'] === 'inactive') {
        // Update account status to active
        $sql = "UPDATE users SET account_status = 'active' WHERE user_id = ?";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "i", $row['user_id']);
        mysqli_stmt_execute($stmt);

        $successMessage = "Welcome back, " . $_SESSION['username'] . "! Your account has been activated.";
      } else {
        $successMessage = "Welcome back, " . $_SESSION['username'] . "!";
      }
    } else {
      $errorMessage = "Invalid username or password.";
      $checkLoginAttemptsSql = "SELECT login_attempts, last_login_attempt FROM users WHERE user_id = ?";
      $checkLoginAttemptsStmt = mysqli_prepare($mysqli, $checkLoginAttemptsSql);
      mysqli_stmt_bind_param($checkLoginAttemptsStmt, "i", $row['user_id']);
      mysqli_stmt_execute($checkLoginAttemptsStmt);
      $checkLoginResult = mysqli_stmt_get_result($checkLoginAttemptsStmt);
      $checkLoginRow = mysqli_fetch_assoc($checkLoginResult);

      $attempts = $checkLoginRow['login_attempts'];
      $lastAttempt = strtotime($checkLoginRow['last_login_attempt']);
      $currentTime = time();
      $threshold = 5; // Maximum allowed attempts
      $lockoutTime = 3600; // Lockout duration in seconds (1 hour)

      if ($attempts >= $threshold && ($currentTime - $lastAttempt) < $lockoutTime) {
        $errorMessage = "Your account has been temporarily locked due to multiple failed login attempts. Please try again later.";
      } else {
        $errorMessage = "Invalid username or password.";
      }
      

      mysqli_free_result($checkLoginResult);
      mysqli_stmt_close($checkLoginAttemptsStmt);
    }
  } else {
    $errorMessage = "Invalid username or password.";
  }

  mysqli_free_result($result);
  mysqli_stmt_close($stmt);
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
  include realpath("../components/sign_in_form.inc");

} else if (!empty($successMessage)) {
  echo "<p style='color: blue;'>$successMessage</p>";
}

include realpath("../components/tail.inc"); // closing tags for layout div, body, and html 
