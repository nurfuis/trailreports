<div class="padding-regular">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <h2>Add Trail</h2>
        <label for="trail_name">Trail Name:</label>
        <input type="text" id="trail_name" name="trail_name" required><br>
        <label for="location">Location:</label>
        <input type="text" id="location" name="location"><br>
        <label for="distance">Distance (miles):</label>
        <input type="number" id="distance" name="distance" step="0.1"><br>
        <label for="elevation_gain">Elevation Gain (ft):</label>
        <input type="number" id="elevation_gain" name="elevation_gain"><br>
        <label for="elevation_loss">Elevation Loss (ft):</label>
        <input type="number" id="elevation_loss" name="elevation_loss"><br>
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="5"></textarea><br>
        <label for="image">Image (optional):</label>
        <input type="file" id="image" name="image"><br>
        <button type="submit">Add Trail</button>
    </form>
</div>