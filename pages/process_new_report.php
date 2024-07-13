<?php

$page_title = "Process Trail Report";
$page_css = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/single.inc");

require_once realpath("../../db_connect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) mysqli_real_escape_string($mysqli, trim($_POST['user_id'])); // Get user ID
    $featureId = (int) mysqli_real_escape_string($mysqli, trim($_POST['feature']));
    $rating = (int) mysqli_real_escape_string($mysqli, trim($_POST['rating']));
    $summary = mysqli_real_escape_string($mysqli, trim($_POST['summary']));

    // Basic validation (You can improve this)
    if (empty($featureId) || empty($rating) || empty($summary)) {
        $errorMessage = "Please fill out all required fields.";
    } else {

        // Prepare and execute insert query
        $sql = "INSERT INTO trail_reports (feature_id, user_id, rating, summary) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($mysqli, $sql);
        mysqli_stmt_bind_param($stmt, "iiii", $featureId, $userId, $rating, $summary);

        if (mysqli_stmt_execute($stmt)) {
            $successMessage = "Trail report submitted successfully!";
        } else {
            $errorMessage = "Error submitting report: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($mysqli);

?>
<main>

    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?= $errorMessage ?></p>
    <?php elseif (!empty($successMessage)): ?>
        <p style="color: green;"><?= $successMessage ?></p>
    <?php endif; ?>

    <h2>Thank you for submitting your trail report!</h2>
</main>

</body>

</html>