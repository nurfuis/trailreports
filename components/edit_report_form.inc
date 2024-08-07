<?php
require_once realpath("../../db_connect.php");
require_once realpath("../components/user_roles.php");

// $selectedFeature = "";
$reportId = isset($_GET['id']) ? $_GET['id'] : '';

$sqlFeatures = "SELECT id, name FROM features WHERE collections_id IN (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)";
$resultFeatures = $mysqli->query($sqlFeatures);

if (!$resultFeatures) {
  die("Error retrieving features: " + $mysqli->error);
}

$sqlReport = "";

if ($reportId) {
  $sqlReport = "SELECT tr.*, f.name AS trail_name, tr.user_id AS report_creator_id 
               FROM trail_reports tr
               INNER JOIN features f ON tr.feature_id = f.id
               WHERE tr.id = $reportId";

  $resultReport = $mysqli->query($sqlReport);
  if (!$resultReport) {
    die("Error retrieving report: " . $mysqli->error);
  }

  $reportData = $resultReport->fetch_assoc();

  $isAuthorized = ($user_id == $reportData['report_creator_id']) || is_admin();

  if (!$isAuthorized) {
    die("You are not authorized to edit this report.");
  }
}
?>

<div class="trail-report__form">
  <h1>Edit Report</h1>
  <form id="hideReportForm" action="/pages/hide_report.php" method="post" onsubmit="return confirmHideReport()">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="report_id" value="<?php echo $reportData['id']; ?>">
    <button class="small-button" type="submit">Delete Report</button>
  </form>


  <script>
    function confirmHideReport() {
      const reportId = document.getElementById("hideReportForm").elements["report_id"].value;
      if (confirm(`Are you sure you want to hide report?`)) {
        return true;
      } else {
        return false;
      }
    }
  </script>
  <h2>Update Collection</h2>


  <form action="process_edit_report.php" method="post">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="report_id" value="<?php echo $reportData['id']; ?>">

    <label for="feature">Select Trail:</label><br><br>
    <select name="feature" id="feature" required>
      <option value="">-- Select Trail --</option>
      <?php
      while ($row = $resultFeatures->fetch_assoc()) :
      ?>
        <option value="<?php echo $row["id"]; ?>" <?php echo ($reportData['feature_id'] == $row["id"]) ? 'selected' : ''; ?>>
          <?php echo $row["name"]; ?>
        </option>
      <?php endwhile; ?>


    </select>
    <hr>

    <label for="title">Report Title:</label><br><br>
    <input type="text" name="title" id="title" size="30" value="<?php echo $reportData['title']; ?>" placeholder="Enter a descriptive title" required>
    <hr>

    <label class="radio" for="rating-group">Overall Condition Rating:</label>
    <div>
      <?php

      $OVERALL_RATINGS = [
        'Good' => 1,
        'Passable' => 2,
        'Poor' => 3,
        'Impassable' => 4,
        'Gone' => 5
      ];
      foreach ($OVERALL_RATINGS as $rating_text => $rating_value) : ?>
        <label class="k2d-regular" for="<?php echo $rating_value; ?>">
          <input type="radio" id="<?php echo $rating_value; ?>" name="rating" value="<?php echo $rating_value; ?>" <?php echo ($reportData['rating'] == $rating_value) ? 'checked' : ''; ?> required>

          <span><?php echo $rating_text; ?></span>
        </label><br>
      <?php endforeach; ?>
    </div>
    <hr>

    <label for="summary">Detailed Summary:</label>
    <a href="#" class="toggle-guidance"> (more)</a><br>

    <ul class="guidance-list" style="display: none;">
      <li><span>Trail Conditions:</span> Describe the overall condition of the trail (e.g., well-maintained, muddy
        in some sections, overgrown in parts).</li>
    </ul>
    <script>
    </script>
    <br>
    <textarea name="summary" id="summary" class="summary-input" placeholder="Provide a detailed description of your experience on the trail." required><?php echo $reportData['summary']; ?></textarea>
    <br><br>

    <button type="submit">Save Changes</button>
    <input type="hidden" name="time_submitted" value="<?php echo time(); ?>">
  </form>
</div>

<?php
mysqli_close($mysqli);
?>