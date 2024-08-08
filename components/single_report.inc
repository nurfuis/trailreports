<?php

$isUpdated = false;
// $isUpdated = $report['time_updated'] !== $report['created_at'];
$postedOnText = $isUpdated ? 'Updated:' : 'Posted:';

$OVERALL_RATINGS = [
    'Good' => 1,
    'Passable' => 2,
    'Poor' => 3,
    'Impassable' => 4,
    'Gone' => 5
];

$ratings = array_flip($OVERALL_RATINGS);

$time = $report['time_updated'];
$formattedTime = date("F j, Y", strtotime($time));

$title = htmlspecialchars($report['report_title'], ENT_QUOTES, 'UTF-8');
$summary = htmlspecialchars($report['summary'], ENT_QUOTES, 'UTF-8');

$submitterId = $report['user_id'];
$reportId = $report['id'];


$userId = get_user_id();
$geometryType = $report["geometry_type"];
$selected_feature_id = $report['feature_id'];


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
    <!-- <div class="whitespace-container">
        <div class="vertical-divider"></div>

        <div class="scroll-text">scroll</div>
        <div class="vertical-divider"></div>
    </div> -->

    <?php if ($userId == $submitterId || is_admin()) : ?>
        <a class="small-button" href="/pages/edit_report.php?id=<?php echo $reportId; ?>"><span>Edit</span></a>

    <?php endif; ?>


    <div class="map-icon__wrapper">
        <img class="map-icon" src="../assets/images/map-icon-lg.webp">


    </div>


    <h1>Trail Report</h1>



    <div id="map-inset"></div>

    <script>
        const geojsonData = JSON.parse('<?php echo json_encode($geojsonData); ?>');
        const geojsonDataPoints = JSON.parse('<?php echo json_encode($geojsonDataPoints); ?>');

        console.log(geojsonData, geojsonDataPoints)

        function getMapParamsFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            const lat = parseFloat(urlParams.get("lat"));
            const long = parseFloat(urlParams.get("long"));
            const zoom = parseInt(urlParams.get("zoom"), 11);

            // Check if all parameters are valid numbers
            if (isNaN(lat) || isNaN(long) || isNaN(zoom)) {
                console.warn("Invalid lat, long, or zoom in URL");
                return {
                    lat: 36.3315,
                    long: -121.7615,
                    zoom: 11
                };
            }

            return {
                lat,
                long,
                zoom
            };
        }

        var redIcon = L.icon({
            iconUrl: '../assets/images/chevron.png',
            shadowUrl: '../assets/images/chevron-shadow.png',

            iconSize: [64, 64],
            shadowSize: [64, 64],
            iconAnchor: [32, 48],
            shadowAnchor: [26, 40],
            popupAnchor: [-3, -76]
        });

        const {
            lat,
            long,
            zoom
        } = getMapParamsFromUrl();

        var map = L.map("map-inset", {
            renderer: L.canvas({
                tolerance: 8
            }),
            zoomControl: false,
        });

        // const point = L.marker([lat, long], {
        //     icon: redIcon
        // }).addTo(map);

        // point.on("click", function() {
        // showOverlay();
        // });

        // L.control.scale().addTo(map);

        // map.on("load", function() {
        //     setTimeout(function() {
        //         document.getElementById('loading').style.display = 'none';
        //     }, 500);
        // });

        map.setView([lat, long], zoom);


        function getParentWidth(elementId) {
            const element = document.getElementById(elementId);
            const parent = element.parentElement;
            return parent.offsetWidth;
        }

        var width = getParentWidth("map-inset");
        var height = 200;

        document.getElementById("map-inset").style.width = width * 0.8 + "px";
        document.getElementById("map-inset").style.height = height + "px";

        var tileLayer = L.tileLayer("https://tile.opentopomap.org/{z}/{x}/{y}.png", {
            maxZoom: 15,
        });

        // const sourceSpan = document.getElementById('source-span');
        // const fullSource = '<?php echo $source; ?>';

        // sourceSpan.addEventListener('click', function() {
        //     if (sourceSpan.textContent.length <= 13) {
        //         sourceSpan.textContent = fullSource;
        //     } else {
        //         sourceSpan.textContent = sourceSpan.textContent.substring(0, 10) + '...';
        //     }
        // });


        // tileLayer.on('load', function() {
        //     setTimeout(function() {
        //         document.getElementById('loading').style.display = 'none';
        //     }, 500); // Delay for 0.5 seconds

        // });
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

        mapIcon.addEventListener('click', () => {
            if (mapInset.style.display == "none") {
                mapInset.style.display = 'block';
            } else {
                mapInset.style.display = 'none';
            }
        });


        map.setMaxBounds(allBounds);
        // TODO resize map on window resize
    </script>

    <h2><?php echo $report['feature_name']; ?></h2>

    <p><strong>Submitted by:</strong> <?php echo $report['username']; ?></p>
    <p><strong><?php echo $postedOnText; ?></strong> <?php echo $formattedTime; ?></p>


    <h3><?php echo nl2br($title); ?> </h3>

    <p><strong>Rating:</strong> <?php echo $ratings[$report['rating']]; ?></p>
    <p class="indented larger">
        <?php echo nl2br($summary); ?>
    </p>
    <!-- 
    <div class="whitespace-container">
        <div class="vertical-divider"></div>

        <div class="scroll-text">scroll</div>
        <div class="vertical-divider"></div>
    </div> -->
</div>