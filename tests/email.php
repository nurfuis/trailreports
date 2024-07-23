<?php

$to = "cramer.ross@gmail.com";
$subject = "Test Email from Server";

$message = "This is a test email to verify if email sending works on the server.";

// Try sending the email
$success = mail($to, $subject, $message);

if ($success) {
  echo "Email sent successfully!";
} else {
  echo "Error: Email could not be sent.";
  // For debugging, uncomment the following line to see any potential errors
  echo "Error message: " . error_get_last()['message'];
}

