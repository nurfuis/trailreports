<?php

$page_title = "Email Verification";
$page_css = "/assets/css/style.css";

include ("../components/head.inc");
include ("../layouts/single.inc");

session_start();

require_once ("../../db_connect.php"); // Include database connection

if (isset($_GET['token'])) {

    $token = $_GET['token'];

    $sql = "SELECT user_id FROM users WHERE verification_token = ? AND verification_token_expiry > NOW()";

    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $num_rows = $result->num_rows;

    if ($num_rows === 1) {
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['user_id'];

        // Update user record to verified, clear token and expiry
        $sql = "UPDATE users SET verified = 1, verification_token = NULL, verification_token_expiry = NULL WHERE user_id = ?";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $successMessage = "Your email address has been verified successfully. You can now log in using your email address.";
        } else {
            $errorMessage = "Verification failed: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);

    } else {
        $errorMessage = "Invalid or expired verification link.";
    }

} else {
    $errorMessage = "Invalid verification link.";
}

mysqli_close($mysqli);

include ("../components/tail.inc");

