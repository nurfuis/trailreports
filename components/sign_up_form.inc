<div>
  <h3>New User</h3>
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
          passwordError.textContent = 'Password must be at least 8 characters and contain at least one lowercase letter, one uppercase letter, one number, and one special character.';
        }
      });
    </script>
    <button type="submit">Register</button>
  </form>
</div>