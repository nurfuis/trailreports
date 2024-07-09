<?php

include_once ("../db_connect.php");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$collections_id = 1; // Set collections_id

// Define the directory containing GeoJSON files
$dir = "/media/usb/lospadres/trails/"; // Adjust the path to your directory

// Open the directory for reading
if (is_dir($dir) && is_readable($dir)) {
    $dh = opendir($dir);

    if ($dh) {
        while (($file = readdir($dh)) !== false) {
            // Skip non-GeoJSON files ('.', '..') and hidden files (starting with '.')
            if (in_array($file, array('.', '..', '.gitkeep')) || substr($file, 0, 1) === '.') {
                continue;
            }

            // Check if it's a GeoJSON file (extension check)
            if (pathinfo($file, PATHINFO_EXTENSION) === 'geojson') {
                $filepath = realpath($dir . $file);
                echo "Processing file: $filepath \n";

                // Read the GeoJSON file content
                $data = json_decode(file_get_contents($filepath));

                // Process each feature in the GeoJSON file
                foreach ($data->features as $feature) {
                    // Extract feature properties
                    $name = $feature->properties->Name;
                    $properties = json_encode($feature->properties);
                    $geometry_type = $feature->geometry->type;
                    $coordinates = json_encode($feature->geometry->coordinates);

                    // Prepare and execute the INSERT query
                    $sql = "INSERT INTO features (collections_id, name, geometry_type, geometry, properties) 
          VALUES (?, ?, ?, ?, ?) 
          ON DUPLICATE KEY UPDATE collections_id = VALUES(collections_id), 
                                 geometry_type = VALUES(geometry_type), 
                                 geometry = VALUES(geometry), 
                                 properties = VALUES(properties)";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("sssss", $collections_id, $name, $geometry_type, $coordinates, $properties);

                    if ($stmt->execute()) {
                        $rows_affected = $stmt->affected_rows;
                        if ($rows_affected === 1) {
                            echo "Feature '" . $name . "' added successfully! \n";
                        } else if ($rows_affected > 1) {
                            echo "Unexpected behavior: Multiple rows affected. \n";
                        } else { // rows_affected === 0 (update case)
                            echo "Feature '" . $name . "' already exists and has been updated. \n";
                        }
                    } else {
                        echo "Error adding feature: " . $mysqli->error . "\n";
                    }

                    $stmt->close();
                }
            }
        }
        closedir($dh);
    } else {
        echo "Error: Failed to open directory '$dir'. \n";
    }
} else {
    echo "Error: Directory '$dir' is inaccessible or not a directory. \n";
}

$mysqli->close();

?>