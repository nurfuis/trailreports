<?php

$page_title = "Change Password";
$page_css = "/assets/css/style.css";

include ("../components/head.inc");
include ("../layouts/single.inc");

session_start();
session_destroy();

require_once ("../../db_connect.php"); // Include database connection

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

  $num_rows = $result->num_rows;  // Store the number of rows
  $row = mysqli_fetch_assoc($result);

  if ($num_rows === 1 && $row['reset_token_expiry'] > date("Y-m-d H:i:s")) {
    $user_id = $row['user_id'];
    include ('../components/new_password_form.inc');

  } else {
    echo "<p style='color: red;'>Invalid password reset link.</p>";
    exit;
  }

} else {
  echo "<p style='color: red;'>Invalid password reset link.</p>";
  exit;
}

mysqli_close($mysqli);

include ("../components/tail.inc");
?>