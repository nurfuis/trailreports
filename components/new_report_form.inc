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
<div class="trail-report__form">
<h1>New Trail Report</h1>

<form action="process_new_report.php" method="post">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

    <label for="title">Report Title:</label><br><br>
    <input type="text" name="title" id="title" placeholder="Enter a descriptive title"><br><br>



    <label for="feature">Select Trail:</label><br><br>
    <select name="feature" id="feature">
        <option value="">-- Select Trail --</option>
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

    <br><br><br>
    <label for="summary">Detailed Summary:</label>
    <a href="#" class="toggle-guidance"> (more)</a><br>

    <ul class="guidance-list" style="display: none;">
        <li>Trail Conditions: Describe the overall condition of the trail (e.g., well-maintained, muddy in some
            sections, overgrown in parts).</li>
        <li>Obstacles: Report any obstacles encountered (e.g., downed trees, washed-out sections, wildlife sightings).
        </li>
        <li>Scenery Highlights: Share any particularly scenic views or interesting landmarks on the trail.</li>
        <li>Gear Recommendations: Mention any specific gear that might be helpful for hikers (e.g., hiking poles for
            steep sections, insect repellent for areas with mosquitos).</li>
        <li>Difficulty Level: Briefly describe the difficulty of the trail (e.g., easy, moderate, challenging) and any
            specific sections that may be more difficult.</li>
        <li>Additional Tips: Share any additional tips or insights that may be helpful for future hikers (e.g., parking
            availability, best time of day to hike, water or camps along the trail).</li>
    </ul>
    <script>
        const toggleLink = document.querySelector(".toggle-guidance");
        const guidanceList = document.querySelector(".guidance-list");

        toggleLink.addEventListener("click", function (event) {
            event.preventDefault();
            guidanceList.style.display = guidanceList.style.display === "none" ? "block" : "none";
            toggleLink.textContent = guidanceList.style.display === "none" ? "(more)" : "(less)";
        });
    </script>
    <br>
    <textarea name="summary" id="summary" class="summary-input"
        placeholder="Provide a detailed description of your experience on the trail."></textarea>
    <br><br>

    <button type="submit">Submit Report</button>

</form>
</div>
<?php
$mysqli->close();
?>