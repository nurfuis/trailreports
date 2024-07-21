<div class="regular-padding">
  <p>Welcome, <span class="em"><?php echo $username; ?></span>! You are currently logged in.</p>

  <form action="../pages/process_sign_out.php" method="post">
    <button type="submit">Log out </button>
  </form>
  <p><a href="/pages/user_reports.php">Edit Reports</a></p>
  <p><a href="/pages/account.php">Manage Account</a></p>
</div>