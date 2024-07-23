<?php
// Check if user is logged in and admin
if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin') {
    //
} else {
    // Redirect to sign_in.php if not admin
    header('Location: sign_in.php');
    exit;
}