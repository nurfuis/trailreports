<div class="regular-padding">
  <p>Welcome, <?php echo $username; ?>! You are currently logged in.</p>

  <form action="../pages/process_sign_out.php" method="post">
    <button type="submit">Logout </button>
  </form>
  <p><a href="/pages/account.php">Manage Account</a></p>
</div>