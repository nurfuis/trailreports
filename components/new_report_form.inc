<?php
require_once realpath("../../db_connect.php");

$selectedFeature = "";
$sqlFeatures = "SELECT id, name FROM features ORDER BY name ASC;";
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
            $selected_feature_id = isset($_GET['id']) ? $_GET['id'] : '';

            while ($row = $resultFeatures->fetch_assoc()):
            ?>
                <option value="<?php echo $row["id"]; ?>" <?php echo ($selected_feature_id == $row["id"]) ? 'selected' : ''; ?>>
                    <?php

                    $shortenedName = substr($row['name'], 0, 34);
                    if (strlen($row['name']) > 34) {
                        $shortenedName .= '...';
                    }
                    echo $shortenedName;
                    ?>
                </option>
            <?php endwhile; ?>
        </select>
        <hr>

        <label for="title">Report Title:</label><br><br>
        <input type="text" name="title" id="title" size="30" placeholder="Enter a descriptive title" required>
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

            foreach ($OVERALL_RATINGS as $rating_text => $rating_value): ?>
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
            <li><span>Date Hiked:</span> Include the date of your hike.</li>
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

            toggleLink.addEventListener("click", function(event) {
                event.preventDefault();
                guidanceList.style.display = guidanceList.style.display === "none" ? "block" : "none";
                toggleLink.textContent = guidanceList.style.display === "none" ? "(more)" : "(less)";
            });
        </script>
        <br>
        <textarea name="summary" id="summary" class="summary-input" placeholder="Date hiked. A detailed description. "
            required></textarea>

        <hr class="hidden">
        <label class="hidden" for="image-input">Images:</label>

        <input class="hidden" type="file" id="image-input" name="images[]" multiple>

        <div class="hidden" id="image-preview"></div>
        <script>
            const imageInput = document.getElementById('image-input');
            const imagePreview = document.getElementById('image-preview');

            imageInput.addEventListener('change', (event) => {
                imagePreview.innerHTML = "";
                const files = event.target.files;

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        const img = new Image();
                        img.src = e.target.result;

                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');


                            var MAX_WIDTH = 300;
                            var MAX_HEIGHT = 300;
                            var width = img.width;
                            var height = img.height;

                            if (width > height) {
                                if (width > MAX_WIDTH) {
                                    height *= MAX_WIDTH / width;
                                    width = MAX_WIDTH;
                                }
                            } else {
                                if (height > MAX_HEIGHT) {
                                    width *= MAX_HEIGHT / height;
                                    height = MAX_HEIGHT;
                                }
                            }
                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(img, 0, 0, width, height);

                            const preview__wrapper = document.createElement('div');
                            imagePreview.appendChild(preview__wrapper);

                            // Create an image element to display the resized image
                            const imgElement = document.createElement('img');
                            imgElement.src = canvas.toDataURL();
                            imgElement.classList.add('preview');
                            imgElement.id = `image-${i}`;
                            imgElement.name = `image-${i}`;
                            preview__wrapper.appendChild(imgElement);

                            const captionInput = document.createElement('input');
                            captionInput.type = 'text';
                            captionInput.placeholder = 'Caption';
                            captionInput.maxLength = 256;
                            captionInput.id = `caption-${i}`;
                            captionInput.name = `caption-${i}`;
                            captionInput.classList.add('full-width')

                            preview__wrapper.appendChild(captionInput);

                        };
                    };

                    reader.readAsDataURL(file);
                }
                const numImages = document.createElement('input');
                numImages.type = "hidden";
                numImages.name = "num_images";
                numImages.value = `${files.length}`;
                imagePreview.appendChild(numImages);
                console.log(imagePreview);
            });
        </script>
        <hr>
        <button class="margin-regular" type="submit">Submit Report</button>
        <input type="hidden" name="time_submitted" value="<?php echo time(); ?>">
    </form>
</div>
<?php
$mysqli->close();
?>