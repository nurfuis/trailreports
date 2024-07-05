<?php

$user_id = $_SESSION['user_id'];

include_once ("../../db_connect.php");
$sql = "SELECT email, verified, verification_token_expiry, registration_date, pending_email FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind the user ID
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$fetched = mysqli_fetch_assoc($result);

$email = $fetched['email'];
$verified = $fetched['verified'];
$verification_token_expiry = $fetched['verification_token_expiry'];
$registration = $fetched['registration_date'];
$pending_email = $fetched['pending_email']; // Get the pending_email

$currentTime = strtotime(date('Y-m-d H:i:s')); // Current time as timestamp

$errorMessage = "";
$successMessage = "";

if (isset($_POST['resend_verification'])) {
  // User clicked "Resend Verification Email" button
  if ($verified === 0 && !empty($pending_email)) {
    // Generate a unique random email verification token
    $token = bin2hex(random_bytes(16));

    // Set token expiry time (e.g., 24 hours from now)
    $expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));

    // Prepare SQL statement to update verification data
    $sql = "UPDATE users SET verification_token = ?, verification_token_expiry = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $user_id);

    if (mysqli_stmt_execute($stmt)) {
      // Prepare email content
      $to = $pending_email;
      $subject = "Email Verification for Trail Reports Website";
      $message = "Thank you for registering on Trail Reports! \n\n";
      $message .= "Please click the following link to verify your email address and activate your account: \n";
      $verification_link = "192.168.0.78/pages/verify_email.php?token=" . $token; // Replace with your actual URL
      $message .= $verification_link . "\n\n";
      $message .= "This link will expire in 24 hours. \n\n";
      $message .= "If you did not register on Trail Reports, please ignore this email. \n\n";

      $success = mail($to, $subject, $message);

      if ($success) {
        $successMessage = "A verification email has been sent to your pending email address.";
      } else {
        $errorMessage = "Email was not sent. Please try again later.";
      }
    } else {
      $errorMessage = "Verification data could not be updated. Please try again later.";
    }

    mysqli_stmt_close($stmt);
  } else {
    $errorMessage = "Verification email cannot be resent at this time.";
  }
}

mysqli_free_result($result);
mysqli_stmt_close($stmt);
mysqli_close($mysqli);

?>

<div>

  <h2>Account Management</h2>

  <ul class="account-management-list--compact">
    <li>
      Username: <span><?php echo $_SESSION['username']; ?></span>
    </li>
    <li>
      Email: <span><?php echo $email ?></span>
    </li>
    <li>
      Verified: <span>
        <?php
        $verifiedText = ($verified === 1) ? 'Verified' : 'Pending';
        echo $verifiedText;
        ?>

        <?php if ($verified === 0 && !empty($pending_email)): ?>
          <form method="post">
            <button type="submit" name="resend_verification">Resend Verification Email</button>
          </form>
        <?php endif; ?>
      </span>
    </li>
    <li>
      Registration date: <span><?php echo $registration ?></span>
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