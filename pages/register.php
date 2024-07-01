<?php

$page_title = "Register";
$page_css = "/assets/css/style.css";

include_once ("../../db_connect.php"); // $msqli connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize user input to prevent SQL injection
    $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));
    $password = mysqli_real_escape_string($mysqli, trim($_POST['password']));

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
        echo "Registration successful!";
    } else {
        // Handle registration failure (e.g., duplicate username)
        $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
    }
}
$query = "SELECT * FROM users";
$result = mysqli_query($mysqli, $query);

if ($result) {
    $users = mysqli_fetch_all($result);

    foreach ($users as $user) {
        echo '<h6>Username: </h6>', $user[1], ' <h6>Registration date: </h6>', $user[3];
    }

    mysqli_free_result($result);
}
mysqli_close($mysqli);

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/secondary.inc"); // An open div with layout class

if (!empty($errorMessage)) {
    echo "<p style='color: red;'>$errorMessage</p>";
}

include ("../layouts/tail.inc"); // closing tags for layout div, body, and html
