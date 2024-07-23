<?php

$tokenBytes = random_bytes(16);
if ($tokenBytes !== false) {
  $token = bin2hex($tokenBytes);
  // Use the token here...
} else {
  // Handle the error: log it or display a user-friendly message
  error_log("Failed to generate random bytes for token");
}