<?php

include_once ("../db_connect.php");

// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

$data = json_decode(file_get_contents("../data/CAMPGROUNDS.geojson"));

// Loop through each feature in the dataset
foreach ($data->features as $feature) {
  
  // Check if feature type is "campground"
  if ($feature->properties->Type !== "campground") {
    continue; // Skip non-campground features
  }
  
  // Extract feature properties
  $name = $feature->properties->Name;
  $properties = json_encode($feature->properties);
  $geometry_type = $feature->geometry->type;
  $coordinates = json_encode($feature->geometry->coordinates);

  // Prepare and execute the INSERT or UPDATE query
  $sql = "INSERT INTO features (collection_id, name, geometry_type, geometry, properties) 
          VALUES (?, ?, ?, ?, ?) 
          ON DUPLICATE KEY UPDATE collection_id = VALUES(collection_id), 
                                  geometry_type = VALUES(geometry_type), 
                                  geometry = VALUES(geometry), 
                                  properties = VALUES(properties)";
  $stmt = $mysqli->prepare($sql);

  // Assuming a default collection_id (replace with your actual value)
  $collection_id = 1;
  $stmt->bind_param("sssss", $collection_id, $name, $geometry_type, $coordinates, $properties);

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
    echo "Error adding/updating feature: " . $mysqli->error . "\n";
  }

  $stmt->close();
}

$mysqli->close();

?>
