<form action="../pages/process_password_update.php" method="post">
    <p>Please enter your new password:</p>
    <input type="password" name="new_password" required>
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <button type="submit">Update Password</button>
</form>