<?php

$trail_sql = "SELECT id, name FROM features WHERE collections_id IN (" . implode(",", COLLECTION_IDS) . ")";
$trail_result = mysqli_query($mysqli, $trail_sql);

if (!$trail_result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}
?>
<a href="#content"></a>

<div class="main__trail-select regular-padding">
    <form action="#content" method="get" id="trail-select-form">
        <label for="trail-select">Select a Trail:</label>
        <select name="feature_id" id="trail-select">
            <option value="recent">-- Select a Trail --</option>
            <?php while ($trail = mysqli_fetch_assoc($trail_result)): ?>
                <option value="<?php echo $trail['id']; ?>" <?php if (isset($_GET['feature_id']) && $_GET['feature_id'] == $trail['id']): ?> selected <?php endif; ?>>
                    <?php echo $trail['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">View</button>
    </form>

</div>