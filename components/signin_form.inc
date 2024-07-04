<h2>Login</h2>

<form method="post" action="/pages/process_signin.php">
  <label for="username">Username:</label>
  <input type="text" id="username" name="username" required><br><br>
  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required><br><br>
  <input type="checkbox" id="showPassword">
  <label for="showPassword">Show Password</label><br><br>

  <input type="submit" value="Login">
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

<p>Don't have an account? <a href="signup.php">Register Here</a></p>