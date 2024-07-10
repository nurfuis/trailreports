<?php

$page_title = "Login";
$page_css = "/assets/css/style.css";

include realpath("../components/head.inc");
include realpath("../layouts/single.inc");

require_once realpath("../../db_connect.php");

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
    // Username exists (handle multiple rows if necessary)
    $row = mysqli_fetch_assoc($result);

    $sql_login = "UPDATE users SET last_login_attempt = NOW() WHERE user_id = ?";
    $stmt_login = mysqli_prepare($mysqli, $sql_login);
    mysqli_stmt_bind_param($stmt_login, "i", $row['user_id']);
    mysqli_stmt_execute($stmt_login);
    mysqli_stmt_close($stmt_login);

    // Verify password using password_verify function
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
        $successMessage = "Welcome back, " . $_SESSION['username'] . "!";
      }
    } else {
      $errorMessage = "Invalid username or password.";
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
