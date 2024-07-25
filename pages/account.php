<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: sign_in.php");
  exit;
}
$page_title = "Account Management";
$stylesheet = "/assets/css/style.css";

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");
include_once realpath("../components/manage_account.inc");
include_once realpath("../components/tail.inc");