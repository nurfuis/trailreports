<?php
session_start();
require_once realpath("../../db_connect.php"); // $msqli connection
require_once realpath("../components/is_admin.inc");

$page_title = "Display Users";
$stylesheet = "../assets/css/style.css";


include_once realpath("../components/head.inc"); // Top section up to and including body tag
include_once realpath("../layouts/wide.inc"); // An open div with layout class

$show_inactive = isset($_GET['show_inactive']) && $_GET['show_inactive'] == 'on';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_POST['user_id'];

  $sql = "UPDATE users SET account_status = 'inactive' WHERE user_id = ?";
  $stmt = mysqli_prepare($mysqli, $sql);
  mysqli_stmt_bind_param($stmt, "i", $user_id);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  echo '<p>' . $user_id . ' was set to inactive.</p>';
}


$sql = "SELECT user_id, username, verified, account_status, login_attempts, last_login_attempt, email_login_attempts, last_email_login_attempt, registration_date 
        FROM users";

if (!$show_inactive) {
  $sql .= " WHERE account_status = 'active'";
}
$sql .= " ORDER BY registration_date DESC";
$result = mysqli_query($mysqli, $sql);
if (!$result) {
  echo "Error: " . mysqli_error($mysqli);
  exit;
}


?>
<h1>Display Users</h1>
<form method="get">
  <input type="checkbox" name="show_inactive" id="show_inactive">
  <label for="show_inactive">Show Inactive Users</label>
  <button type="submit">Submit</button>
</form><br><br>
<form method="post" action="">
  <input type="text" name="user_id" placeholder="Enter User ID">
  <button type="submit">Set to Inactive</button>
</form>

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
