<?php

$page_title = "Process Trail Report";
$stylesheet = "/assets/css/style.css";
$currentTime = time();
session_start();
include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");
require_once realpath("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['last_submitted_time'])) {
        $lastSubmittedTime = (int) $_SESSION['last_submitted_time'];

        $timeDifference = $currentTime - $lastSubmittedTime;
        if ($timeDifference < 15) {
            $errorMessage = "Please wait a moment before submitting again.";
            goto after_validation;
        }
    }

    $userId = (int) mysqli_real_escape_string($mysqli, trim($_POST['user_id'])); // Get user ID
    $featureId = (int) mysqli_real_escape_string($mysqli, trim($_POST['feature']));
    $rating = (int) mysqli_real_escape_string($mysqli, trim($_POST['rating']));
    $summary = trim($_POST['summary']);
    $title = mysqli_real_escape_string($mysqli, trim($_POST['title'])); // Get the title

    if (empty($featureId) || empty($rating) || empty($summary) || empty($title)) {
        $errorMessage = "Please fill out all required fields.";
    } else {

        $sql = "INSERT INTO trail_reports (feature_id, user_id, rating, summary, title, time_updated) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "iiiss", $featureId, $userId, $rating, $summary, $title); // Bind title parameter

        if (mysqli_stmt_execute($stmt)) {

            ?>
            <script type="text/javascript">
                window.location.href = "./display_reports.php?success=true" 
            </script>

            <?php
            $successMessage = "Trail report submitted successfully!";
            // header("Location: ./display_reports.php?success=true");

        } else {
            $errorMessage = "Error submitting report: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}
after_validation:

$_SESSION['last_submitted_time'] = $currentTime;

mysqli_close($mysqli);

?>
<main>

    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?= $errorMessage ?></p>
    <?php elseif (!empty($successMessage)): ?>
        <p style="color: green;"><?= $successMessage ?></p>
        <h2>Thank you for submitting your trail report!</h2>
    <?php endif; ?>

</main>

</body>

</html>