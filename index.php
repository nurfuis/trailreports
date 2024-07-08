<?php
$page_title = "Keep Sur Wild";
$page_css = "/assets/css/style.css";

include ("/components/head.inc");

include ("/layouts/main.inc");

include ("/components/nav.inc");

include ("/components/header.inc");

include ("/components/topo_map.inc");

session_start();
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    include ("/components/sign_out_form.inc");
} else {
    include ("/components/sign_in_form.inc");
}

include ("/components/tail.inc");
