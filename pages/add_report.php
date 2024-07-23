<?php

$page_title = "Add report";
$page_css = "../assets/css/style.css";
$currentPagePath = $_SERVER['SCRIPT_NAME'];

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

session_start();
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    include_once realpath("../components/new_report_form.inc");
} else {
    include_once realpath("../components/sign_in_form.inc");

}

include_once realpath("../components/tail.inc");
