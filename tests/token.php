<?php

$tokenBytes = random_bytes(6);
if ($tokenBytes !== false) {
    echo '$tokenBytes';
} else {
    error_log("Failed to generate random bytes for token");
}