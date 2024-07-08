<?php
include_once ("../db_connect.php");

// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}
$data = json_decode(file_get_contents("../data/CAMPGROUNDS.geojson"));

// Loop through each feature in the dataset
foreach ($data->features as $feature) {
  
  // Extract feature properties
  $name = $feature->properties->Name;
  $properties = json_encode($feature->properties);
  $geometry_type = $feature->geometry->type;
  $coordinates = json_encode($feature->geometry->coordinates);

  // Prepare and execute the INSERT query
  $sql = "INSERT INTO features (name, geometry_type, geometry, properties) 
          VALUES (?, ?, ?, ?)";
  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param("ssss", $name, $geometry_type, $coordinates, $properties);

  if ($stmt->execute()) {
    echo "Feature '" . $name . "' added successfully! \n";
  } else {
    // Handle potential duplicate key error (assuming UNIQUE constraint on feature_name)
    if ($mysqli->errno === 1062) { // Duplicate key error code
      echo "Feature '" . $name . "' already exists. Skipping insertion. \n";
    } else {
      echo "Error adding feature: " . $mysqli->error . "\n";
    }
  }

  $stmt->close();
}

$mysqli->close();

?>
