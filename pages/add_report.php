<?php
session_start();


$page_title = "Add report";
$stylesheet = "../assets/css/style.css";
$currentPagePath = $_SERVER['REQUEST_URI'];

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    include_once realpath("../components/new_report_form.inc");
} else {
    include_once realpath("../components/sign_in_form.inc");

}

include_once realpath("../components/tail.inc");
