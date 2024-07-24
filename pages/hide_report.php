<?php
session_start();
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'admin') {
    // Redirect if user is not admin
    header("Location: display_reports.php");
    exit;
}
require_once realpath("../../db_connect.php");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$reportId = isset($_POST['report_id']) ? intval($_POST['report_id']) : 0;

if (!$reportId) {
    die("Invalid report ID");
}

$sql = "UPDATE trail_reports SET active = 0 WHERE id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $reportId);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
    ?>
    <script type="text/javascript">
        window.location.href = "hidden_reports.php" 
    </script>

    <?php
    header("Location: hidden_reports.php");
    exit;
} else {
    echo "Error hiding report: " . mysqli_error($mysqli);
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
}