<?php

$page_title = "Sign in";
$page_css = "../assets/css/style.css";
$host = "192.168.0.78";

include ("../components/head.inc");
include ("../layouts/secondary.inc");

session_start();
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    include ("../components/is_logged_in.inc");
} else {
    include ("../components/signin_form.inc");

}

include ("../layouts/tail.inc");
