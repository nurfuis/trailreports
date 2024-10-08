<?php
session_start();
require_once realpath("../../db_connect.php");

$page_title = "Register Email";
$stylesheet = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));

  if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errorMessage = "Please enter a valid email address.";
    echo '<p class="alert">' . $errorMessage . '</p>';
    include_once realpath("../components/update_email_form.inc");
    exit;
  }

  $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $row = mysqli_fetch_assoc($result);

  if ($row['COUNT(*)'] > 0) {
    $errorMessage = "Email already exists. Please use a different email.";

  } else {
    $user_id = $_SESSION['user_id'];
    $token = md5(uniqid(mt_rand(), true));
    $expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));
    $sql = "UPDATE users SET pending_email = ?, verification_token = ?, verification_token_expiry = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $email, $token, $expiry, $user_id);

    if (mysqli_stmt_execute($stmt)) {
      $successMessage = "Email registration is pending. Please verify your email address by clicking the link in the confirmation email we sent you. Once verified, you will be able to post and edit trail reports.";
      $to = $email;
      $subject = "Email Verification for Big Sur Trail Reports Website";
      $message = "Thank you for registering on Big Sur Trail Reports! \n\n";
      $message .= "Please click the following link to verify your email address and activate your account: \n";
      $verification_link = "bigsurtrailreports.net" . "/pages/verify_email.php?token=" . $token;
      $message .= $verification_link . "\n\n";
      $message .= "This link will expire in 24 hours. \n\n";
      $message .= "If you did not register on Big Sur Trail Reports, please ignore this email. \n\n";
      $headers = 'From: noreply@bigsurtrailreports.net' . "\r\n";
      $success = mail($to, $subject, $message, $headers);
      if ($success) {
        $successMessage = "A verification email has been sent.";
      } else {
        $errorMessage = "Email was collected but the verification email could not be sent. Please try again later.";
      }
    } else {
      $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
  }
  mysqli_free_result($result);
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
  echo '<p><a href="' . $_SESSION['referrer'] . '">Continue your report...</a></p>';

  include_once realpath("../components/update_email_form.inc");

} else {
  echo '<p style="color: blue;">' . $successMessage . '</p>';
  $referrer = $_SESSION['referrer'];
  echo '<p><a href="' . $referrer . '">Continue your report...</a></p>';
}

include_once realpath("../components/tail.inc");