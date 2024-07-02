<div>
  <h1>User Registration</h1>
  <form action="/pages/register.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required><br>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <span class="password-toggle" aria-label="Toggle password visibility"><i class="fas fa-eye"></i></span><br>
    <button type="submit">Register</button>
  </form>
</div>

<script>
  const passwordInput = document.getElementById('password');
  const passwordToggle = document.querySelector('.password-toggle');
  const icon = passwordToggle.querySelector('i');

  // Update icon based on initial password state
  if (passwordInput.value !== "") {
    passwordInput.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  }

  passwordToggle.addEventListener('click', function() {
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
      passwordInput.focus(); // Focus back on password field
    }
  });
</script>

<style>
  .password-toggle {
    /* Add some styling for the icon (position, size, etc.) */
  }
</style>
