<?php
$page_title = "New Password";
$page_css = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

require_once realpath("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $new_password = mysqli_real_escape_string($mysqli, trim($_POST['new_password']));
    $user_id = mysqli_real_escape_string($mysqli, trim($_POST['user_id']));

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL, login_attempts = 0 WHERE user_id = ?";

    $stmt = mysqli_prepare($mysqli, $sql);

    mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        ?>
        <script type="text/javascript">
            window.location.href = "/pages/account.php" 
        </script>

        <?php
        $successMessage = "Password updated successfully.";
        // header("Location: /pages/account.php");
    } else {
        $errorMessage = "Failed to update password: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($mysqli);

if (isset($errorMessage)) {
    echo '<p class="alert">' . $errorMessage . '</p>';
} else if (isset($successMessage)) {

    ?>
        <script type="text/javascript">
            window.location.href = "/home.php" 
        </script>

        <?php
        echo '<p style="color: blue;">' . $successMessage . '</p>';
}
include_once realpath("../components/tail.inc");