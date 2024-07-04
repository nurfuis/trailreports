<?php

$user_id = $_SESSION['user_id'];

include_once ("../../db_connect.php");
$sql = "SELECT email, verified, registration_date FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind the user ID
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$fetched = mysqli_fetch_assoc($result);

$email = $fetched['email'];
$verified = $fetched['verified'];
$registration = $fetched['registration_date'];
?>

<div>

  <h2>Account Management</h2>


  <ul class="account-management-list--compact">
    <li>
      Username: <span class="k2d-regular"><?php echo $_SESSION['username']; ?></span>
    </li>
    <li>
      Email: <span class="k2d-regular"><?php echo $email ?></span>
    </li>
    <li>
      Verified status: <span class="k2d-regular"><?php echo $verified ?></span>
    </li>
    <li>
      Registration date: <span class="k2d-regular"><?php echo $registration ?></span>
    </li>

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