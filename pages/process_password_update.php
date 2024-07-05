<?php

$page_title = "New Password";
$page_css = "/assets/css/style.css";

// Include components at the beginning for better organization
include ("../components/head.inc");
include ("../layouts/single.inc");

// Include database connection
include_once ("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_password = mysqli_real_escape_string($mysqli, trim($_POST['new_password']));
    $user_id = mysqli_real_escape_string($mysqli, trim($_POST['user_id']));

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Prepared statement for updating password and resetting tokens
    $sql = "UPDATE users 
          SET password_hash = ?, 
              reset_token = NULL, 
              reset_token_expiry = NULL 
          WHERE user_id = ?";

    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $hashed_password, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $successMessage = "Password updated successfully.";
        header("Location: /pages/account.php");

    } else {
        $errorMessage = "Failed to update password: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($mysqli); // Close database connection

if (isset($errorMessage)) {
    echo '<p class="alert">' . $errorMessage . '</p>';
} else if (isset($successMessage)) {
    echo '<p style="color: blue;">' . $successMessage . '</p>';
}

// Include tail component at the end
include ("../components/tail.inc");

?>