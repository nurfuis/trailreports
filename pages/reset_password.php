<h2>Reset Password</h2>

<?php

// Check if a valid token is present in the URL
if (isset($_GET['token'])) {
  $token = $_GET['token'];
  echo 'valid'. $token .'';
  // Validate token and expiry time on server-side (process_reset_password.php)
} else {
  echo "<p style='color: red;'>Invalid password reset link.</p>";
  exit;
}
?>