<?php

$user_id = $_SESSION['user_id'];

include_once ("../../db_connect.php");
$sql = "SELECT email FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind the user ID
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$fetched_email = mysqli_fetch_assoc($result);
$email = $fetched_email['email'];
?>

<div>

  <h2>Account Management</h2>

  <p>
    Welcome,
    <?php echo $_SESSION['username']; ?>!
  </p>
  <ul>
    <li><?php echo $email ?></li>
  </ul>

  <ul class="account-management-list">
    <li><?php include ("../components/update_email_form.inc"); ?></li>
    <li><?php include ("../components/update_password_form.inc"); ?></li>
    <li><?php include ("../components/update_account_status.inc"); ?></li>
  </ul>
</div>
<?php
mysqli_free_result($result);
mysqli_stmt_close($stmt);
mysqli_close($mysqli);
?>