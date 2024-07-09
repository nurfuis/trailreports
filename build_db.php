<?php

// Define the directory containing your collection scripts
$script_dir = __DIR__ . '/build'; // Adjust path if needed

// Validate directory existence and access
if (!is_dir($script_dir) || !is_readable($script_dir)) {
    die("Error: Script directory '$script_dir' is inaccessible.");
}

// Open the directory for reading
$dh = opendir($script_dir);

if ($dh) {
    while (($file = readdir($dh)) !== false) {
        // Skip non-PHP files ('.', '..') and hidden files (starting with '.')
        if (in_array($file, array('.', '..', '.gitkeep')) || substr($file, 0, 1) === '.') {
            continue;
        }

        // Check if it's a PHP file
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $script_path = realpath($script_dir . '/' . $file);
            echo "Running script: $script_path \n";
            // Execute the script using include_once
            include_once $script_path;
        }
    }
    closedir($dh);
} else {
    die("Error: Failed to open directory '$script_dir'.");
}

?>