<?php
function is_admin() {
    if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin') {
        return true;
    } else {
        return false;
    }
}