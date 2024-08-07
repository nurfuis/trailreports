<?php
session_start();
require_once realpath("../../db_connect.php");
require_once realpath("../components/user_roles.php");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$reportId = isset($_POST['report_id']) ? intval($_POST['report_id']) : 0;
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$logged_in_id = get_user_id();

if (!$reportId) {
    die("Invalid report ID");
}

if ($logged_in_id != $user_id) {
    if (!is_admin()) {
        exit;
    }
}


$sql = "UPDATE trail_reports SET active = 0 WHERE id = ?";
$stmt = mysqli_prepare($mysqli, $sql);
mysqli_stmt_bind_param($stmt, "i", $reportId);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
?>
    <script type="text/javascript">
        window.location.href = "/home.php"
    </script>

<?php
    exit;
} else {
    echo "Error hiding report: " . mysqli_error($mysqli);
    mysqli_stmt_close($stmt);
    mysqli_close($mysqli);
}
