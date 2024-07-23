<?php
$page_title = "Trail Reports for Big Sur, California";
$page_css = "./assets/css/style.css";
$currentPagePath = $_SERVER['SCRIPT_NAME'];

session_start();

// Remember to update config and db_connect on the deployment server
require_once realpath("../config.php");
require_once realpath("../db_connect.php");

include_once realpath("./components/head.inc");
include_once realpath("./layouts/main.inc");
include_once realpath("./components/header.inc");
include_once realpath("./components/intro.inc");

include_once realpath("./components/single_select_form.inc");

if (isset($_GET['feature_id'])) {
    $selected_feature_id = $_GET['feature_id'];
    if ($selected_feature_id == 'recent') {
        include_once realpath("./components/recent_reports.inc");

    } else {
        include_once realpath("./components/single_feature.inc");
    }
} else {
    include_once realpath("./components/recent_reports.inc");
}

$mysqli->close();
include_once realpath("./components/footer.inc");
include_once realpath("./components/tail.inc");
