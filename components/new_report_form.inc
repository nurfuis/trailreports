<?php
require_once realpath("../config.php");
require_once realpath("../db_connect.php");

$selectedFeature = "";
$quotedIds = array_map(function ($id) {
    return "'$id'";  // Wrap each ID in quotes
}, COLLECTION_IDS);

$sqlFeatures = "SELECT id, name FROM features WHERE collections_id IN ('" . implode("','", COLLECTION_IDS) . "')";

$resultFeatures = $mysqli->query($sqlFeatures);
if (!$resultFeatures) {
    die("Error retrieving features: " . $mysqli->error);
}
?>
<div class="trail-report__form">
    <h1>New Report</h1>
    <h2>Submit to Collection</h2>
    <form action="process_new_report.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">


        <label for="feature">Select Trail:</label><br><br>
        <select name="feature" id="feature" required>
            <option value="">-- Select Trail --</option>
            <?php
            // Check if a trail ID is present in the URL parameter
            $selectedTrailId = isset($_GET['id']) ? $_GET['id'] : '';

            while ($row = $resultFeatures->fetch_assoc()):
                ?>
                <option value="<?php echo $row["id"]; ?>" <?php echo ($selectedTrailId == $row["id"]) ? 'selected' : ''; ?>>
                    <?php echo $row["name"]; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <hr>

        <label for="title">Report Title:</label><br><br>
        <input type="text" name="title" id="title" size="30" placeholder="Enter a descriptive title" required>
        <hr>

        <label class="radio" for="rating-group">Overall Condition Rating:</label>
        <div>
            <?php foreach (OVERALL_RATINGS as $rating_text => $rating_value): ?>
                <label class="k2d-regular" for="<?php echo $rating_value; ?>">
                    <input type="radio" id="<?php echo $rating_value; ?>" name="rating" value="<?php echo $rating_value; ?>"
                        required>

                    <span><?php echo $rating_text; ?></span>
                </label><br>
            <?php endforeach; ?>
        </div>
        <hr>
        <label for="summary">Detailed Summary:</label>
        <a href="#" class="toggle-guidance"> (more)</a><br>

        <ul class="guidance-list" style="display: none;">
            <li><span>Trail Conditions:</span> Describe the overall condition of the trail (e.g., well-maintained, muddy
                in some
                sections, overgrown in parts).</li>
            <li><span>Obstacles:</span> Report any obstacles encountered (e.g., downed trees, washed-out sections,
                wildlife
                sightings).
            </li>
            <li><span>Scenery Highlights:</span> Share any particularly scenic views or interesting landmarks on the
                trail.</li>
            <li><span>Gear Recommendations:</span> Mention any specific gear that might be helpful for hikers (e.g.,
                hiking poles for
                steep sections, insect repellent for areas with mosquitos).</li>
            <li><span>Difficulty Level:</span> Briefly describe the difficulty of the trail (e.g., easy, moderate,
                challenging) and
                any
                specific sections that may be more difficult.</li>
            <li><span>Additional Tips:</span> Share any additional tips or insights that may be helpful for future
                hikers (e.g.,
                parking
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
            placeholder="Provide a detailed description of your experience on the trail." required></textarea>
        <br><br>

        <button type="submit">Submit Report</button>
        <input type="hidden" name="time_submitted" value="<?php echo time(); ?>">
    </form>
</div>
<?php
$mysqli->close();
?>