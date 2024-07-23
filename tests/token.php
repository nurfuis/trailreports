<?php

$tokenBytes = random_bytes(6);
if ($tokenBytes !== false) {
    $token = bin2hex($tokenBytes);
} else {
    error_log("Failed to generate random bytes for token");
}