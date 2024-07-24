<?php
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
                    $link_url = "./pages/topo_map.php?name=" . $feature['name'] . "&lat=" . $first_latitude . "&long=" . $first_longitude . "&zoom=13";
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
            $numToShow = 5;

            $sql = "SELECT tr.*, f.name AS trail_name, u.username
                  FROM trail_reports tr
                  INNER JOIN features f ON tr.feature_id = f.id
                  INNER JOIN users u ON tr.user_id = u.user_id WHERE f.id = $selected_feature_id";

            if (isset($_GET['filter-by-trail']) && $_GET['filter-by-trail'] != "all") {
                $selected_trail = $_GET['filter-by-trail'];
                $sql .= " WHERE tr.feature_id = $selected_trail"; // Filter by trail ID
            }
            $sql .= $date_range_sql;
            $sql .= " ORDER BY $order_by";
            $sql .= " LIMIT $numToShow;";
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const sortBySelect = document.getElementById("sort-by");
                    const dateRangeSelect = document.getElementById("date-range");

                    sortBySelect.addEventListener("change", function () {
                        this.form.submit(); // Submits form for sort by
                    });

                    dateRangeSelect.addEventListener("change", function () {
                        this.form.submit(); // Submits form for date range
                    });

                });
            </script>
            <p class="hidden"><span>Distance:</span> </p>
            <p class="hidden"><span>Management Area:</span> <?php echo $feature["management_area_id"]; ?></p>
            <hr>
            <h3>Reports:</h3>
            <div>
                <form action="" method="get">
                    <input type='hidden' name='feature_id' value='<?php echo $selected_feature_id ?>'>
                    <div class="hidden">
                        <label for="sort-by">Sort By:</label>
                        <select name="sort-by" id="sort-by">
                            <option value="recent" <?php if ($order_by == "tr.created_at DESC")
                                echo "selected"; ?>>Most Recent
                            </option>
                            <option value="oldest" <?php if ($order_by == "tr.created_at ASC")
                                echo "selected"; ?>>Oldest First
                            </option>
                            <option value="rating_high" <?php if ($order_by == "tr.rating ASC, tr.created_at DESC")
                                echo "selected"; ?>>
                                Rating
                                (High to Low)
                            </option>
                            <option value="rating_low" <?php if ($order_by == "tr.rating DESC, tr.created_at DESC")
                                echo "selected"; ?>>Rating
                                (Low to High)
                            </option>
                        </select>
                    </div>
                    <div class="hidden">
                        <label for="date-range">Date Range:</label>

                        <select name="date-range" id="date-range">
                            <option value="all" <?php echo (isset($_GET['date-range']) && $_GET['date-range'] == "all") ? "selected" : ""; ?>>
                                All Time</option>
                            <option value="day" <?php echo (isset($_GET['date-range']) && $_GET['date-range'] == "day") ? "selected" : ""; ?>>
                                Past Day</option>
                            <option value="week" <?php echo (isset($_GET['date-range']) && $_GET['date-range'] == "week") ? "selected" : ""; ?>>
                                Past Week</option>
                            <option value="month" <?php echo (isset($_GET['date-range']) && $_GET['date-range'] == "month") ? "selected" : ""; ?>>Past Month</option>
                            <option value="year" <?php echo (isset($_GET['date-range']) && $_GET['date-range'] == "year") ? "selected" : ""; ?>>
                                Past Year</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="trail-reports regular-padding">

                <?php

                $result = mysqli_query($mysqli, $sql);
                $OVERALL_RATINGS = [
                    'Good' => 1,
                    'Passable' => 2,
                    'Poor' => 3,
                    'Impassable' => 4,
                    'Gone' => 5
                ];
                $ratings = array_flip($OVERALL_RATINGS);

                if (!$result) {
                    echo "Error: " . mysqli_error($mysqli);
                    exit;
                }

                if (mysqli_num_rows($result) === 0) {
                    echo "<p>There are currently no trail reports.</p>";
                } else {
                    $count = 1;
                    while ($report = mysqli_fetch_assoc($result)) {
                        $summary = substr($report['summary'], 0, BLURB_LIMIT) . '...';
                        $isUpdated = $report['time_updated'] !== $report['created_at']; // Check if updated time is different
                        $postedOnText = $isUpdated ? 'Updated:' : 'Posted:';
                        $reportNumber = $count;
                        $count++;
                        echo "<h4>$reportNumber.</h4>";
                        echo "<div class='report-item'>";
                        echo "  <h5><a href='./pages/trail_report.php?id=" . $report['id'] . "'>" . $report['title'] . "</a></h5>";
                        echo "  <p><span>Rating:</span> " . $ratings[$report['rating']] . "</p>";
                        echo "  <p><span>Submitted by:</span> " . $report['username'] . "</p>";

                        $time = $report['time_updated'];
                        $formattedTime = date("F j, Y", strtotime($time));
                        echo "  <p><strong>" . $postedOnText . "</strong> " . $formattedTime . "</p>";
                        $summary = $report['summary'];
                        $BLURB_LIMIT = 200;
                        if (strlen($summary) > $BLURB_LIMIT) {
                            $summary = substr($summary, 0, $BLURB_LIMIT) . '...';
                            $showReadMore = true;
                        } else {
                            $showReadMore = false;
                        }
                        echo "  <p class='indented'>" . nl2br($summary);
                        ;

                        if ($showReadMore): ?>
                            <a href="./pages/trail_report.php?id=<?php echo $report['id']; ?>" class="read-more-btn">read more</a>
                        <?php endif;
                        echo "</p>";
                        echo "</div>";
                    }
                } ?>

                <div class="nav">.
                    <a href="./pages/display_reports.php?filter-by-trail=<?php echo $selected_feature_id ?>">
                        View More Reports
                    </a>
                </div>
            </div>
        </div>
        <?php
} else {
    echo "Please select a trail to view details.";
}
?>