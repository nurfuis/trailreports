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
                // Access report ID from the hidden form field
                const reportId = document.getElementById("hideReportForm").elements["report_id"].value;

                if (confirm(`Are you sure you want to hide report?`)) {
                    return true; // Submit the form if confirmed
                } else {
                    return false; // Prevent default form submission
                }
            }
        </script>
    <?php endif; ?>


    <h3>Trail Report</h3>
    <p><strong>Title:</strong> <?php echo htmlspecialchars($report['report_title'], ENT_QUOTES, 'UTF-8'); ?></p>
    
    <p><strong>Trail:</strong> <?php echo $report['feature_name']; ?></p>

    <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
    
    
    <?php
    $time = $report['time_updated'];
    $formattedTime = date("F j, Y", strtotime($time));
    echo " <p><strong>" . $postedOnText . "</strong> " . $formattedTime . "</p>"; ?>


    <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
    
    <p><strong>Report:</strong>

    <?php
    $summary = htmlspecialchars($report['summary'], ENT_QUOTES, 'UTF-8');
    ?>
    </p>

    <p class="indented"><?php echo nl2br($summary); ?> </div></p>

</div>
