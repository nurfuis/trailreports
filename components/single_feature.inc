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
    ?>
    <div>
        <div class="regular-padding">
            <h2><?php echo $feature['name'] ?></h2>

            <div class="nav">
                <a href="./pages/add_report.php?id=<?php echo $selected_feature_id ?>">
                    Submit a Report
                </a>
            </div>

            <?php
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

                    echo '<p><span>Location:</span> <a href="' . $link_url . '">' . $geometry_string . '</a></p>';
                } else {
                    echo "No polylines found for this feature.";
                }


                while ($polyline = mysqli_fetch_assoc($polyline_result)) {
                    echo '<div class="hidden">';
                    echo "<h4>Segment</h4>";
                    echo "<p>Path: " . $polyline['text_geometry'] . "</p>";
                    echo '</div>';
                }
            }
            mysqli_free_result($polyline_result);

            if (isset($_GET['sort-by'])) {
                $sort_by = $_GET['sort-by'];
                switch ($sort_by) {
                    case "recent":
                        $order_by = "tr.created_at DESC";
                        break;
                    case "oldest":
                        $order_by = "tr.created_at ASC";
                        break;
                    case "rating_high":
                        $order_by = "tr.rating ASC, tr.created_at DESC";
                        break;
                    case "rating_low":
                        $order_by = "tr.rating DESC, tr.created_at DESC";
                        break;
                    default:
                        $order_by = "tr.created_at DESC";
                }
            } else {
                $order_by = "tr.created_at DESC";
            }
            $selected_trail = isset($_GET['feature_id']) ? $_GET['feature_id'] : "recent";

            $date_range_sql = "";

            if (isset($_GET['date-range']) && $_GET['date-range'] != "all") {
                switch ($_GET['date-range']) {
                    case "day":
                        $date_range_sql = " AND tr.created_at >= CURDATE() - INTERVAL 1 DAY";
                        break;
                    case "week":
                        $date_range_sql = " AND tr.created_at >= CURDATE() - INTERVAL 1 WEEK";
                        break;
                    case "month":
                        $date_range_sql = " AND tr.created_at >= CURDATE() - INTERVAL 1 MONTH";
                        break;
                    case "year":
                        $date_range_sql = " AND tr.created_at >= CURDATE() - INTERVAL 1 YEAR";
                        break;
                    default:
                        // Handle invalid selection or no selection
                        break;
                }
            }

            $sql = "SELECT tr.*, f.name AS trail_name, u.username
            FROM trail_reports tr
            INNER JOIN features f ON tr.feature_id = f.id
            INNER JOIN users u ON tr.user_id = u.user_id
            WHERE f.id = $selected_feature_id AND tr.active = 1";

            if (isset($_GET['filter-by-trail']) && $_GET['filter-by-trail'] != "all") {
                $selected_trail = $_GET['filter-by-trail'];
                $sql .= " WHERE tr.feature_id = $selected_trail"; // Filter by trail ID
            }
            $sql .= $date_range_sql;
            $sql .= " ORDER BY $order_by";
            $sql .= " LIMIT $ITEMS_PER_PAGE;";
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const sortBySelect = document.getElementById("sort-by");
                    const dateRangeSelect = document.getElementById("date-range");

                    sortBySelect.addEventListener("change", function () {
                        this.form.submit();
                    });

                    dateRangeSelect.addEventListener("change", function () {
                        this.form.submit();
                    });

                });
            </script>
            <p class="hidden"><span>Distance:</span> </p>
            <p class="hidden"><span>Management Area:</span> <?php echo $feature["management_area_id"]; ?></p>
            <hr>
            <h3>Reports:</h3>

            <div class="trail-reports regular-padding">
                <?php
                $result = mysqli_query($mysqli, $sql);
                if (!$result) {
                    echo "Error: " . mysqli_error($mysqli);
                    exit;
                }
                if (mysqli_num_rows($result) === 0) {
                    echo "<p>There are currently no trail reports for this selection.</p>";
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

                        $summary = $report['summary'];
                        if (strlen($summary) > $BLURB_LIMIT) {
                            $summary = substr($summary, 0, $BLURB_LIMIT) . '...';
                            $showReadMore = true;
                        } else {
                            $showReadMore = false;
                        }

                        echo "<h4>$reportNumber.</h4>";
                        echo "<div class='report-item'>";
                        echo "  <h5><a href='./pages/trail_report.php?id=" . $report['id'] . "'>" . $report['title'] . "</a></h5>";
                        echo "  <p><span>Submitted by:</span> " . $report['username'] . "</p>";
                        echo "  <p><span>" . $postedOnText . "</span> " . $formattedTime . "</p>";
                        echo "  <p><span>Rating:</span> " . $ratings[$report['rating']] . "</p>";
                        echo "  <p class='indented'>" . nl2br($summary);
                        if ($showReadMore): ?>
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
