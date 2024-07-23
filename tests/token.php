<?php

$token = md5(uniqid(mt_rand(), true));
if ($token !== false) {
    echo $token;
} else {
    error_log("Failed to generate random bytes for token");
}