<?php
session_start();
require_once realpath("../../db_connect.php");

$page_title = "Process Edited Trail Report";
$stylesheet = "/assets/css/style.css";
$currentTime = time();

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['last_submitted_time'])) {
        $lastSubmittedTime = (int) $_SESSION['last_submitted_time'];

        $timeDifference = $currentTime - $lastSubmittedTime;
        if ($timeDifference < 15) {
            $errorMessage = "Please wait a moment before submitting again.";
            goto after_validation;
        }
    }

    $reportId = (int) mysqli_real_escape_string($mysqli, trim($_POST['report_id'])); // Get report ID for update
    $userId = (int) mysqli_real_escape_string($mysqli, trim($_POST['user_id'])); // Get user ID
    $featureId = (int) mysqli_real_escape_string($mysqli, trim($_POST['feature']));
    $rating = (int) mysqli_real_escape_string($mysqli, trim($_POST['rating']));
    $summary = trim($_POST['summary']);
    $title = mysqli_real_escape_string($mysqli, trim($_POST['title'])); // Get the title

    if (empty($reportId) || empty($featureId) || empty($rating) || empty($summary) || empty($title)) {
        $errorMessage = "Please fill out all required fields.";
    } else {
        $sql = "UPDATE trail_reports SET feature_id = ?, rating = ?, summary = ?, title = ?, time_updated = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "iissi", $featureId, $rating, $summary, $title, $reportId);

        if (mysqli_stmt_execute($stmt)) {
            ?>
            <script type="text/javascript">
                window.location.href = "./user_reports.php?success=true" 
            </script>

            <?php

            $successMessage = "Trail report updated successfully!";
            // header("Location: ./user_reports.php?success=true"); // Redirect to reports page

        } else {
            $errorMessage = "Error updating report: " . mysqli_stmt_error($stmt);
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
        <h2>Your report has been updated!</h2>
    <?php endif; ?>

</main>

</body>

</html>