<?php
$page_title = "Trail Reports for Big Sur, California";
$page_css = "./assets/css/style.css";
$currentPagePath = $_SERVER['SCRIPT_NAME'];

session_start();

require_once realpath("../config.php");
require_once realpath("../db_connect.php");

include_once realpath("./components/head.inc");
include_once realpath("./layouts/main.inc");
include_once realpath("./components/header.inc");
include_once realpath("./components/intro.inc");

// main page

$sql = "SELECT tr.*, f.name AS feature_name, 
       COALESCE(tr.title, 'Untitled') AS report_title, u.username
  FROM trail_reports tr
  INNER JOIN features f ON tr.feature_id = f.id
  INNER JOIN users u ON tr.user_id = u.user_id
  ORDER BY tr.created_at DESC
  LIMIT 6;";

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query($sql);

if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

if ($result->num_rows >= 1) {
    $report = $result->fetch_assoc();
    $ratings = array_flip(OVERALL_RATINGS);
    ?>

    <div class="single-report">
        <h3>Latest Report</h3>
        <p><strong>Trail:</strong> <?php echo $report['feature_name']; ?></p>
        <p><strong>Title:</strong> <?php echo $report['report_title']; ?></p>
        <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
        <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
        <p><strong>Report:</strong></p>

        <?php
        $summary = $report['summary'];
        echo "Checkpoint";
            $summary = substr($summary, 0, SUMMARY_LIMIT) . '...';
            $showReadMore = true;
        // if (strlen($summary) > SUMMARY_LIMIT) {
        //     $summary = substr($summary, 0, SUMMARY_LIMIT) . '...';
        //     $showReadMore = true;
        //     echo "1";
        // } else {
        //     $showReadMore = false;
        //     echo "0";
        }
        ?>
        <p class="indented">
            <?php echo nl2br($summary) ?>
            <?php if ($showReadMore): ?>
                <a href="./pages/trail_report.php?id=<?php echo $report['id']; ?>" class="read-more-btn">read more</a>
            <?php endif; ?>
        </p>
        <p class="indented-top light-text"><i>Submitted on: <?php echo $report['created_at']; ?></i></p>
    </div>

    <?php
} else {
    echo "<p>No recent reports found.</p>";
}
?>

<div class="recent-reports">
    <h3>Recent Reports</h3>
    <?php
    if ($result->num_rows > 1) {
        $result->data_seek(1);

        while ($recent_report = $result->fetch_assoc()) {
            ?>
            <div class="recent-report">
                <a
                    href="./pages/trail_report.php?id=<?php echo $recent_report['id']; ?>"><?php echo $recent_report['report_title']; ?></a>
                -
                <?php echo $recent_report['feature_name']; ?>
            </div>
            <?php
        }
    } else {
        echo "<p>No recent reports found.</p>";
    }
    ?>
</div>
<?php
$mysqli->close();

include_once realpath("./components/tail.inc");
?>