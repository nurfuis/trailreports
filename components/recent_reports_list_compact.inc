<?php 
$id = $recent_report['id'];
$title = htmlspecialchars($recent_report['report_title'], ENT_QUOTES, 'UTF-8');
$feature = $recent_report['feature_name'];
?>

<div class="recent-reports">
    <h3>Recent Reports</h3>
    <?php
    if ($result->num_rows > 1) {
        $result->data_seek(1);

        while ($recent_report = $result->fetch_assoc()) {
    ?>
            <div class="recent-report">
                <span><?php echo $feature; ?></span> - 
                <a href="./pages/trail_report.php?id=<?php echo $id; ?>"><?php echo $title; ?></a>
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