<?php
$page_title = "Keep Sur Wild";
$page_css = "./assets/css/style.css";

include_once realpath("./components/head.inc");

include_once realpath("./layouts/main.inc");

include_once realpath("./components/nav.inc");

include_once realpath("./components/header.inc");

require_once realpath("../config.php");

require_once realpath("../db_connect.php");

$sql = "SELECT tr.*, f.name AS feature_name,
        COALESCE(tr.title, 'Untitled') AS report_title
        FROM trail_reports tr
        INNER JOIN features f ON tr.feature_id = f.id
        ORDER BY tr.created_at DESC
        LIMIT 1;";


if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query($sql);

if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

if ($result->num_rows === 1) {
    $report = $result->fetch_assoc();
    ?>

    <div class="single">
        <h3>Latest Report: <?php echo $report['report_title']; ?></h3>
        <p><strong>Trail Feature:</strong> <?php echo $report['feature_name']; ?></p>
        <p><strong>Rating:</strong> <?php echo $report['rating']; ?></p>
        <p><?php echo nl2br($report['summary']); ?></p>
        <p><i>Submitted on: <?php echo $report['created_at']; ?></i></p>
    </div>

    <?php
} else {
    echo "<p>No recent reports found.</p>";
}

$mysqli->close();

// session_start();
// if (isset($_SESSION['user_id'])) {
// $username = $_SESSION['username'];
// include_once realpath("./components/sign_out_form.inc");
// } else {
// include_once realpath("./components/sign_in_form.inc");
// }
include_once realpath("./components/links.inc");

include_once realpath("./components/tail.inc");
?>