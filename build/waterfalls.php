<?php


// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}

$data = json_decode(file_get_contents("/media/usb/WATERFALLS.geojson"));
$collections_id = 14;

// Loop through each feature in the dataset
foreach ($data->features as $feature) {
  
  // Extract feature properties
  $name = $feature->properties->Name;
  $properties = json_encode($feature->properties);
  $geometry_type = $feature->geometry->type;
  $coordinates = json_encode($feature->geometry->coordinates);

  $sql = "INSERT INTO features (collections_id, name, geometry_type, geometry, properties) 
          VALUES (?, ?, ?, ?, ?)";
  $stmt = $mysqli->prepare($sql);
  $stmt->bind_param("sssss", $collections_id, $name, $geometry_type, $coordinates, $properties);

  if ($stmt->execute()) {
    echo "Feature '" . $name . "' added successfully! \n";
  } else {
    echo "Error adding feature: " . $mysqli->error . "\n";
  }

  $stmt->close();
}

$mysqli->close();

?>
