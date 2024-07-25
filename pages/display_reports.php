<?php
session_start();
require_once realpath("../../db_connect.php");

$page_title = "Trail Reports";
$page_css = "../assets/css/style.css";
$BLURB_LIMIT = 500;

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

// get the trails for which there are active reports
$trail_sql = "SELECT DISTINCT f.name AS trail_name, f.id AS trail_id 
              FROM trail_reports tr
              INNER JOIN features f ON tr.feature_id = f.id
              WHERE tr.active = 1;";
$trail_result = mysqli_query($mysqli, $trail_sql);

$num_trails = mysqli_num_rows($trail_result);

// Check total reports and set flag if no reports are found
$no_trails = false;
if ($num_trails === 0) {
  $no_trails = true;
}

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

$selected_trail = isset($_GET['filter-by-trail']) ? $_GET['filter-by-trail'] : "all";


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


$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

$sql_count = "SELECT COUNT(*) AS total_reports
             FROM trail_reports tr
             INNER JOIN features f ON tr.feature_id = f.id
             WHERE tr.active = 1";

if (isset($_GET['filter-by-trail']) && $_GET['filter-by-trail'] != "all") {
  $selected_trail = (int) $_GET['filter-by-trail'];
  $stmt = mysqli_prepare($mysqli, $sql_count . " AND tr.feature_id = ?");
  mysqli_stmt_bind_param($stmt, "i", $selected_trail);
  mysqli_stmt_execute($stmt);
  $count_result = mysqli_stmt_get_result($stmt);
  mysqli_stmt_close($stmt);
} else {
  $count_result = mysqli_query($mysqli, $sql_count);
}

if ($count_result) {
  $count_row = mysqli_fetch_assoc($count_result);
  $total_reports = $count_row['total_reports'];
} else {
  echo "Error: " . mysqli_error($mysqli);
  exit;
}

$no_reports = false;
if ($total_reports == 0) {
  $no_reports = true;
}

$total_pages = ceil($total_reports / $items_per_page);

$sql = "SELECT tr.*, f.name AS trail_name, u.username
        FROM trail_reports tr 
        INNER JOIN features f ON tr.feature_id = f.id
        INNER JOIN users u ON tr.user_id = u.user_id WHERE tr.active = 1";

if (isset($_GET['filter-by-trail']) && $_GET['filter-by-trail'] != "all") {
  $selected_trail = $_GET['filter-by-trail'];
  $sql .= " AND tr.feature_id = $selected_trail"; // Filter by trail ID
}

$sql .= $date_range_sql;
$sql .= " ORDER BY $order_by LIMIT $items_per_page OFFSET $offset;";
$result = mysqli_query($mysqli, $sql);

$OVERALL_RATINGS = [
  'Good' => 1,
  'Passable' => 2,
  'Poor' => 3,
  'Impassable' => 4,
  'Gone' => 5
];
$ratings = array_flip($OVERALL_RATINGS);

if (isset($_GET['success']) && $_GET['success'] === 'true') {
  echo '<p style="color: blue;">Your trail report has been successfully added!</p>';
}
?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const filterByTrailSelect = document.getElementById("filter-by-trail");
    const sortBySelect = document.getElementById("sort-by");
    const dateRangeSelect = document.getElementById("date-range");

    const noReportsInput = document.getElementById("no-reports");
    const noTrailsInput = document.getElementById("no-trails");

    filterByTrailSelect.addEventListener("change", function () {
      this.form.submit();
    });

    sortBySelect.addEventListener("change", function () {
      this.form.submit();
    });

    dateRangeSelect.addEventListener("change", function () {
      this.form.submit();
    });

    if (noTrailsInput.value == 1) {
      sortBySelect.disabled = true;
      filterByTrailSelect.disabled = true;
      dateRangeSelect.disabled = true;
    }

    if (noReportsInput.value == 1) {
      sortBySelect.disabled = true;
    }
  });
</script>

