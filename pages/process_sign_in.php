<?php

$page_title = "Login";
$page_css = "/assets/css/style.css";

include realpath("../components/head.inc");
include realpath("../layouts/single.inc");

require_once realpath("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));
  $password = mysqli_real_escape_string($mysqli, trim($_POST['password']));

  $sql = "SELECT user_id, username, password_hash, account_status, login_attempts, last_login_attempt FROM users WHERE username = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $username);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $num_rows = $result->num_rows;
  $row = mysqli_fetch_assoc($result);

  if ($num_rows === 1) {
    // username exists
    $attempts = $row['login_attempts'];
    $lastAttempt = strtotime($row['last_login_attempt']);
    $currentTime = time();
    $threshold = 5;
    $lockoutTime = 3600;
    echo $attempts . ($currentTime - $lastAttempt) < $lockoutTime;

    if ($attempts >= $threshold && ($currentTime - $lastAttempt) < $lockoutTime) {
      // account is locked
      $errorMessage = "Your account has been temporarily locked due to multiple failed login attempts. Please try again later.";

    } else {
      // account is not locked
      // check login
      if (password_verify($password, $row['password_hash'])) {
        // Login successful - Start session and store user data
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
          // account is active
          $successMessage = "Welcome back, " . $_SESSION['username'] . "!";
        }
      } else {
        // Login failed - Update login attempts
        $sql_update_attempts = "UPDATE users SET login_attempts = login_attempts + 1, last_login_attempt = NOW() WHERE username = ?";
        $stmt_update_attempts = mysqli_prepare($mysqli, $sql_update_attempts);
        mysqli_stmt_bind_param($stmt_update_attempts, "s", $username);
        mysqli_stmt_execute($stmt_update_attempts);
        mysqli_stmt_close($stmt_update_attempts);

        $errorMessage = "Invalid username or password.";
      }
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

include realpath("../components/tail.inc");
