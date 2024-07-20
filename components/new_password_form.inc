<form action="../pages/process_password_update.php" method="post">
    <p>Please enter your new password:</p>
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

    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <button type="submit">Update Password</button>
</form>