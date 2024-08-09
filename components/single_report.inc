<?php
// reqs $report
// reqs user_roles
// reqs db_conncect

// if a post has been edited
$isUpdated = false;
// $isUpdated = $report['time_updated'] !== $report['created_at'];
$postedOnText = $isUpdated ? 'Updated:' : 'Posted:';

// TODO - define as global? 
$OVERALL_RATINGS = [
    'Good' => 1,
    'Passable' => 2,
    'Poor' => 3,
    'Impassable' => 4,
    'Gone' => 5
];

$ratings = array_flip($OVERALL_RATINGS);

$userId = get_user_id(); // id | null

// Report
$timeUpdated = $report['time_updated'];

$updateTimeFormat = date("F j, Y", strtotime($timeUpdated));

$title = htmlspecialchars($report['report_title'], ENT_QUOTES, 'UTF-8');

$summary = $report['summary'];

function formatSummaryWithBreaks($summary) {
    $summary = htmlspecialchars($summary, ENT_QUOTES, 'UTF-8');
  
    // Split the summary into sentences
    $sentences = explode('. ', $summary);
  
    // Rebuild the summary with newlines every third sentence
    $formattedSummary = '';
    $sentenceCount = 0;
    foreach ($sentences as $sentence) {
      $formattedSummary .= $sentence . '. ';
      $sentenceCount++;
  
      if ($sentenceCount % 3 === 0) {
        $formattedSummary .= "<br><br>";
      }
    }
  
    // Ensure the last period and space are removed
    return substr($formattedSummary, 0, -2);
  }
  
  
  
$formattedSummary = formatSummaryWithBreaks($summary);

$submitterId = $report['user_id'];

$reportId = $report['id'];

$selected_feature_id = $report['feature_id'];

$geometryType = $report["geometry_type"];


// Map
$sql = "SELECT ST_AsGeoJSON(geometry) AS geojson, p.feature_id, f.name AS feature_name
FROM polylines p
INNER JOIN features f ON p.feature_id = f.id WHERE p.feature_id = " . $selected_feature_id . ";";

$result_polylines = mysqli_query($mysqli, $sql);

$geojsonFeatures = [];

while ($row = mysqli_fetch_assoc($result_polylines)) {
    $geojsonFeatures[] = [
        'type' => 'Feature',
        'geometry' => json_decode($row['geojson'], true),
        'properties' => [
            'feature_id' => $row['feature_id'],
            'feature_name' => $row['feature_name']
        ]
    ];
}
$geojsonData = ['type' => 'FeatureCollection', 'features' => $geojsonFeatures];

$sql_points = "SELECT ST_AsGeoJSON(geometry) AS geojson, p.feature_id, f.name AS feature_name
FROM points p
INNER JOIN features f ON p.feature_id = f.id WHERE p.feature_id = " . $selected_feature_id . ";";

$result_points = mysqli_query($mysqli, $sql_points);


$geojsonFeatures_points = [];

while ($row = mysqli_fetch_assoc($result_points)) {
    $geojsonFeatures_points[] = [
        'type' => 'Feature',
        'geometry' => json_decode($row['geojson'], true),
        'properties' => [
            'feature_id' => $row['feature_id'],
            'feature_name' => $row['feature_name']
        ]
    ];
}

$geojsonDataPoints = ['type' => 'FeatureCollection', 'features' => $geojsonFeatures_points];
$MAP_ZOOM = 13;

?>


