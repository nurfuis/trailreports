<?php
$trail_sql = "SELECT id, name FROM features WHERE collections_id IN ($collection) ORDER BY name ASC;";
$trail_result = mysqli_query($mysqli, $trail_sql);


$collections_sql = "SELECT id, name FROM collections LIMIT 2";
$collections_result = mysqli_query($mysqli, $collections_sql);

$isCollectionSet = isset($_GET['collection']);

?>
<div class="trail-select regular-padding">

    <form action="#app" method="get" id="app" class="trail-select__form">
        <input hidden name="collection" value="<?php echo $collection ?>">

        <label for="trail-select">Select a Trail:</label>

        <select name="feature_id" id="trail-select">
            <option value="recent"> -- Recent Updates -- </option>

            <?php while ($trail = mysqli_fetch_assoc($trail_result)): ?>

                <option 
                    value="<?php echo $trail['id']; ?>" 
                    <?php if (isset($_GET['feature_id']) && $_GET['feature_id'] == $trail['id']): ?> selected <?php endif; ?>>
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

        <button class="hidden" type="submit">View</button>
    </form>

    <div class="advanced">

        <button class="advanced__button--toggle">Options</button>

        <div class="advanced__form-wrapper <?php echo $isCollectionSet ? 'expanded' : ''; ?>">
            <form class="advanced__form" method="get">
                <?php while ($collection = mysqli_fetch_assoc($collections_result)): ?>
                    
                    <input type="radio" name="collection" value="<?php echo $collection['id']; ?>" id="<?php echo $collection['id']; ?>" <?php if (isset($_GET['collection']) && $_GET['collection'] == $collection['id']): ?> checked <?php endif; ?>>
                    <label
                        for="<?php echo $collection['id']; ?>"><?php echo $collection['name']; ?>
                    </label>
                <?php endwhile; ?>
            </form>
        </div>
    </div>
</div>
<script>
    const trailSelectForm = document.querySelectorAll('.trail-select__form')[0];
    trailSelectForm.addEventListener('change', (event) => {
        trailSelectForm.submit();
    })

    const optionsForm = document.querySelectorAll('.advanced__form')[0];
    optionsForm.addEventListener('change', (event) => {
        if (event.target.type === 'radio' && event.target.checked) {
            optionsForm.submit();
        }
    });

    const optionsButton = document.querySelectorAll('.advanced__button--toggle')[0];
    const optionsWrapper = document.querySelectorAll('.advanced__form-wrapper')[0];

    optionsButton.addEventListener('click', () => {
        optionsWrapper.classList.toggle('expanded');
    });


</script>