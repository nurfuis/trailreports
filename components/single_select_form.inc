<?php
$trail_sql = "SELECT id, name FROM features WHERE collections_id IN ($collection)";
$trail_result = mysqli_query($mysqli, $trail_sql);


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
            <form id="trail-select-form" method="get">
                <?php while ($collection = mysqli_fetch_assoc($collections_result)): ?>
                    <input type="radio" name="collection" value="<?php echo $collection['id']; ?>"
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