<?php

include_once ("../db_connect.php");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Define the base directory containing GeoJSON subdirectories
$base_dir = "/media/usb/";

// Open the base directory for reading
if (is_dir($base_dir) && is_readable($base_dir)) {
    $dh = opendir($base_dir);

    if ($dh) {
        while (($dir = readdir($dh)) !== false) {
            // Skip non-directories and special entries ('.', '..')
            if (!is_dir($base_dir . $dir) || in_array($dir, array('.', '..'))) {
                continue;
            }

            // Get collections_id based on directory name
            $collections_id = get_collections_id($mysqli, $dir);
            if ($collections_id === false) {
                echo "Warning: Could not find collections_id for directory '$dir' \n";
                continue; // Skip this directory if no matching collection found
            }

            // Define the subdirectory path for GeoJSON files
            $sub_dir = $base_dir . $dir;

            // Process GeoJSON files within the subdirectory
            process_geojson_files($mysqli, $collections_id, $sub_dir);
        }
        closedir($dh);
    } else {
        echo "Error: Failed to open directory '$base_dir'. \n";
    }
} else {
    echo "Error: Directory '$base_dir' is inaccessible or not a directory. \n";
}

$mysqli->close();

// Function to get collections_id based on directory name
function get_collections_id($mysqli, $dir_name)
{
    $sql = "SELECT id FROM collections WHERE name = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $dir_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row['id'];
    } else {
        return false; // No matching collections entry found
    }

}

// Function to process GeoJSON files in a subdirectory (simplified for now)
function process_geojson_files($mysqli, $collections_id, $sub_dir)
{
    // Open the subdirectory for reading
    if (is_dir($sub_dir) && is_readable($sub_dir)) {
        $sub_dh = opendir($sub_dir);

        if ($sub_dh) {
            while (($file = readdir($sub_dh)) !== false) {
                // Skip non-GeoJSON files and hidden files
                if (in_array($file, array('.', '..', '.gitkeep')) || substr($file, 0, 1) === '.') {
                    continue;
                }

                // Check if it's a GeoJSON file
                if (pathinfo($file, PATHINFO_EXTENSION) === 'geojson') {
                    $filepath = realpath($sub_dir . $file);
                    echo "\n Found GeoJSON file -->  \n";
                    echo "Collection ID: " . $collections_id . "\n";
                    echo $sub_dir . "/" . $file . "\n";
                    // Implement logic to process features and add them to database using collections_id (for future)
                    // ... (will be implemented later)
                }
            }
            closedir($sub_dh);
        } else {
            echo "Error: Failed to open subdirectory '$sub_dir'. \n";
        }
    }
}

?>