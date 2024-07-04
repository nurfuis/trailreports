<?php

$page_title = "Logout";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); // Top section up to and including body tag
include ("../layouts/single.inc"); // An open div with layout class
session_start();

// Destroy the session data
session_destroy();

?>
<h1>Packing Up and Hitting the Trail</h1>
<p>You have successfully logged out. Now you can close your browser to completely end your session.</p>

<?php
include ("../components/tail.inc");
?>