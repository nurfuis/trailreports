<div>
  <h2>User Registration</h2>
<form method="post" action="/pages/register_email.php">
  <label for="username">Username:</label>
  <input type="text" id="username" name="username" required><br><br>
  <label for="password">Password:</label>
  <input type="password" id="password" name="password" required><br><br>
  <input type="submit" value="Register">
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
