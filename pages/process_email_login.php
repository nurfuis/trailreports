<?php

$page_title = "Login by email";
$page_css = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

require_once realpath(".././config.php");
require_once realpath("../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));

    $sql = "SELECT user_id, email, email_login_attempts, last_email_login_attempt FROM users WHERE email = ? AND verified = 1";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $num_rows = $result->num_rows;

    if ($num_rows === 1) {
        $row = mysqli_fetch_assoc($result);

        $attempts = $row['email_login_attempts'];
        $lastAttempt = strtotime($row['last_email_login_attempt']);
        $currentTime = time();
        $threshold = LOGIN_ATTEMPTS;
        $lockoutTime = LOCKOUT_TIME;

        if ($attempts >= $threshold && ($currentTime - $lastAttempt) < $lockoutTime) {
            // account is locked
            $errorMessage = "Your account has been temporarily locked due to multiple failed login attempts. Please try again later.";

        } else {
            // account is not locked
            $user_id = $row['user_id'];

            $token = bin2hex(random_bytes(16));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Update user record with login token and expiry
            $sql = "UPDATE users SET email_login_attempts = email_login_attempts + 1, last_email_login_attempt = NOW(), login_token = ?, login_token_expiry = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($mysqli, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $token, $expiry, $user_id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 1) {

                $to = $email;
                $subject = "Login Link for " . $_SERVER['HTTP_HOST'];
                $message = "Click the link below to log in securely:\n";
                $login_link = HOST_ADDRESS . "/pages/login.php?token=" . $token;
                $message .= $login_link . "\n\n";
                $message .= "This link will expire in 1 hour. \n\n";
                $message .= "If you did not request a login link, please ignore this email. \n\n";

                if (mail($to, $subject, $message)) {
                    $successMessage = "A login link has been sent to your email address.";
                } else {
                    $errorMessage = "Failed to send login link. Please try again.";
                }
            } else {
                $errorMessage = "Failed to generate login token.";
            }
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
    include_once realpath("../components/sign_in_form.inc");

} else if (!empty($successMessage)) {
    echo "<p style='color: blue;'>$successMessage</p>";
}

include_once realpath("../components/tail.inc"); // closing tags for layout div, body, and html 
