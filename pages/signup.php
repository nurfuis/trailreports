<?php

$page_title = "Signup";
$page_css = "/assets/css/style.css";

include ("../components/head.inc");
include ("../layouts/secondary.inc");

session_start();
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    include ("../components/is_logged_in.inc");
} else {
    include ("../components/signup_form.inc");

}

include ("../layouts/tail.inc");
