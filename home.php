<?php
session_start();
require_once realpath("../db_connect.php");
require_once realpath("./components/user_roles.php");

$page_title = "Trail Reports";
$currentPagePath = $_SERVER['REQUEST_URI'];
$stylesheet = "./assets/css/style.css";


$currentTime = time();
$dateTime = new DateTime();
$dateTime->setTimestamp($currentTime);
$dateTime->setTimezone(new DateTimeZone('America/Los_Angeles'));

$hour = (int)$dateTime->format('G');

$lightTheme = "";

if ($hour >= 4 && $hour < 12) {
    $lightTheme = './assets/css/morning.css';
} elseif ($hour >= 12 && $hour < 20) {
    $lightTheme = './assets/css/afternoon.css';
} else {
    $lightTheme = './assets/css/night.css';
}


if (isset($_GET['collection'])) {
    $collection = $_GET['collection'];
} else {
    $collection = "1, 2"; // setting magic #s is sad
}

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
