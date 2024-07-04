<?php

require_once ("../../db_connect.php"); // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));

  // Check if email exists in the database
  $sql = "SELECT user_id, email FROM users WHERE email = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if (mysqli_stmt_num_rows($stmt) === 1) {
    $row = mysqli_fetch_assoc($result);

    // Generate a unique random password reset token
    $token = bin2hex(random_bytes(16));

    // Set token expiry time (e.g., 1 hour)
    $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Update user record with token and expiry
    $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $row['user_id']);
    mysqli_stmt_execute($stmt);

    // Prepare email content
    $to = $email;
    $subject = "Password Reset Request for " . $_SERVER['HTTP_HOST'];
    $message = "You requested to change your password for your account on " . $_SERVER['HTTP_HOST'] . ".\n\n";
    $message .= "Click the following link to reset your password within 1 hour:\n";
    $reset_link = "192.168.0.78/pages/reset_password.php?token=" . $token;  // Replace with your website URL
    $message .= $reset_link . "\n\n";
    $message .= "If you did not request a password reset, please ignore this email.\n\n";

    // Send email using your preferred method (e.g., PHPMailer, built-in mail function)
    $success = mail($to, $subject, $message); // Replace with your email sending logic

    if ($success) {
      $successMessage = "A password reset link has been sent to your registered email address.";
    } else {
      $errorMessage = "Error sending password reset email. Please try again later.";
    }
  } else {
    $errorMessage = "The email address you entered is not associated with an account.";
  }

  mysqli_free_result($result);
  mysqli_stmt_close($stmt);
  mysqli_close($mysqli);
}

$page_title = "Change Password";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); 
include ("../layouts/secondary.inc"); 


if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
} else if (!empty($successMessage)) {
  echo "<p style='color: blue;'>$successMessage</p>";
}


include ("../layouts/tail.inc");
