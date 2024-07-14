<?php


$reportId = isset($_GET['id']) ? intval($_GET['id']) : 0;


$page_title = "Trail Report";
$page_css = "../assets/css/style.css";

session_start();

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

require_once realpath("../../db_connect.php");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sql = "SELECT * FROM trail_reports WHERE id = ?";

$stmt = $mysqli->prepare($stmt);
$stmt->bind_param("i", $reportId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $report = $result->fetch_assoc();
} else {
    // Handle the case where no report is found for the given ID (e.g., display an error message)
    die("Report not found.");
}

$stmt->close();
mysqli_close($mysqli);
?>
<?php if (isset($report)): ?>

    <h1><?php echo $report['summary']; ?></h1>
    <p><strong>Trail Feature:</strong> <?php // Display feature name based on feature_id (optional) ?></p>
    <p><strong>Rating:</strong> <?php echo $report['rating']; ?></p>
    <p><?php echo nl2br($report['summary']); ?></p>
    <p><i>Submitted on: <?php echo $report['created_at']; ?></i></p>

<?php else: ?>

    <p>Report not found.</p>

<?php endif; ?>

<?php
include_once realpath("../components/tail.inc");
?>