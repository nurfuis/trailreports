<div class="regular-padding">
  <h4>User Sign In</h4>

  <form method="post" action="/pages/process_sign_in.php">
    <input type="hidden" name="currentPagePath" value="<?php echo $currentPagePath; ?>">
    <label for="username">Username:</label><br>
    <input type="text" autocomplete="username" id="username" name="username" required><br><br>
    <label for="password">Password:</label><br>
    <input type="password" autocomplete="current-password" id="password" name="password" required><br><br>
    <input type="checkbox" id="showPassword">
    <label for="showPassword">Show Password</label><br><br>
    <input type="submit" value="Sign in"><br><br>
  </form>
  <form method="post" action="/pages/process_email_login.php">
    <p><b>OR</b> have a secure link sent to your inbox:</p>
    <label for="email">Email Address:</label><br>
    <input type="email" autocomplete="email" id="email" name="email" placeholder="Enter email" required><br><br>
    <input type="submit" value="Send Link"><br><br>
  </form>
  <script>
    const passwordInput = document.getElementById("password");
    const showPasswordCheckbox = document.getElementById("showPassword");

    showPasswordCheckbox.addEventListener("change", function () {
      if (this.checked) {
        passwordInput.type = "text";

      } else {
        passwordInput.type = "password";
      }
    });
  </script>

  <div class="notice">Don't have an account? <a href="/pages/sign_up.php">Register Here</a></div>
</div>