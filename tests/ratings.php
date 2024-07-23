<?php
include_once realpath('../../config.php');

echo OVERALL_RATINGS[1];

foreach (OVERALL_RATINGS as $rating_text => $rating_value): ?>
    <label class="k2d-regular" for="<?php echo $rating_value; ?>">
        <input type="radio" id="<?php echo $rating_value; ?>" name="rating" value="<?php echo $rating_value; ?>" required>

        <span><?php echo $rating_text; ?></span>
    </label><br>


<?php endforeach; ?>