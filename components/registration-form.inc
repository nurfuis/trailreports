<div>
  <h1>User Registration</h1>
  <form action="/pages/register.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required><br>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <input type="checkbox" id="showPassword">
    <label for="showPassword">Show Password</label><br>
    <button type="submit">Register</button>
  </form>
</div>

<script>
  const passwordInput = document.getElementById('password');
  const showPasswordCheckbox = document.getElementById('showPassword');

  showPasswordCheckbox.addEventListener('change', function() {
    if (this.checked) {
      passwordInput.type = 'text';
    } else {
      passwordInput.type = 'password';
    }
  });
</script>
