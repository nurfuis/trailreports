<?php

$page_title = "Login";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/single.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Sanitize user input to prevent SQL injection
  $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));
  $password = mysqli_real_escape_string($mysqli, trim($_POST['password']));

  // Prepare SQL statement to check user credentials
  $sql = "SELECT user_id, username, password_hash, account_status FROM users WHERE username = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $username);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  $num_rows = $result->num_rows; // Store the number of rows

  if ($num_rows === 1) {
    // Username exists (handle multiple rows if necessary)
    $row = mysqli_fetch_assoc($result);

    // Verify password using password_verify function
    if (password_verify($password, $row['password_hash'])) {
      // Login successful - Start session and store user data
      session_start();
      $_SESSION['username'] = $row['username'];
      $_SESSION['user_id'] = $row['user_id'];

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
      include ("../components/sign_in_form.inc");
    }
  } else {
    $errorMessage = "Invalid username or password.";
    include ("../components/sign_in_form.inc");
  }

  mysqli_free_result($result);
  mysqli_stmt_close($stmt);
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
} else if (!empty($successMessage)) {
  echo "<p style='color: blue;'>$successMessage</p>";
  // Redirect to homepage or profile page after successful sign_in (optional)
  // header("Location: /index.php");
}

include ("../components/tail.inc"); // closing tags for layout div, body, and html 
