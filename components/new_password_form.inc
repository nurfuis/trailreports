<form action="../pages/process_password_update.php" method="post">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

    <p>Please enter your new password:</p>

    <input type="text" autocomplete="new-password" name="new_password" id="password" maxlength="256" required /><br><br>
    <p id="password-error" class="alert"></p>

    <script>
      const passwordInput = document.getElementById('password');
      const passwordError = document.getElementById('password-error');

      passwordInput.addEventListener('keyup',
        function() {
          const password = passwordInput.value;

          // Check password length first
          if (password.length >= 15) {
            passwordError.textContent = '';
            return; // Password is long enough, no further validation needed
          }

          // Regular expression for password validation (for passwords shorter than 15 characters)
          const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\da-zA-Z])(?!.*\s).{8,}$/;

          // Check if password matches the regex
          if (passwordRegex.test(password)) {
            passwordError.textContent = ''; // Clear any previous error message
          } else {
            passwordError.innerHTML = 'Password must be at least 15 characters OR<br>8 characters and contain at least:<ul><li>one lowercase letter</li><li>one uppercase letter</li><li>one number</li><li>and one special character</li></ul>';
          }
        });
    </script>

    <button type="submit">Update Password</button>
</form>