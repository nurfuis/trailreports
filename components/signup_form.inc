<div>
  <h2>User Sign Up</h2>
  <form action="/pages/process_signup.php" method="post">
    <label for="username">Username:</label>
    <input type="text" autocomplete="username" name="username" id="username" required /><br />

    <label for="password">Password:</label>
    <input type="password" autocomplete="new-password" name="password" id="password" required /><br />

    <input type="checkbox" id="showPassword" />
    <label for="showPassword">Show Password</label><br />
    <button type="submit">Register</button>
  </form>
</div>

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
