<div>
  <h1>New User</h1>
  <h2>Account Creation</h2>
  <p> Create an account in seconds. No email needed to get started! Enjoy complete anonymity or add your email later for
    extra security. Your online identity, your rules.</p>

  <form action="/pages/process_sign_up.php" method="post">
    <input type="hidden" name="currentPagePath" value="<?php echo $currentPagePath; ?>">
    <label for="username">Username:</label>
    <input type="text" autocomplete="username" name="username" id="username" maxlength="30" required /><br><br>
    <p id="username-error" class="alert"></p>
    <script>
      const usernameInput = document.getElementById('username');
      const usernameError = document.getElementById('username-error');

      usernameInput.addEventListener('keyup', function () {
        const username = usernameInput.value;

        const usernameRegex = /^[a-zA-Z0-9]{5,}$/;

        if (usernameRegex.test(username)) {
          usernameError.textContent = '';
        } else {
          usernameError.textContent = 'Username must be at least 5 characters and contain only letters and numbers.';
        }
      });
    </script>

    <label for="password">Password:</label>
    <input type="text" autocomplete="new-password" name="password" id="password" maxlength="256" required /><br><br>
    <p id="password-error" class="alert"></p>

    <script>
      const passwordInput = document.getElementById('password');
      const passwordError = document.getElementById('password-error');

      passwordInput.addEventListener('keyup', function () {
        const password = passwordInput.value;

        // Regular expression for password validation (example)
        const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\da-zA-Z])(?!.*\s).{8,}$/;

        // Check if password matches the regex
        if (passwordRegex.test(password)) {
          passwordError.textContent = ''; // Clear any previous error message
        } else {
          passwordError.innerHTML = 'Password must be at least 8 characters and contain at least:<ul><li>one lowercase letter</li><li>one uppercase letter</li><li>one number</li><li>and one special character</li></ul>';
        }
      });
    </script>
    <button type="submit">Register</button>
  </form>
</div>