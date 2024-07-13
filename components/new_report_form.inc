<?php
require_once realpath("../../config.php");
require_once realpath("../../db_connect.php");

$selectedFeature = "";



$sqlFeatures = "SELECT id, name FROM features WHERE collections_id IN (" . implode(",", COLLECTION_IDS) . ")";

$resultFeatures = $mysqli->query($sqlFeatures);
if (!$resultFeatures) {
    die("Error retrieving features: " . $mysqli->error);
}
?>
<h1>New Trail Report</h1>

<form action="process_new_report.php" method="post">

    <label for="feature">Select Trail:</label><br><br>
    <select name="feature" id="feature">
        <option value="">Select Feature</option>
        <?php while ($row = $resultFeatures->fetch_assoc()): ?>
            <option value="<?php echo $row["id"]; ?>">
                <?php echo $row["name"]; ?>
            </option>
        <?php endwhile; ?>
    </select>
    <br><br>


    <label for="rating">Overall Condition Rating:</label><br><br>
    <select id="rating" name="rating">
        <?php foreach (OVERALL_RATINGS as $rating_text => $rating_value): ?>
            <option value="<?php echo $rating_value; ?>">
                <?php echo $rating_text; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <label for="summary">Detailed Summary:</label><br><br>
    <textarea name="summary" id="summary" rows="9" cols="50"
        placeholder="Provide a detailed description of your experience on the trail."></textarea>
    <br><br>

    <button type="submit">Submit Report</button>

</form>

<?php
$mysqli->close();
?>