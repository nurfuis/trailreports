<?php
function is_admin() {
    if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'admin') {
        return true;
    } else {
        return false;
    }
}

function is_user() {
    if (isset($_SESSION['user_level']) && $_SESSION['user_level'] === 'user') {
        return true;
    } else {
        return false;
    }
}

function get_user_id() {
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    } else {
        return null;
    }
}