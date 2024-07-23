<?php
require_once realpath("../../config.php");
require_once realpath("../../db_connect.php");

$selectedFeature = "";
$reportId = isset($_GET['id']) ? $_GET['id'] : '';

$sqlFeatures = "SELECT id, name FROM features WHERE collections_id IN (1)";
$resultFeatures = $mysqli->query($sqlFeatures);
if (!$resultFeatures) {
  die("Error retrieving features: " + $mysqli->error);
}

$sqlReport = "";

// Check if a report ID is present and user is logged in
if ($reportId && isset($user_id)) {
  $sqlReport = "SELECT tr.*, f.name AS trail_name FROM trail_reports tr
                INNER JOIN features f ON tr.feature_id = f.id
                WHERE tr.id = $reportId AND tr.user_id = $user_id";
  $resultReport = $mysqli->query($sqlReport);
  if (!$resultReport) {
    die("Error retrieving report: " . $mysqli->error);
  }
}

// Check if report exists and display an error message if not
if (!$resultReport || $resultReport->num_rows == 0) {
  die("Report not found or you are not authorized to edit this report.");
}

$reportData = $resultReport->fetch_assoc();
?>

<div class="trail-report__form">
  <h1>Edit Report</h1>
  <h2>Submit to Collection</h2>
  <form action="process_edit_report.php" method="post">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="report_id" value="<?php echo $reportData['id']; ?>">

    <label for="feature">Select Trail:</label><br><br>
    <select name="feature" id="feature" required>
      <option value="">-- Select Trail --</option>
      <?php
      while ($row = $resultFeatures->fetch_assoc()):
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
      <?php foreach (OVERALL_RATINGS as $rating_text => $rating_value): ?>
        <label class="k2d-regular" for="<?php echo $rating_value; ?>">
          <input type="radio" id="<?php echo $rating_value; ?>" name="rating" value="<?php echo $rating_value; ?>"
            <?php echo ($reportData['rating'] == $rating_value) ? 'checked' : ''; ?> required>

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
    <textarea name="summary" id="summary" class="summary-input"
              placeholder="Provide a detailed description of your experience on the trail." required><?php echo $reportData['summary']; ?></textarea>
    <br><br>

    <button type="submit">Save Changes</button>
    <input type="hidden" name="time_submitted" value="<?php echo time(); ?>">
  </form>
</div>

<?php
    mysqli_close($mysqli);
    ?>