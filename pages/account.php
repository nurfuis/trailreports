<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$page_title = "Account Management";
$page_css = "/assets/css/style.css";

include ("../components/head.inc"); 
include ("../layouts/secondary.inc"); 

?>

<h2>Account Management</h2>

<p>Welcome, <?php echo $_SESSION['username']; ?>!</p>

<ul>
  <li><a href="#">Update Email Address</a>
    <?php include ("update_email.inc"); ?> </li>
  <li><a href="#">Update Password</a>
    <?php include ("update_password.inc"); ?> </li>
</ul>

<?php include ("../layouts/tail.inc");  ?>
