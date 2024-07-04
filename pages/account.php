<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: sign_in.php");
  exit;
}

$page_title = "Account Management";
$page_css = "/assets/css/style.css";

include ("../components/head.inc");
include ("../layouts/single.inc");

include ("../components/manage_account.inc");

include ("../components/tail.inc");