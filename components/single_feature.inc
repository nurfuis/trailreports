<?php
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$ITEMS_PER_PAGE = 5;
$BLURB_LIMIT = 200;
$MAP_ZOOM = 13;
$OVERALL_RATINGS = [
    'Good' => 1,
    'Passable' => 2,
    'Poor' => 3,
    'Impassable' => 4,
    'Gone' => 5
];

$ratings = array_flip($OVERALL_RATINGS);

if (isset($_GET['feature_id'])) {
    $selected_feature_id = $_GET['feature_id'];

    $feature_sql = "SELECT * FROM features WHERE id = $selected_feature_id;";
    $feature_result = mysqli_query($mysqli, $feature_sql);

    if (!$feature_result) {
        echo "Error: " . mysqli_error($mysqli);
        exit;
    }

    $feature = mysqli_fetch_assoc($feature_result);
    if (!$feature) {
        echo "Feature not found.";
        exit;
    }

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
    <div>
        <div class="regular-padding">
            <h1><?php echo $feature['name'] ?></h1>

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

                document.getElementById("map-inset").style.width = width * 0.8 + "px";
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


            <?php
            // map points
            if ($feature['geometry_type'] == 'LineString') {
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
                    $link_url = "./pages/topo_map.php?name=" . $feature['name'] . "&lat=" . $first_latitude . "&long=" . $first_longitude . "&zoom=" . $MAP_ZOOM;
                    if (!empty($source)) {
                        $link_url .= "&source=" . $source;
                    }

                    echo '<p><span>Map:</span> <a href="' . $link_url . '">' . $geometry_string . '</a></p>';
                } else {
                    echo "No polylines found for this feature.";
                }


                while ($polyline = mysqli_fetch_assoc($polyline_result)) {
                    echo '<div class="hidden">';
                    echo "<h4>Segment</h4>";
                    echo "<p>Path: " . $polyline['text_geometry'] . "</p>";
                    echo '</div>';
                }
            } elseif ($feature['geometry_type'] == 'Point') {
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
                    $link_url = "./pages/topo_map.php?name=" . $feature['name'] . "&lat=" . $latitude . "&long=" . $longitude . "&zoom=" . $MAP_ZOOM;

                    echo '<p><span>Map:</span> <a href="' . $link_url . '">' . $geometry_string . '</a></p>';
                } else {
                    echo "No point found for this feature.";
                }

                mysqli_free_result($point_result);
            }


            $sql = "SELECT tr.*, f.name AS trail_name, u.username
            FROM trail_reports tr
            INNER JOIN features f ON tr.feature_id = f.id
            INNER JOIN users u ON tr.user_id = u.user_id
            WHERE f.id = $selected_feature_id AND tr.active = 1";
            ?>

            <hr>

            <h3>Reports:</h3>
            <div class="nav">
                <a href="./pages/add_report.php?id=<?php echo $selected_feature_id ?>">
                    Submit a Report
                </a>
            </div>
            <div class="trail-reports regular-padding">

                <?php

                $result = mysqli_query($mysqli, $sql);

                if (!$result) {
                    echo "Error: " . mysqli_error($mysqli);
                    exit;
                }

                if (mysqli_num_rows($result) === 0) {
                    echo "<p>There are currently no trail reports for this selection.</p>";
                    //
                } else {
                    $is_descending = ($order_by === "tr.created_at DESC" || $order_by === "tr.rating ASC, tr.created_at DESC");
                    $count = 1;
                    while ($report = mysqli_fetch_assoc($result)) {
                        $reportNumber = ($is_descending) ? ($current_page - 1) * $ITEMS_PER_PAGE + $count : ($total_reports - ($current_page - 1) * $ITEMS_PER_PAGE - $count + 1);
                        $count++;

                        $isUpdated = $report['time_updated'] !== $report['created_at']; // Check if updated time is different
                        $postedOnText = $isUpdated ? 'Updated:' : 'Posted:';
                        $time = $report['time_updated'];
                        $formattedTime = date("F j, Y", strtotime($time));

                        $summary = htmlspecialchars($report['summary'], ENT_QUOTES, 'UTF-8');
                        if (strlen($summary) > $BLURB_LIMIT) {
                            $summary = substr($summary, 0, $BLURB_LIMIT) . '...';
                            $showReadMore = true;
                        } else {
                            $showReadMore = false;
                        }

                        echo "<h4>$reportNumber.</h4>";
                        echo "<div class='report-item'>";
                        echo "  <h5><a href='./pages/trail_report.php?id=" . $report['id'] . "'>" . htmlspecialchars($report['title'], ENT_QUOTES, 'UTF-8') . "</a></h5>";
                        echo "  <p><span>Submitted by:</span> " . $report['username'] . "</p>";
                        echo "  <p><span>" . $postedOnText . "</span> " . $formattedTime . "</p>";
                        echo "  <p><span>Rating:</span> " . $ratings[$report['rating']] . "</p>";
                        echo "  <p class='indented'>" . nl2br($summary);
                        if ($showReadMore) : ?>
                            <a href="./pages/trail_report.php?id=<?php echo $report['id']; ?>" class="read-more-btn">read more</a>
                <?php endif;
                        echo "</p>";
                        echo "</div>";
                    }
                    echo '<div class="nav">';
                    echo '<a href="./pages/display_reports.php?filter-by-trail=' . $selected_feature_id . '">View More Reports';
                    echo '</a></div>';
                } ?>
            </div>
        </div>
    <?php
} else {
    echo "Please select a trail to view details.";
}
    ?>