<?php

$page_title = "Change Password";
$page_css = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

session_start();
session_destroy();

require_once realpath("../db_connect.php");

$errorMessage = "";
$successMessage = "";

?>
<h2>Change Password</h2>

<?php

if (isset($_GET['token'])) {

  $token = $_GET['token'];

  $sql = "SELECT user_id, reset_token_expiry FROM users WHERE reset_token = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $token);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  $num_rows = $result->num_rows;
  $row = mysqli_fetch_assoc($result);

  if ($num_rows === 1 && $row['reset_token_expiry'] > date("Y-m-d H:i:s")) {
    $user_id = $row['user_id'];
    include_once ('../components/new_password_form.inc');

  } else {
    $errorMessage = "Invalid password reset link.";
  }

} else {
  $errorMessage = "Invalid password reset link.";
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo '<p class="alert">' . $errorMessage . '</p>';
} else if (!empty($successMessage)) {
  echo '<p style="color: blue;">' . $successMessage . '</p>';
}

include_once realpath("../components/tail.inc");
?>