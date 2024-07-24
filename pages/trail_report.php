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

if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin') {
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




if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query($sql);
$OVERALL_RATINGS = [
    'Good' => 1,
    'Passable' => 2,
    'Poor' => 3,
    'Impassable' => 4,
    'Gone' => 5
];
$ratings = array_flip($OVERALL_RATINGS);

if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}


if ($result->num_rows === 1) {
    $report = $result->fetch_assoc();
    $isUpdated = $report['time_updated'] !== $report['created_at']; // Check if updated time is different
    $postedOnText = $isUpdated ? 'Updated:' : 'Posted:';
} else {
    // Handle the case where no report is found for the given ID (e.g., display an error message)
    die("Report not found.");
}

mysqli_close($mysqli);
?>
<?php if (isset($report)): ?>

    <div class="single-report">
        <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin' && $report['active'] == 1): ?>
            <form id="hideReportForm" action="hide_report.php" method="post" onsubmit="return confirmHideReport()">
                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                <button type="submit">Hide Report</button>
            </form>

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
        <p><strong>Title:</strong> <?php echo $report['report_title']; ?></p>
        <p><strong>Trail:</strong> <?php echo $report['feature_name']; ?></p>

        <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
        <?php
        $time = $report['time_updated'];
        $formattedTime = date("F j, Y", strtotime($time));
        echo " <p><strong>" . $postedOnText . "</strong> " . $formattedTime . "</p>"; ?>
        <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
        <p><strong>Report:</strong></p>

        <?php
        $summary = $report['summary'];
        ?>

        <p class="indented"><?php echo nl2br($summary); ?> </div< /p>


    </div>

<?php else: ?>

    <p>Report not found.</p>

<?php endif; ?>

<?php
include_once realpath("../components/tail.inc");
?>