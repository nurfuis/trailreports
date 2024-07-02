<h2>Password Recovery Email</h2>
<p>Would you like to add a password recovery email address to your account? This email will be used to send you a link to reset your password if you forget it.</p>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
  <input type="radio" name="recovery_email" id="add_email" value="yes">
  <label for="add_email">Yes, add a recovery email</label><br>
  <input type="radio" name="recovery_email" id="skip_email" value="no" checked>
  <label for="skip_email">Skip for now (you can add it later)</label><br>
  <p class="warning">**Important:** If you don't add a recovery email, you won't be able to reset your password if you forget it. Your account may be lost.</p>
  <button type="submit">Continue</button>
</form>