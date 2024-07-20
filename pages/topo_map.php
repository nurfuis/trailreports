<?php $featureName = isset($_GET['name']) ? $_GET['name'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title><?php echo $featureName; ?> - Topo Map</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Julius+Sans+One&family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/fonts.css" />

    <!-- Leaflet Code -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
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
        function getMapParamsFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            const lat = parseFloat(urlParams.get("lat"));
            const long = parseFloat(urlParams.get("long"));
            const zoom = parseInt(urlParams.get("zoom"), 11);

            // Check if all parameters are valid numbers
            if (isNaN(lat) || isNaN(long) || isNaN(zoom)) {
                console.error("Invalid lat, long, or zoom in URL");
                return { lat: 36.3315, long: -121.7615, zoom: 11 };
            }

            return { lat, long, zoom };
        }

        // ## Leaflet Code ## //
        const { lat, long, zoom } = getMapParamsFromUrl();

        var map = L.map("map");
        map.on("load", function () {
            setTimeout(function () {
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
            maxZoom: 19,
            attribution: '&copy; <a href="https://opentopomap.org">OpenTopoMap</a> (CC-BY-SA)',
        });

        // Add event listener to tile layer 'load' event
        tileLayer.on('load', function () {
            document.getElementById('loading').style.display = 'none';

        });

        // Add the tile layer to the map
        tileLayer.addTo(map);



        // map.whenReady(isLoaded);
        // function isLoaded() {
        //     setTimeout(function () {
        //         document.getElementById('loading').style.display = 'none';
        //     }, 500); // Delay for 0.5 seconds
        // }
    </script>

    <div class="overlay">
        <div>
            <h2><?php echo $featureName; ?></h2>

            <p id="feature-location">
                <script>document.getElementById('feature-location').innerHTML = `${lat} ${long}`;</script>
            </p>
            <button id="close-overlay">Hide</button>
        </div>
    </div>
    <script>
        const overlay = document.querySelectorAll('.overlay')[0];
        const closeButton = document.getElementById('close-overlay');

        function showOverlay() {
            overlay.style.display = 'block';
        }

        function hideOverlay() {
            overlay.style.display = 'none';
        }
        showOverlay();
        closeButton.addEventListener('click', hideOverlay);
    </script>
</body>

</html>