<?php

$page_title = "Sign up";
$page_css = "../assets/css/style.css";

include ("../components/head.inc");
include ("../layouts/secondary.inc");

session_start();
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    include ("../components/sign_out_form.inc");
} else {
    include ("../components/sign_up_form.inc");

}

include ("../components/tail.inc");