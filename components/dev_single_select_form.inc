<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_collections = $_POST['collections'] ?? [1];

    // Now you can use $selected_collections for updating your trail list query
    $collection_ids = implode(',', $selected_collections);
    $trail_sql = "SELECT id, name FROM features WHERE collections_id IN ($collection_ids)";
    $trail_result = mysqli_query($mysqli, $trail_sql);

    if (!$trail_result) {
        echo "Error: " . mysqli_error($mysqli);
        exit;
    }
} else {

    $trail_sql = "SELECT id, name FROM features WHERE collections_id IN (1)";
    $trail_result = mysqli_query($mysqli, $trail_sql);
}

if (!$trail_result) {
    echo "Error: " . mysqli_error($mysqli);
    exit;
}

$collections_sql = "SELECT id, name FROM collections";
$collections_result = mysqli_query($mysqli, $collections_sql);

?>

<div class="main__trail-select regular-padding">
    <form action="#trail-select-form" method="get" id="trail-select-form">
        <label for="trail-select">Select a Trail:</label>
        <select name="feature_id" id="trail-select">
            <option value="recent">-- Latest Updates --</option>
            <?php while ($trail = mysqli_fetch_assoc($trail_result)): ?>
                <option value="<?php echo $trail['id']; ?>" <?php if (isset($_GET['feature_id']) && $_GET['feature_id'] == $trail['id']): ?> selected <?php endif; ?>>
                    <?php echo $trail['name']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">View</button>
    </form>
    <div>
        <div class="advanced-search"><a id="advanced-search" href="#">Show More Features</a></div>
        <div id="advanced-search-form" style="display: none;">
            <form id="trail-select-form" method="post">
                <?php while ($collection = mysqli_fetch_assoc($collections_result)): ?>
                    <input type="checkbox" name="collections[]" value="<?php echo $collection['id']; ?>"
                        id="<?php echo $collection['id']; ?>">
                    <label for="<?php echo $collection['id']; ?>"><?php echo $collection['name']; ?></label>
                <?php endwhile; ?>
                <button type="submit">Update</button>
            </form>
        </div>
        <script>
            const advancedSearch = document.getElementById('advanced-search');
            const advancedSearchForm = document.getElementById('advanced-search-form');

            advancedSearch.addEventListener('click', () => {
                if (advancedSearchForm.style.display === 'none') {
                    advancedSearchForm.style.display = 'block';
                } else {
                    advancedSearchForm.style.display = 'none';
                }
            });
        </script>
    </div>
</div>