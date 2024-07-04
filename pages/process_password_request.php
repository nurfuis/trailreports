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

    // Validate the submitted email
    $email = mysqli_real_escape_string($mysqli, trim($_POST['email']));
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errorMessage = "Please enter a valid email address.";
    }
    echo $user_id . ", " . $email;
    // Check if the entered email matches the logged-in user's email
    $sql = "SELECT COUNT(*) FROM users WHERE email = ? AND user_id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $num_rows = $result->num_rows;

    if ($num_rows === 1) {
        // Email matches logged-in user, proceed with generating token, etc.

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

        // Prepare email content (similar to previous code)

        $success = mail($to, $subject, $message);

        if ($success) {
            $successMessage = "A password reset link has been sent to your registered email address.";
        } else {
            $errorMessage = "Error sending password reset email. Please try again later.";
        }

    } else {
        $errorMessage = "The email address you entered is not associated with your account.";
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
