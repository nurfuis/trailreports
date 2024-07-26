<?php
session_start();
$currentPagePath = $_SERVER['REQUEST_URI'];

$page_title = "Edit report";
$stylesheet = "../assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    include_once realpath("../components/edit_report_form.inc");
} else {
    include_once realpath("../components/sign_in_form.inc");

}

include_once realpath("../components/tail.inc");
