<?php
require_once realpath("../../db_connect.php");


$sql = "SELECT ST_AsGeoJSON(geometry) AS geojson, p.feature_id, f.name AS feature_name
FROM polylines p
INNER JOIN features f ON p.feature_id = f.id;";

$result = mysqli_query($mysqli, $sql);

$geojsonFeatures = [];

while ($row = mysqli_fetch_assoc($result)) {
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
INNER JOIN features f ON p.feature_id = f.id;";

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


$featureName = isset($_GET['name']) ? $_GET['name'] : '';
$source = isset($_GET['source']) ? $_GET['source'] : '';
$shortSource = substr($source, 0, 12);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Trail Map<?php echo " " . $featureName; ?> </title>
    <link rel="icon" href="/favicon.png" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Julius+Sans+One&family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css" />

    <!-- Leaflet Code -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map {
            height: 100vh;
            width: 100%;
        }

        #loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent black background */
            display: flex;
            /* Center content horizontally */
            justify-content: center;
            /* Center content vertically */
            align-items: center;
            /* Center content within container */
            z-index: 999;
            /* Ensure loading indicator is above the map */
        }

        #loading-text {
            color: white;
            font-size: 20px;
        }
    </style>
    <!-- End Leaflet Code -->


</head>

<body>

    <div id="loading">
        <p id="loading-text">Loading Map...</p>
    </div>

    <!-- Leaflet Map -->
    <div id="map"></div>

    <script>
        const geojsonData = JSON.parse('<?php echo json_encode($geojsonData); ?>');
        const geojsonDataPoints = JSON.parse('<?php echo json_encode($geojsonDataPoints); ?>');

        let selectedFeature = false;

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
            selectedFeature = true;
            return {
                lat,
                long,
                zoom
            };
        }

        // ## Leaflet Code ## //
        var redIcon = L.icon({
            iconUrl: '../assets/images/chevron.png',
            shadowUrl: '../assets/images/chevron-shadow.png',

            iconSize: [64, 64], // size of the icon
            shadowSize: [64, 64], // size of the shadow
            iconAnchor: [32, 48], // point of the icon which will correspond to marker's location
            shadowAnchor: [26, 40], // the same for the shadow
            popupAnchor: [-3, -76] // point from which the popup should open relative to the iconAnchor
        });

        const {
            lat,
            long,
            zoom
        } = getMapParamsFromUrl();

        var map = L.map("map", {
            renderer: L.canvas({
                tolerance: 8
            })
        });

        console.log(selectedFeature)

        if (selectedFeature) {

            const point = L.marker([lat, long], {
                icon: redIcon
            }).addTo(map);

            point.on("click", function() {
                showOverlay();
            });

        }

        L.control.scale().addTo(map);

        map.on("load", function() {
            setTimeout(function() {
                document.getElementById('loading').style.display = 'none';
            }, 500);
        });

        map.setView([lat, long], zoom);

        // Get the window width and height
        var width = window.innerWidth || document.documentElement.clientWidth;
        var height = window.innerHeight || document.documentElement.clientHeight;

        // Set the map container size
        document.getElementById("map").style.width = width + "px";
        document.getElementById("map").style.height = height + "px";

        var tileLayer = L.tileLayer("https://tile.opentopomap.org/{z}/{x}/{y}.png", {
            maxZoom: 15,
            attribution: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a> <span class="source" id="source-span"><span class="source-toggle" id="source-span"> <?php echo $shortSource; ?></span>',
        });

        tileLayer.on('load', function() {
            setTimeout(function() {
                document.getElementById('loading').style.display = 'none';
            }, 500); // Delay for 0.5 seconds

        });
        tileLayer.addTo(map);


        geojsonData.features.forEach(feature => {
            const popup = feature.properties.feature_name;

            L.geoJSON(feature, {
                style: {
                    color: 'red'
                }
            }).bindPopup(function(layer) {
                return popup;
            }).addTo(map);
        });

        var geojsonMarkerOptions = {
            radius: 6,
            fillColor: "#ff7800",
            color: "#000",
            weight: 1,
            opacity: 1,
            fillOpacity: 0.8
        };

        geojsonDataPoints.features.forEach(feature => {
            const popup = feature.properties.feature_name;

            L.geoJSON(feature, {
                pointToLayer: function(feature, latlng) {
                    return L.circleMarker(latlng, geojsonMarkerOptions);
                }

            }).bindPopup(function(layer) {
                return popup;
            }).addTo(map);
        });
    </script>



    <div class="overlay">
        <div>
            <h2><?php echo $featureName; ?></h2>

            <p id="feature-location">
                <script>
                    document.getElementById('feature-location').innerHTML = `${lat} ${long}`;
                </script>
            </p>
            <button id="close-overlay">Hide</button>
        </div>
    </div>



    <script>
        const sourceSpan = document.getElementById('source-span');
        const fullSource = '<?php echo $source; ?>'; // Store the full source in a variable

        sourceSpan.addEventListener('click', function() {
            if (sourceSpan.textContent.length <= 13) {
                sourceSpan.textContent = fullSource;
            } else {
                sourceSpan.textContent = sourceSpan.textContent.substring(0, 10) + '...';
            }
        });


        const overlay = document.querySelectorAll('.overlay')[0];
        const closeButton = document.getElementById('close-overlay');

        function showOverlay() {
            overlay.style.display = 'block';
        }

        function hideOverlay() {
            overlay.style.display = 'none';
        }
        closeButton.addEventListener('click', hideOverlay);
    </script>
</body>

</html>