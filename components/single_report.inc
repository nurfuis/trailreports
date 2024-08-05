<?php
$isUpdated = false;
// $isUpdated = $report['time_updated'] !== $report['created_at'];
$postedOnText = $isUpdated ? 'Updated:' : 'Posted:';

$OVERALL_RATINGS = [
    'Good' => 1,
    'Passable' => 2,
    'Poor' => 3,
    'Impassable' => 4,
    'Gone' => 5
];

$ratings = array_flip($OVERALL_RATINGS);

$time = $report['time_updated'];
$formattedTime = date("F j, Y", strtotime($time));
$title = htmlspecialchars($report['report_title'], ENT_QUOTES, 'UTF-8');
$summary = htmlspecialchars($report['summary'], ENT_QUOTES, 'UTF-8');
?>


<div class="single-report">
    <?php if (is_admin() && $report['active'] == 1) : ?>
        <form id="hideReportForm" action="hide_report.php" method="post" onsubmit="return confirmHideReport()">
            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
            <button type="submit">Hide Report</button>
        </form>

        <a class="edit" href="./edit_report.php?id=<?php echo $reportId; ?>"><span>Edit</span></a>

        <script>
            function confirmHideReport() {
                const reportId = document.getElementById("hideReportForm").elements["report_id"].value;
                if (confirm(`Are you sure you want to hide report?`)) {
                    return true;
                } else {
                    return false;
                }
            }
        </script>
    <?php endif; ?>

    <h1>Trail Report</h1>
    <h2><?php echo $report['feature_name']; ?></h2>
    <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
    <p><strong><?php echo $postedOnText; ?></strong><?php echo $formattedTime; ?></p>
    <h3><?php echo nl2br($title); ?> </h3>
    <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
    <p class="indented larger"><?php echo nl2br($summary); ?></p>
</div>
