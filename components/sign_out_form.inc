<div class="regular-padding">
  <?php
  if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin') {
    echo '<p>Welcome, Administrator <span class="em">' . $username . '</span>!</p>';

  } else {
    echo '<p>Welcome, <span class="em">' . $username . '</span>!</p>';
  }
  ?>
  <p>You are currently logged in.</p>

  <form action="../pages/process_sign_out.php" method="post">
    <button type="submit">Log out</button>
  </form>

  <?php
  if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin') {
    echo '<p><a href="/pages/hidden_reports.php">Hidden Reports</a></p>';
  }
  ?>
  <p><a href="/pages/user_reports.php">Edit Reports</a></p>
  <p><a href="/pages/account.php">Manage Account</a></p>
</div>