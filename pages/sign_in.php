<?php

$page_title = "Sign in";
$page_css = "../assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/single.inc");

session_start();
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    include_once realpath("../components/sign_out_form.inc");
} else {
    include_once realpath("../components/sign_in_form.inc");

}

include_once realpath("../components/tail.inc");
