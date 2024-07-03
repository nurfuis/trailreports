<?php

$page_title = "Register";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/secondary.inc"); // An open div with layout class

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
            // The user should be logged in upon successful registration
            // Get the user ID of the newly registered user
            $sql = "SELECT user_id FROM users WHERE username = ?";
            $stmt = mysqli_prepare($mysqli, $sql);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            $userId = $row['user_id'];
            // Start the session
            session_start();

            // Add username and user_id as session variables
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $userId;
            echo $_SESSION['user_id'];
            $successMessage = "Registration successful! Welcome " . $_SESSION['username'] . ".";
        } else {
            // Handle registration failure (e.g., duplicate username)
            $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
        }
    }

    mysqli_free_result($result); // Free the result from the username check
    mysqli_stmt_close($stmt); // Close the statement used for username check
}

mysqli_close($mysqli);

if (!empty($errorMessage)) {
    echo "<p style='color: red;'>$errorMessage</p>";
} else if (!empty($successMessage)) {
    echo "<p style='color: blue;'>$successMessage</p>";
    // Redirect to homepage or profile page after successful registration (optional)
    // header("Location: homepage.php");
}

include "../components/register_form.inc";

include ("../layouts/tail.inc"); // closing tags for layout div, body, and html ?>