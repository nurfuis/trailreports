<?php

session_start();

include_once ("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_password = mysqli_real_escape_string($mysqli, trim($_POST['new_password']));
    $user_id = mysqli_real_escape_string($mysqli, trim($_POST['user_id']));

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = ? WHERE user_id = ?";

    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $successMessage = "Password updated successfully.";
        header("Location: /pages/account.php");
    } else {
        $errorMessage = "Failed to update password: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($mysqli);

if (isset($errorMessage)) {
    echo '<p class="alert">' . $errorMessage . '</p>';
} else if (isset($successMessage)) {
    echo '<p style="color: blue;">' . $successMessage . '</p>';
}
