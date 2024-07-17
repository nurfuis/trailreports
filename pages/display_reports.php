<?php

$page_title = "Trail Reports";
$page_css = "../assets/css/style.css";

session_start();
require_once realpath("../../config.php");

include_once realpath("../components/head.inc");
include_once realpath("../layouts/wide.inc");

require_once realpath("../../db_connect.php");
?>
<div class="trail-reports">
  <h2>Trail Reports</h2>
  <form action="" method="get"> <label for="sort-by">Sort By:</label>
    <select name="sort-by" id="sort-by">
      <option value="recent" selected>Most Recent</option>
      <option value="oldest">Oldest First</option>
      <option value="rating_high">Rating (High to Low)</option>
      <option value="rating_low">Rating (Low to High)</option>
    </select>
    <input type="submit" value="Sort">
  </form>
  <?php
  // Write the query to select all reports with user information and add report ID
  
  if (isset($_GET['sort-by'])) {
    $sort_by = $_GET['sort-by'];
    switch ($sort_by) {
      case "recent":
        $order_by = "tr.created_at DESC"; // Same order as before
        break;
      case "oldest":
        $order_by = "tr.created_at ASC"; // Ascending order for oldest first
        break;
      case "rating_high":
        $order_by = "tr.rating ASC";
        break;
      case "rating_low":
        $order_by = "tr.rating DESC";
        break;
      default:
        $order_by = "tr.created_at DESC"; // Default to recent
    }
  } else {
    $order_by = "tr.created_at DESC"; // Default if no sorting chosen
  }

  $sql = "SELECT tr.*, f.name AS trail_name, u.username
  FROM trail_reports tr
  INNER JOIN features f ON tr.feature_id = f.id
  INNER JOIN users u ON tr.user_id = u.user_id
  ORDER BY $order_by;";


  $result = mysqli_query($mysqli, $sql);
  $ratings = array_flip(OVERALL_RATINGS);

  // Check for errors
  if (!$result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
  }

  // Check if there are any reports
  if (mysqli_num_rows($result) === 0) {
    echo "<p>There are currently no trail reports.</p>";
  } else {

    // Loop through results and display each report as a separate item
    while ($report = mysqli_fetch_assoc($result)) {
      $summary = substr($report['summary'], 0, BLURB_LIMIT) . '...'; // Truncate summary
  
      echo "<div class='report-item'>";
      echo "  <h4><a href='./trail_report.php?id=" . $report['id'] . "'>" . $report['title'] . "</a></h4>";
      echo "  <p><span>Trail:</span> " . $report['trail_name'] . "</a></p>";
      echo "  <p><span>Rating:</span> " . $ratings[$report['rating']] . "</p>";

      echo "  <p><span>Submitted on:</span> " . date("Y-m-d", strtotime($report['created_at'])) . "</p>";
      $summary = $report['summary'];

      if (strlen($summary) > BLURB_LIMIT) {
        $summary = substr($summary, 0, BLURB_LIMIT) . '...'; // Truncate and add ellipsis
        $showReadMore = true; // Flag to indicate truncation
      } else {
        $showReadMore = false; // Flag remains false if not truncated
      }
      echo "  <p class='indented'>" . nl2br($summary);
      ;

      if ($showReadMore): ?>
        <a href="./trail_report.php?id=<?php echo $report['id']; ?>" class="read-more-btn">read more</a>
      <?php endif;
      echo "</p>";
      echo "</div>";
    }
  }
  mysqli_close($mysqli);

  echo "</div>";
  include_once realpath("../components/tail.inc");

  ?>