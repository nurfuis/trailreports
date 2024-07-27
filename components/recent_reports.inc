<?php
$SUMMARY_LIMIT = 500;
$OVERALL_RATINGS = [
    'Good' => 1,
    'Passable' => 2,
    'Poor' => 3,
    'Impassable' => 4,
    'Gone' => 5
];
$ratings = array_flip($OVERALL_RATINGS);

$sql = "SELECT tr.*, f.name AS feature_name, 
       COALESCE(tr.title, 'Untitled') AS report_title, u.username
  FROM trail_reports tr
  INNER JOIN features f ON tr.feature_id = f.id
  INNER JOIN users u ON tr.user_id = u.user_id WHERE tr.active = 1
  ORDER BY tr.created_at DESC
  LIMIT 6;";

$result = $mysqli->query($sql);

if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

if ($result->num_rows >= 1) {
    $report = $result->fetch_assoc();
    $summary = $report['summary'];
    if (strlen($summary) > $SUMMARY_LIMIT) {
        $summary = substr($summary, 0, $SUMMARY_LIMIT) . '...';
        $showReadMore = true;
    } else {
        $showReadMore = false;
    }

    $isUpdated = $report['time_updated'] !== $report['created_at']; // Check if updated time is different
    $postedOnText = $isUpdated ? 'Updated:' : 'Posted:';
    $time = $report['time_updated'];
    ?>

    <div class="single-report">
        <h3>Latest Report</h3>
        <p><strong>Trail:</strong> <?php echo $report['feature_name']; ?></p>
        <p><strong>Title:</strong> <?php echo $report['report_title']; ?></p>
        <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
        <p><strong><?php echo $postedOnText ?></strong> <?php echo date("F j, Y", strtotime($time)); ?></p>
        <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
        <p><strong>Report:</strong></p>
        <p class="indented"><?php echo nl2br($summary) ?><?php if ($showReadMore): ?><a
                    href="./pages/trail_report.php?id=<?php echo $report['id']; ?>" class="read-more-btn">read
                    more</a><?php endif; ?>
        </p>
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
            <div class="recent-report"><a
                    href="./pages/trail_report.php?id=<?php echo $recent_report['id']; ?>"><?php echo $recent_report['report_title']; ?></a>
                - <span><?php echo $recent_report['feature_name']; ?></span>
            </div>
            <?php
        }
    } else {
        echo "<p>No recent reports found.</p>";
    }
    ?><br>
    <hr>
    <div class="regular-padding nav">
        <a href="./pages/add_report.php">Submit a
            Report</a> <span class="em">or</span>
        <a href="./pages/display_reports.php">View More Reports</a>
    </div>
</div>
