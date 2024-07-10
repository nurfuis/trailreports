<?php
$page_title = "Keep Sur Wild";
$page_css = "./assets/css/style.css";

include_once realpath("./components/head.inc");

include_once realpath("./layouts/main.inc");

include_once realpath("./components/nav.inc");

include_once realpath("./components/header.inc");

include_once realpath("./components/topo_map.inc");

session_start();
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    include_once realpath("./components/sign_out_form.inc");
} else {
    include_once realpath("./components/sign_in_form.inc");
}

include_once realpath("./components/tail.inc");
