<form action="../pages/process_password_update.php" method="post">
    <p>Please enter your new password:</p>
    <input type="password" name="new_password" id="password" required><br><br>
    
    <input type="checkbox" id="showPassword">
    <label for="showPassword">Show Password</label><br><br>
    
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <button type="submit">Update Password</button>
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