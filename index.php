<?php

$page_title = "Keep Sur Wild";
$page_css = "/assets/css/style.css";

include ("./components/head.inc"); // Top section up to and including body tag
include ("./layouts/main.inc"); // An open div with layout class

session_start();

if (isset($_SESSION['username'])) {
  $username = $_SESSION['username'];
  echo "<p>Welcome, $username! You are currently logged in.</p>";
} else {
  echo "<p>You are not currently logged in.</p>";
}

include ("./components/nav.inc"); // standalone div
include ("./components/welcome-header.inc"); // standalone div
include ("./components/topo-map.inc"); // standalone div + script
include ("./components/registration-form.inc");

include ("./layouts/tail.inc"); // closing tags for layout div, body, and html

include_once ("../db_connect.php"); // $msqli connect
// $query = "SELECT * FROM users";
// $result = mysqli_query($mysqli, $query);

// if ($result) {
//   $users = mysqli_fetch_all($result);

//   foreach ($users as $user) {
//     echo '<h6>Username: </h6>', $user[1], ' <h6>Registration date: </h6>', $user[3];
//   }

//   mysqli_free_result($result);
// }
mysqli_close($mysqli);
