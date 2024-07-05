<?php

$page_title = "Register Email";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/single.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Sanitize user input to prevent SQL injection
  $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));

  // Basic email validation (check for @ and .)
  if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $errorMessage = "Please enter a valid email address.";
    echo '<p class="alert">' . $errorMessage . '</p>';
    include ("../components/update_email_form.inc"); // Include email form with error message
    exit; // Exit script after including form
  }

  // Check email availability
  $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  $row = mysqli_fetch_assoc($result);

  if ($row['COUNT(*)'] > 0) {
    $errorMessage = "Email already exists. Please use a different email.";
    include ("../components/update_email_form.inc");

  } else {
    session_start();

    // Use user_id from session variable
    $user_id = $_SESSION['user_id'];
    // Generate a unique random email verification token
    $token = bin2hex(random_bytes(16));
    // Set token expiry time (e.g., 24 hours from now)
    $expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));

    // Prepare SQL statement to update user email and verification data
    $sql = "UPDATE users SET pending_email = ?, verification_token = ?, verification_token_expiry = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "ssss", $email, $token, $expiry, $user_id);

    if (mysqli_stmt_execute($stmt)) {
      $successMessage = "Email registration is pending. Please verify your email address by clicking the link in the confirmation email we sent you. Once verified, you will be able to post and edit trail reports.";

      // Prepare email content
      $to = $email;
      $subject = "Email Verification for Trail Reports Website";
      $message = "Thank you for registering on Trail Reports! \n\n";
      $message .= "Please click the following link to verify your email address and activate your account: \n";
      $verification_link = "192.168.0.78/pages/verify_email.php?token=" . $token; // Replace with your actual URL
      $message .= $verification_link . "\n\n";
      $message .= "This link will expire in 24 hours. \n\n";
      $message .= "If you did not register on Trail Reports, please ignore this email. \n\n";

      $success = mail($to, $subject, $message);

      if ($success) {
        // Optional: Redirect to a success page or display a success message here
        // header("Location: /pages/account.php"); // Assuming a success page
        $successMessage = "A verification email has been sent.";
      } else {
        $errorMessage = "Email was collected but the verification email could not be sent. Please try again later.";
      }
    } else {
      $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
  }



  mysqli_free_result($result); // Free the result from the email check
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
  echo "<p style='color: red;'>$errorMessage</p>";
} else {
  echo "<p style='color: blue;'>$successMessage</p>";
}

include ("../components/tail.inc"); // closing tags for layout div, body, and html
