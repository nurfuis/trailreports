<?php

$page_title = "Logout";
$stylesheet = "/assets/css/style.css";

include_once realpath("../components/head.inc"); // Top section up to and including body tag
include_once realpath("../layouts/wide.inc"); // An open div with layout class

session_start();
session_destroy();

?>

<h1>Packing Up and Hitting the Trail</h1>
<p>You have successfully logged out. Now you can close your browser to completely end your session.</p>

<?php
include_once realpath("../components/tail.inc");
?>