<?php

session_start();

$user_id = $_SESSION['user_id'];

require_once realpath("../../db_connect.php");
$sql = "SELECT email, pending_email, verified, verification_token_expiry, registration_date FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind the user ID
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$fetched = mysqli_fetch_assoc($result);

$email = $fetched['email'];
$verified = $fetched['verified'];
$registration = $fetched['registration_date'];
$pending_email = $fetched['pending_email'];
?>

<div>

  <h2>Account Management</h2>


  <ul class="account-management-list--compact">
    <li>
      Username: <span><?php echo $_SESSION['username']; ?></span>
    </li>
    <li>
      Email: <span>
        <?php
        // Display "Incomplete" if both email and pending_email are null
        if (is_null($email) && is_null($pending_email)) {
          echo "Incomplete";
        } else {
          // Display pending_email if email is null, otherwise display email
          if (is_null($email)) {
            echo $pending_email;
          } else {
            echo $email;
          }
        }
        ?>
      </span>
    </li>
    <li>
      Verified: <span>
        <?php
        $verifiedText = ($verified === 1) ? 'Yes' : 'No';
        echo $verifiedText;
        ?>
        <?php if ($verified === 0 && !empty($pending_email)): ?>
          <form method="post" action="process_email.php">
            <input type="hidden" name="email" value="<?= $pending_email ?>">
            <button type="submit" name="resend_verification">Resend Verification Email</button </form>
          <?php endif; ?>
      </span>
    </li>
    <li>
      Registration date: <span><?php echo $registration ?></span>
    </li>
  </ul>

  <ul class="account-management-list">
    <li><?php include_once realpath("../components/update_email_form.inc"); ?></li>
    <li>
      <?php
      if ($verified === 1) {
        include_once realpath("../components/update_password_form.inc");
      } else {
        echo '<h3>Change Password</h3><p>Email verification required to change password.</p>';
      }
      ?>
    </li>
    <li><?php include_once realpath("../components/update_account_status.inc"); ?></li>
  </ul>
</div>
<?php
mysqli_free_result($result);
mysqli_stmt_close($stmt);
mysqli_close($mysqli);
?>