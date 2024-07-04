<?php

$page_title = "Reset Password";
$page_css = "/assets/css/style.css";

include ("../components/head.inc");
include ("../layouts/secondary.inc");

require_once ("../../db_connect.php"); // Include database connection

?>
<h2>Reset Password</h2>

<?php

if (isset($_GET['token'])) {
  $token = $_GET['token'];

  $sql = "SELECT user_id, reset_token_expiry FROM users WHERE reset_token = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $token);
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  $num_rows = $result->num_rows;  // Store the number of rows

  if ($num_rows === 1) {
    echo "1";
  }

} else {
  echo "<p style='color: red;'>Invalid password reset link.</p>";
  exit;
}

mysqli_close($mysqli);

include ("../layouts/tail.inc");
?>