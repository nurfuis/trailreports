<?php
require_once realpath("../../db_connect.php");

$page_title = "Login";
$stylesheet = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");


if (isset($_GET['token'])) {

  $token = $_GET['token'];

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

    if ($row['account_status'] === 'inactive') {
      $sql = "UPDATE users SET account_status = 'active' WHERE user_id = ?";
      $stmt = mysqli_prepare($mysqli, $sql);
      mysqli_stmt_bind_param($stmt, "i", $user_id);
      mysqli_stmt_execute($stmt);
    }

    // **Log in the user:**
    session_start();
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;

    $sql = "SELECT * FROM authorized_users WHERE user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);  // Prepare statement for security
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);  // Bind user ID
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
      $_SESSION['user_level'] = 'admin';
    } else {
      $_SESSION['user_level'] = 'user';
    }

    $sql = "UPDATE users SET login_token = NULL, login_token_expiry = NULL, email_login_attempts = 0 WHERE user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $successMessage = "Welcome back, " . $_SESSION['username'] . "!";
    if ($row['account_status'] === 'inactive') {
      $successMessage .= " Your account has been activated.";
    }

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
  ?>
    <script type="text/javascript">
      window.location.href = "/home.php" 
    </script>

    <?php
    echo "<p style='color: blue;'>$successMessage</p>";
}

include_once realpath("../components/tail.inc");

