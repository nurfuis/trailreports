<?php
$page_title = "Trail Report";
$page_css = "../assets/css/style.css";

$previousPage = $_SERVER['HTTP_REFERER'];

session_start();
include_once realpath("../components/head.inc");
include_once realpath("../layouts/bud.inc");
require_once realpath("../../config.php");
require_once realpath("../../db_connect.php");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$reportId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT tr.*, f.name AS feature_name, 
       COALESCE(tr.title, 'Untitled') AS report_title, u.username
  FROM trail_reports tr
 INNER JOIN features f ON tr.feature_id = f.id
 INNER JOIN users u ON tr.user_id = u.user_id
 WHERE tr.id = $reportId;";


if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query($sql);
$ratings = array_flip(OVERALL_RATINGS);

if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}


if ($result->num_rows === 1) {
    $report = $result->fetch_assoc();
} else {
    // Handle the case where no report is found for the given ID (e.g., display an error message)
    die("Report not found.");
}

mysqli_close($mysqli);
?>
<?php if (isset($report)): ?>

    <div class="single-report">
        <h3>Trail Report</h3>
        <p><strong>Trail:</strong> <?php echo $report['feature_name']; ?></p>
        <p><strong>Title:</strong> <?php echo $report['report_title']; ?></p>
        <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
        <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
        <p><strong>Report:</strong></p>

        <?php
        $summary = $report['summary'];
        ?>

        <p class="indented"><?php echo nl2br($summary); ?> </div< /p>

        <p class="indented-top light-text"><i>Submitted on: <?php echo $report['created_at']; ?></i></p>

    </div>

<?php else: ?>

    <p>Report not found.</p>

<?php endif; ?>

<?php
include_once realpath("../components/tail.inc");
?>