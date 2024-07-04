<?php
// Your main script
require_once ('config.php');

$page_title = "Keep Sur Wild";
$page_css = "/assets/css/style.css";

include ("./components/head.inc");
include ("./layouts/main.inc");

include ("./components/nav.inc");


include ("./components/welcome_header.inc");
include ("./components/topo_map.inc");

session_start();
if (isset($_SESSION['user_id'])) {
    $username = $_SESSION['username'];
    include ("./components/is_logged_in.inc");
} else {
    include ("./components/signin_form.inc");
}

include ("./layouts/tail.inc");
