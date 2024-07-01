<?php

$page_title = "Register";
$page_css = "../assets/css/style.css";

include ("../layouts/head.inc"); // Top section up to and including body tag
include ("../layouts/secondary.inc"); // An open div with layout class


include ("../layouts/tail.inc"); // closing tags for layout div, body, and html

include_once ("../../db_connect.php"); // $msqli connect
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
