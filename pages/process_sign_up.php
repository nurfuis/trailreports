<?php

$page_title = "Register";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/single.inc"); // An open div with layout class

include_once ("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize user input to prevent SQL injection
    $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));
    $password = mysqli_real_escape_string($mysqli, trim($_POST['password']));

    // Check username availability
    $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['COUNT(*)'] > 0) {
        $errorMessage = "Username already exists. Please choose another.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";

        $stmt = mysqli_prepare($mysqli, $sql);

        mysqli_stmt_bind_param($stmt, "ss", $username, $passwordHash);

        if (mysqli_stmt_execute($stmt)) {
            $sql = "SELECT user_id FROM users WHERE username = ?";
            $stmt = mysqli_prepare($mysqli, $sql);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            $userId = $row['user_id'];

            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $userId;
            $successMessage = "Registration successful! Welcome " . $_SESSION['username'] . ".";
        } else {
            $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
        }
    }

    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
    echo "<p style='color: red;'>$errorMessage</p>";
    include ("../components/sign_up_form.inc");

} else if (!empty($successMessage)) {
    echo "<p style='color: blue;'>$successMessage</p>";
    header("Location: /pages/email.php");
}


include ("../components/tail.inc");