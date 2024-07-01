<?php

$page_title = "Register";
$page_css = "/assets/css/style.css";

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
        // Username is available, proceed with registration... (existing code)
    }

    mysqli_free_result($result); // Free the result from the username check
    mysqli_stmt_close($stmt); // Close the statement used for username check

    // Hash the password before storing it in the database (recommended)
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement (prevents SQL injection)
    $sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)";

    // Prepare the statement
    $stmt = mysqli_prepare($mysqli, $sql);

    // Bind parameters to the statement
    mysqli_stmt_bind_param($stmt, "ss", $username, $passwordHash);

    // Execute the statement
    if (mysqli_stmt_execute($stmt)) {
        $successMessage = "Registration successful!";
    } else {
        // Handle registration failure (e.g., duplicate username)
        $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
    }
}

mysqli_close($mysqli);

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/secondary.inc"); // An open div with layout class

if (!empty($errorMessage)) {
    echo "<p style='color: red;'>$errorMessage</p>";
} else {
    echo "<p style='color: blue;'>$successMessage</p>";

}

include ("../layouts/tail.inc"); // closing tags for layout div, body, and html