<div class="trail-reports">
  <h1>Reports</h1>
  <h2>View Collection</h2>

  <form action="" method="get">

    <input type="hidden" id="no-reports" value="<?php echo $no_reports ?>">
    <input type="hidden" id="no-trails" value="<?php echo $no_trails ?>">

    <div>
      <label for="filter-by-trail">Filter By Trail:</label>
      <select name="filter-by-trail" id="filter-by-trail">
        <option value="all">-- Select All --</option>
        <?php
        if ($trail_result && mysqli_num_rows($trail_result) > 0) {
          
          while ($trail = mysqli_fetch_assoc($trail_result)) {
            $selected = ($selected_trail == $trail['trail_id']) ? "selected" : ""; // Check for selected trail
        
            $shortenedName = substr($trail['trail_name'], 0, 34);
            if (strlen($trail['trail_name']) > 34) {
              $shortenedName .= '...';
            }
            echo "<option value='" . $trail['trail_id'] . "' $selected>" . $shortenedName . "</option>";
          }
        } 
        ?>
      </select>
    </div>

    <div>
      <label for="sort-by">Sort By:</label>
      <select name="sort-by" id="sort-by">
        <option value="recent" <?php if ($order_by == "tr.created_at DESC")
          echo "selected"; ?>>Most Recent</option>
        <option value="oldest" <?php if ($order_by == "tr.created_at ASC")
          echo "selected"; ?>>Oldest First</option>
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

    <div>
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

  <hr>

  <h3>Reports:</h3>
  <?php
  if ($total_pages > 1) {
    $page_links = "";

    if ($current_page > 1) {
      $prev_page = $current_page - 1;
      $page_links .= "<a href='?page=$prev_page" . (isset($_GET['sort-by']) ? "&sort-by=" . $_GET['sort-by'] : "") . (isset($_GET['filter-by-trail']) ? "&filter-by-trail=" . $_GET['filter-by-trail'] : "") . (isset($_GET['date-range']) ? "&date-range=" . $_GET['date-range'] : "") . "'>&laquo; Previous</a> ";
    }

    for ($i = 1; $i <= $total_pages; $i++) {
      $active_class = ($i == $current_page) ? "active" : "";
      $page_links .= "<a class='page-link' href='?page=$i" . (isset($_GET['sort-by']) ? "&sort-by=" . $_GET['sort-by'] : "") . (isset($_GET['filter-by-trail']) ? "&filter-by-trail=" . $_GET['filter-by-trail'] : "") . (isset($_GET['date-range']) ? "&date-range=" . $_GET['date-range'] : "") . "' class='$active_class'>$i</a> ";
    }

    if ($current_page < $total_pages) {
      $next_page = $current_page + 1;
      $page_links .= "<a href='?page=$next_page" . (isset($_GET['sort-by']) ? "&sort-by=" . $_GET['sort-by'] : "") . (isset($_GET['filter-by-trail']) ? "&filter-by-trail=" . $_GET['filter-by-trail'] : "") . (isset($_GET['date-range']) ? "&date-range=" . $_GET['date-range'] : "") . "'>Next &raquo;</a> ";
    }

    echo "<div class='pagination'>$page_links</div>";
  }
  ?>

  <script>
    const currentPage = new URLSearchParams(window.location.search).get('page');
    const pageLinks = document.querySelectorAll('.page-link');

    pageLinks.forEach(link => {
      const linkPage = link.getAttribute('href').split('?page=')[1];
      if (linkPage === currentPage) {
        link.classList.add('active');
      }
    });
  </script>

  <?php
  if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
  }

  if (mysqli_num_rows($result) === 0) {
    echo "<p>There are currently no trail reports for the selected criteria.</p>";
  } else {
    $is_descending = ($order_by === "tr.created_at DESC" || $order_by === "tr.rating ASC, tr.created_at DESC");
    $count = 1;
    while ($report = mysqli_fetch_assoc($result)) {

      $reportNumber = ($is_descending) ? ($current_page - 1) * $items_per_page + $count : ($total_reports - ($current_page - 1) * $items_per_page - $count + 1);
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

      echo "  <h5><a href='./trail_report.php?id=" . $report['id'] . "'>" . $report['title'] . "</a></h5>";

      echo "  <p><span>Trail:</span> " . $report['trail_name'] . "</a></p>";

      echo "  <p><span>Submitted by:</span> " . $report['username'] . "</a></p>";

      echo "  <p><span>" . $postedOnText . "</span> " . $formattedTime . "</p>";

      echo "  <p><span>Rating:</span> " . $ratings[$report['rating']] . "</p>";


      echo "  <p class='indented'>" . nl2br($summary);

      if ($showReadMore) {
        echo '<a href="./trail_report.php?id=' . $report['id'] . '" class="read-more-btn">read more</a>';
      }
      echo "</p>";
      echo "</div>";
    }
  }

  mysqli_close($mysqli);

  echo "</div>";
  include_once realpath("../components/tail.inc");

  ?>