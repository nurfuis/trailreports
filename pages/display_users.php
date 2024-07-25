<?php
session_start();
require_once realpath("../../db_connect.php"); // $msqli connection
require_once realpath("../components/is_admin.inc");

$page_title = "Display Users";
$stylesheet = "../assets/css/style.css";
date_default_timezone_set('America/Los_Angeles');


include_once realpath("../components/head.inc"); // Top section up to and including body tag
include_once realpath("../layouts/wide.inc"); // An open div with layout class

$sql = "SELECT user_id, username, verified, account_status, login_attempts, last_login_attempt, email_login_attempts, last_email_login_attempt, registration_date 
        FROM users 
        ORDER BY registration_date DESC";

$result = mysqli_query($mysqli, $sql);
if (!$result) {
  echo "Error: " . mysqli_error($mysqli);
  exit;
}
?>
<div class="user-list">
  <div class="user-list-body">
    <?php while ($row = mysqli_fetch_assoc($result)) {
      $formatted_registration_date = $row["registration_date"] ? date('M j, Y g:i A', strtotime($row["registration_date"])) : 'NA';
      $formatted_last_login_attempt = $row["last_login_attempt"] ? date('M j, Y g:i A', strtotime($row["last_login_attempt"])) : 'NA';
      $formatted_last_email_login_attempt = $row["last_email_login_attempt"] ? date('M j, Y g:i A', strtotime($row["last_email_login_attempt"])) : 'NA';


      ?>
      <div class="user-item">
        <div class="user-id"><span>user-id:</span> <?php echo $row["user_id"]; ?></div>
        <div>
          <div class="username"><span>username:</span> <?php echo $row["username"]; ?></div>
          <div class="verified"><span>verified:</span> <?php echo ($row["verified"] ? "Yes" : "No"); ?></div>
          <div class="registration-date"><span>registration-date:</span> <?php echo $formatted_registration_date; ?></div>

          <div class="last-login-attempt"><span>last-login-attempt:</span> <?php echo $formatted_last_login_attempt; ?>
          </div>
          <div class="login-attempts"><span>login-attempts:</span> <?php echo $row["login_attempts"]; ?></div>
          <div class="email-login-attempts"><span>email-login-attempts:</span> <?php echo $row["email_login_attempts"]; ?>
          </div>
          <div class="last-email-login-attempt"><span>last-email-login-attempt:</span>
            <?php echo $formatted_last_email_login_attempt; ?></div>

        </div>
      </div>
    <?php } ?>
  </div>
</div>

<?php
mysqli_close($mysqli);

include_once ("../components/tail.inc"); // closing tags for layout div, body, and html
