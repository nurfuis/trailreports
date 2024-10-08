<?php
session_start();
require_once realpath("../../db_connect.php");

$page_title = "Register";
$stylesheet = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = mysqli_real_escape_string($mysqli, trim($_POST['username']));

    if (preg_match('/^[a-zA-Z0-9]{5,}$/', $username)) {
        // username is good

    } else {
        $errorMessage = "Username must be valid characters, numerals, and over 5 characters long.";
        goto after_validation;
    }

    $password = mysqli_real_escape_string($mysqli, trim($_POST['password']));

    if (strlen($password) < 8 || (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\da-zA-Z])(?!.*\s).{8,}$/', $password) && strlen($password) < 15)) {
        $errorMessage = "Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, one number, and one special character. Or, it can be 15 characters or longer.";
        goto after_validation;
    }


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

            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $userId;
            $_SESSION['verified'] = false;
            $successMessage = "Registration successful! Welcome " . $_SESSION['username'] . ".";
        } else {
            $errorMessage = "Registration failed: " . mysqli_stmt_error($stmt);
        }
    }
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
}
after_validation:

mysqli_close($mysqli);

if (!empty($errorMessage)) {
    echo "<p style='color: red;'>$errorMessage</p>";
    include_once realpath("../components/sign_up_form.inc");
} else if (!empty($successMessage)) {
?>
    <script type="text/javascript">
        window.location.href = "/pages/email.php"
    </script>

<?php
    echo "<p style='color: blue;'>$successMessage</p>";
    echo '<p><a href="/pages/email.php">Register an Email.</p>';
    // header("Location: /pages/email.php");
    exit;
}

include_once realpath("../components/tail.inc");
