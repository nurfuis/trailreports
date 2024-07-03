<p>Welcome, $username! You are currently logged in.</p>

<form action="logout.php" method="post">
  <button type="submit">Logout (<?php echo $username; ?>)</button>
</form>

<a href="account.php">Manage Account</a>