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

$summary = htmlspecialchars($report['summary'], ENT_QUOTES, 'UTF-8');

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
?>


<div class="single-report">

    <?php if ($userId == $submitterId || is_admin()) : ?>
        <a class="small-button" href="/pages/edit_report.php?id=<?php echo $reportId; ?>"><span>Edit</span></a>
    <?php endif; ?>


    <div class="map-icon__wrapper">
        <img class="map-icon" src="../assets/images/map-icon-lg.webp">
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

        // const width = getParentWidth("map-inset");
        // const height = 200;

        // document.getElementById("map-inset").style.width = width * 0.8 + "px";
        // document.getElementById("map-inset").style.height = height + "px";


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

        const mapIcon = document.querySelector('.map-icon');
        const mapInset = document.getElementById('map-inset');

        mapIcon.addEventListener('click', () => {});

        map.setMaxBounds(allBounds);

        window.addEventListener('resize', () => {
            const mapContainer = document.getElementById('map-inset');
            mapContainer.style.width = '80%';
            mapContainer.style.height = '200px';

            map.invalidateSize();
        });
    </script>

    <h2><?php echo $report['feature_name']; ?></h2>

    <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
    <p><strong><?php echo $postedOnText; ?></strong> <?php echo $updateTimeFormat; ?></p>


    <h3><?php echo nl2br($title); ?> </h3>

    <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
    <p class="indented larger">
        <?php echo nl2br($summary); ?>
    </p>

</div>