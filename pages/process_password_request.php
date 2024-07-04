<?php

$page_title = "Change Password";
$page_css = "/assets/css/style.css";

include ("../components/head.inc");
include ("../layouts/single.inc");

require_once ("../../db_connect.php"); // Include database connection

session_start();

// Include the database connection file
include_once ("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the logged-in user ID from the session variable
    $user_id = $_SESSION['user_id'];

    // Query to get the user's email address
    $sql = "SELECT email FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id); // Bind the user ID
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $fetched_email = mysqli_fetch_assoc($result);

    if ($fetched_email) {
        $email = $fetched_email['email'];

        // Generate a unique random password reset token
        $token = bin2hex(random_bytes(16));

        // Set token expiry time (e.g., 1 hour)
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Update user record with token and expiry
        $sql = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $user_id);
        mysqli_stmt_execute($stmt);

        // Prepare email content
        $to = $email;
        $subject = "Password Reset Request for " . $_SERVER['HTTP_HOST'];
        $message = "You requested to change your password for your account on " . $_SERVER['HTTP_HOST'] . ".\n\n";
        $message .= "Click the following link to reset your password within 1 hour:\n";
        $reset_link = $_SERVER['HTTP_HOST'] . "/pages/change_password.php?token=" . $token;
        $message .= $reset_link . "\n\n";

        $success = mail($to, $subject, $message);

        if ($success) {
            $successMessage = "A password reset link has been sent to your registered email address.";
        } else {
            $errorMessage = "Error sending password reset email. Please try again later.";
        }

    } else {
        $errorMessage = "There is not an email associated with this account.";
    }

    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
}

if (isset($errorMessage)) {
    echo '<p class="alert">' . $errorMessage . '</p>';
} else if (isset($successMessage)) {
    echo '<p style="color: blue;">' . $successMessage . '</p>';
}

include ("../components/tail.inc");
