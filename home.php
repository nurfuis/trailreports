<?php
session_start();
require_once realpath("../db_connect.php");

date_default_timezone_set('America/Los_Angeles');
$californiaTime = date('g:i a');

$stylesheet = "./assets/css/style.css";
$lightTheme = "";

if (date('G') >= 4 && date('G') < 12) {
    $lightTheme = './assets/css/morning.css';
} elseif (date('G') >= 12 && date('G') < 18) {
    $lightTheme = './assets/css/afternoon.css';
} else {
    $lightTheme = './assets/css/night.css';
}

if (isset($_GET['collection'])) {
    $collection = $_GET['collection'];
} else {
    $collection = 1;
}

$page_title = "Trail Reports for Big Sur, California";
$currentPagePath = $_SERVER['REQUEST_URI'];

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
