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
$report = $result->fetch_assoc();

if (isset($report)) {
    include_once realpath("./components/single_report.inc");
} else {
    echo "<p>Report not found.</p>";
}
?>


<div class="recent-reports">
    <h3>Recent Reports</h3>
    <?php
    if ($result->num_rows > 1) {
        $result->data_seek(1);

        while ($recent_report = $result->fetch_assoc()) {
    ?>
            <div class="recent-report"><a href="./pages/trail_report.php?id=<?php echo $recent_report['id']; ?>"><?php echo htmlspecialchars($recent_report['report_title'], ENT_QUOTES, 'UTF-8'); ?></a>
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