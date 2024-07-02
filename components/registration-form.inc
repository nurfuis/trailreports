<div>
  <h1>User Registration</h1>
  <form action="/pages/register.php" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required><br>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <span class="password-toggle"><i class="fas fa-eye"></i></span><br>
    <button type="submit">Register</button>
  </form>
</div>

<script>
  const passwordInput = document.getElementById('password');
  const passwordToggle = document.querySelector('.password-toggle');

  passwordToggle.addEventListener('click', function() {
    const icon = this.querySelector('i');
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  });
</script>
