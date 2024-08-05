<?php
session_start();
require_once realpath("../../db_connect.php");

$isModerator = false;
if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin') {
    $isModerator = true;
}

$previousPage = $_SERVER['HTTP_REFERER'];

$reportId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$OVERALL_RATINGS = [
    'Good' => 1,
    'Passable' => 2,
    'Poor' => 3,
    'Impassable' => 4,
    'Gone' => 5
];
$ratings = array_flip($OVERALL_RATINGS);

$page_title = "Trail Report";
$stylesheet = "../assets/css/style.css";


if ($isModerator) {
    $sql = "SELECT tr.*, f.name AS feature_name, 
       COALESCE(tr.title, 'Untitled') AS report_title, u.username
  FROM trail_reports tr
 INNER JOIN features f ON tr.feature_id = f.id
 INNER JOIN users u ON tr.user_id = u.user_id
 WHERE tr.id = $reportId";
} else {
    $sql = "SELECT tr.*, f.name AS feature_name, 
       COALESCE(tr.title, 'Untitled') AS report_title, u.username
  FROM trail_reports tr
 INNER JOIN features f ON tr.feature_id = f.id
 INNER JOIN users u ON tr.user_id = u.user_id
 WHERE tr.id = $reportId AND active = 1;";
}

$result = $mysqli->query($sql);

if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

if ($result->num_rows === 1) {
    $report = $result->fetch_assoc();
    $isUpdated = false;
    $isUpdated = $report['time_updated'] !== $report['created_at'];
    $postedOnText = $isUpdated ? 'Updated:' : 'Posted:';
} else {
    die("Report not found.");
}
mysqli_close($mysqli);

include_once realpath("../components/head.inc");
include_once realpath("../layouts/bud.inc");
?>
<?php if (isset($report)) : ?>

    <div class="single-report">
        <?php if ($isModerator && $report['active'] == 1) : ?>
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
        <p><strong>Report:</strong></p>

        <?php
        $summary = htmlspecialchars($report['summary'], ENT_QUOTES, 'UTF-8');
        ?>

        <p class="indented"><?php echo nl2br($summary); ?> </div< /p>


    </div>

<?php else : ?>

    <p>Report not found.</p>

<?php endif; ?>

<?php
include_once realpath("../components/tail.inc");
?>