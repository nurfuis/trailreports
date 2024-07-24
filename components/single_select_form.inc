<?php
$trail_sql = "SELECT id, name FROM features WHERE collections_id IN ($collection)";
$trail_result = mysqli_query($mysqli, $trail_sql);


$collections_sql = "SELECT id, name FROM collections";
$collections_result = mysqli_query($mysqli, $collections_sql);

?>

<div class="main__trail-select regular-padding">
    <form action="#trail-select-form" method="get" id="trail-select-form">
        <input hidden name="collection" value="<?php echo $collection ?>">
        <label for="trail-select">Select a Trail:</label>
        <select name="feature_id" id="trail-select">
            <option value="recent">-- Latest Updates --</option>
            <?php while ($trail = mysqli_fetch_assoc($trail_result)): ?>
                <option value="<?php echo $trail['id']; ?>" <?php if (isset($_GET['feature_id']) && $_GET['feature_id'] == $trail['id']): ?> selected <?php endif; ?>>
                    <?php

                    $shortenedName = substr($trail['name'], 0, 34);
                    if (strlen($trail['name']) > 34) {
                        $shortenedName .= '...';
                    }
                    echo $shortenedName;
                    ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">View</button>
    </form>
    <div>
        <div class="advanced-search">
            <button id="advanced-search">Options</button>
        </div>
        <div id="advanced-search-form" class="advanced-form">
            <form id="collection-select-form" method="get">
                <?php while ($collection = mysqli_fetch_assoc($collections_result)): ?>
                    <input type="radio" name="collection" value="<?php echo $collection['id']; ?>"
                        id="<?php echo $collection['id']; ?>">
                    <label for="<?php echo $collection['id']; ?>"><?php echo $collection['name']; ?></label>
                <?php endwhile; ?>

            </form>
        </div>

        <script>
            const advancedSearch = document.getElementById('advanced-search');
            const advancedSearchForm = document.getElementById('advanced-search-form');
            const collectionForm = document.getElementById("collection-select-form");

            advancedSearch.addEventListener('click', () => {
                advancedSearchForm.classList.toggle('expanded');
            });

            // Add event listener to the entire form for radio button changes
            collectionForm.addEventListener('change', (event) => {
                if (event.target.type === 'radio' && event.target.checked) {
                    // Submit the form programmatically when a radio button is selected
                    collectionForm.submit();
                }
            });
        </script>
    </div>
</div>