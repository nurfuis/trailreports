<?php

session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: sign_in.php");
  exit;
}

$page_title = "Account Management";
$page_css = "/assets/css/style.css";

include realpath("../components/head.inc");
include realpath("../layouts/single.inc");
include realpath("../components/manage_account.inc");
include realpath("../components/tail.inc");