<?php

$page_title = "Login by email";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/single.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize user input to prevent SQL injection
    $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));

    // Check if email exists
    $sql = "SELECT user_id, email FROM users WHERE email = ? AND verified = 1";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $num_rows = $result->num_rows;

    if ($num_rows === 1) {

        $row = mysqli_fetch_assoc($result);
        $user_id = $row['user_id'];

        // Generate a unique login token and expiry time
        $token = bin2hex(random_bytes(16));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update user record with login token and expiry
        $sql = "UPDATE users SET login_token = ?, login_token_expiry = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $user_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) === 1) {

            // Prepare email content (similar to verification email)
            $to = $email;
            $subject = "Login Link for " . $_SERVER['HTTP_HOST'];
            $message = "Click the link below to log in securely:\n";
            $login_link = "192.168.0.78/pages/login.php?token=" . $token; // Replace with your actual URL
            $message .= $login_link . "\n\n";
            $message .= "This link will expire in 1 hour. \n\n";
            $message .= "If you did not request a login link, please ignore this email. \n\n";

            // Use a library like PHPMailer or your server's mail function to send the email
            if (mail($to, $subject, $message)) {
                $successMessage = "A login link has been sent to your email address.";
            } else {
                $errorMessage = "Failed to send login link. Please try again.";
                // Log the error for troubleshooting
            }
        } else {
            $errorMessage = "Failed to generate login token.";
        }

    } else {
        $errorMessage = "Invalid email address.";
    }

    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
    echo "<p style='color: red;'>$errorMessage</p>";
} else if (!empty($successMessage)) {
    echo "<p style='color: blue;'>$successMessage</p>";
}

include ("../components/tail.inc"); // closing tags for layout div, body, and html 