<div class="single-report">

    <div class="map-icon__wrapper">
        <div class="map-icon__dashes"></div>
        <img class="map-icon" src="../assets/images/compass-icon.webp">
    </div>


    <h1>Trail Report</h1>

    <div id="map-inset"></div>

    <!-- Map -->
    <script>
        const geojsonData = JSON.parse('<?php echo json_encode($geojsonData); ?>');
        const geojsonDataPoints = JSON.parse('<?php echo json_encode($geojsonDataPoints); ?>');

        var map = L.map("map-inset", {
            renderer: L.canvas({
                tolerance: 8
            }),
            zoomControl: false,
        });

        function getParentWidth(elementId) {
            const element = document.getElementById(elementId);
            const parent = element.parentElement;
            return parent.offsetWidth;
        }

        const width = getParentWidth("map-inset");
        const height = 200;

        document.getElementById("map-inset").style.width = "80%";
        document.getElementById("map-inset").style.height = height + "px";


        var tileLayer = L.tileLayer("https://tile.opentopomap.org/{z}/{x}/{y}.png", {
            maxZoom: 15,
        });


        tileLayer.addTo(map);


        let allBounds = new L.LatLngBounds([]); // Initialize an empty bounds object

        // Process features from geojsonData
        geojsonData.features.forEach(feature => {
            const popup = feature.properties.feature_name;

            // Create a GeoJSON layer
            const geoJsonLayer = L.geoJSON(feature, {
                style: {
                    color: 'red'
                }
            }).bindPopup(function(layer) {
                return popup;
            }).addTo(map);

            // Extend the existing bounds
            allBounds.extend(geoJsonLayer.getBounds());
        });
        var geojsonMarkerOptions = {
            radius: 6,
            fillColor: "#ff7800",
            color: "#000",
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
        };
        // Process features from geojsonDataPoints
        geojsonDataPoints.features.forEach(feature => {
            const popup = feature.properties.feature_name;

            // Create a GeoJSON layer
            const markerLayer = L.geoJSON(feature, {
                pointToLayer: function(feature, latlng) {
                    return L.circleMarker(latlng, geojsonMarkerOptions);
                }
            }).bindPopup(function(layer) {
                return popup;
            }).addTo(map);

            // Extend the existing bounds
            allBounds.extend(markerLayer.getBounds());
        });

        // After iterating through all features
        map.fitBounds(allBounds);

        const mapInset = document.getElementById('map-inset');
        
        
        const compassIcon = document.querySelector('.map-icon');
        let rocking = false; // Flag to track rocking state
        let rotationAngle = 0;
        compassIcon.addEventListener('click', () => {
            // Randomly choose rotation direction
            const direction = Math.random() < 0.5 ? -1 : 1; // -1 for clockwise, 1 for counter-clockwise

            // Randomize rotation amount (less than 360 for rocking)
            const randomRotation = Math.random() * 45; // Adjust for desired oscillation range (e.g., 10-30 degrees)

            // Update rotation angle with direction and randomness
            rotationAngle += direction * randomRotation;

            // Start or stop rocking animation
            rocking = !rocking;

            // Apply rotation and rocking animation
            compassIcon.style.transform = `rotate(${rotationAngle}deg)`;
            if (rocking) {
                rockAnimation(); // Start rocking animation
            } else {
                // Optionally add a reset animation after rocking stops
                resetAnimation(); // (Optional) Add a reset animation if desired
            }
        });

        function rockAnimation() {
            // Define rocking animation logic
            // Here's a basic example for illustration:
            let rockingAngle = 0;
            const rockingInterval = setInterval(() => {
                rockingAngle = Math.sin(Date.now() / 100) * 5; // Adjust for rocking intensity (e.g., 2-5 degrees)
                compassIcon.style.transform = `rotate(${rotationAngle + rockingAngle}deg)`;
                if (Math.abs(rockingAngle) < 0.1) { // Adjust threshold for stopping rocking
                    clearInterval(rockingInterval);
                    rocking = false; // Reset rocking state
                }
            }, 20); // Adjust interval for rocking speed (e.g., 10-30 milliseconds)
        }

        map.setMaxBounds(allBounds);

        window.addEventListener('resize', () => {
            const mapContainer = document.getElementById('map-inset');
            mapContainer.style.width = '80%';
            mapContainer.style.height = '200px';

            map.invalidateSize();
        });
    </script>
    <?php
    if ($geometryType == 'LineString') {
        $polyline_sql = "SELECT ST_AsText(geometry) AS text_geometry, source FROM polylines WHERE feature_id = $selected_feature_id;";

        $polyline_result = mysqli_query($mysqli, $polyline_sql);

        if (!$polyline_result) {
            echo "Error: " . mysqli_error($mysqli);
            exit;
        }

        $first_polyline = mysqli_fetch_assoc($polyline_result);

        // Continue processing only if a first polyline is found
        if ($first_polyline) {
            $points = explode(",", substr($first_polyline['text_geometry'], strpos($first_polyline['text_geometry'], "(") + 1, -1));
            $source = $first_polyline['source'];
            $first_point = explode(" ", trim($points[0]));
            $first_longitude = $first_point[0];
            $first_latitude = $first_point[1];

            $geometry_string = "$first_longitude, $first_latitude";

            // Create a link to another page with lat, long, and zoom
            $link_url = "./pages/topo_map.php?name=" . $report['feature_name'] . "&lat=" . $first_latitude . "&long=" . $first_longitude . "&zoom=" . $MAP_ZOOM;
            if (!empty($source)) {
                $link_url .= "&source=" . $source;
            }

            echo '<p><a href="' . $link_url . '"><span>Open Trail Map</span></a></p>';
        } else {
            echo "No polylines found for this feature.";
        }


        while ($polyline = mysqli_fetch_assoc($polyline_result)) {
            echo '<div class="hidden">';
            echo "<h4>Segment</h4>";
            echo "<p>Path: " . $polyline['text_geometry'] . "</p>";
            echo '</div>';
        }
    } elseif ($geometryType == 'Point') {
        $point_sql = "SELECT ST_AsText(geometry) AS text_geometry FROM points WHERE feature_id = $selected_feature_id;";
        $point_result = mysqli_query($mysqli, $point_sql);

        if (!$point_result) {
            echo "Error: " . mysqli_error($mysqli);
            exit;
        }

        $first_point = mysqli_fetch_assoc($point_result);

        if ($first_point) {
            $point_coords = explode(" ", substr($first_point['text_geometry'], strpos($first_point['text_geometry'], "(") + 1, -1));

            $longitude = trim($point_coords[0]);
            $latitude = trim($point_coords[1]);


            $geometry_string = "$longitude, $latitude";

            // Create a link to another page with lat, long, and zoom
            $link_url = "./pages/topo_map.php?name=" . $report['feature_name'] . "&lat=" . $latitude . "&long=" . $longitude . "&zoom=" . $MAP_ZOOM;

            echo '<p><a href="' . $link_url . '"><span>Open Trail Map</span> </a></p>';
        } else {
            echo "No point found for this feature.";
        }

        mysqli_free_result($point_result);
    }
    ?>
    <h2><?php echo $report['feature_name']; ?></h2>

    <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
    <p><strong><?php echo $postedOnText; ?></strong> <?php echo $updateTimeFormat; ?></p>


    <h3><?php echo nl2br($title); ?> </h3>

    <p><strong>Trail Condition:</strong> <?php echo $ratings[$report['rating']]; ?></p>
    <p class="indented larger">
        <?php echo nl2br($formattedSummary); ?>
    </p>
    <?php if ($userId == $submitterId || is_admin()) : ?>
        <a class="small-button" href="/pages/edit_report.php?id=<?php echo $reportId; ?>"><span>Edit</span></a>
    <?php endif; ?>
</div>