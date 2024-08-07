<?php
// $collection is setting the initial dropdown list
$trail_sql = "SELECT id, name FROM features WHERE collections_id IN ($collection) ORDER BY name ASC;";
$trail_result = mysqli_query($mysqli, $trail_sql);

$collections_sql = "SELECT id, name FROM collections LIMIT 16"; // magic number to limit collection filters
$collections_result = mysqli_query($mysqli, $collections_sql);

$collectionName = "Features";

?>
<div class="trail-select regular-padding">

    <form action="#app" method="get" id="app" class="trail-select__form">

        <input hidden name="collection" value="<?php echo $collection ?>">


        <label class="no-select" for="trail-select">Select a Feature:</label><select name="feature_id" id="trail-select">


            <option id="collecions-title" value="recent"> -- <?php echo $collectionName; ?> -- </option>


            <?php while ($trail = mysqli_fetch_assoc($trail_result)) : ?>
                <option value="<?php echo $trail['id']; ?>" <?php if (isset($_GET['feature_id']) && $_GET['feature_id'] == $trail['id']) : ?> selected <?php endif; ?>>
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


        <div>
            <button class="advanced__button--toggle">Filter</button><button class="clear-button">Reset</button>
        </div>


        <div class="advanced__form-wrapper">

            <form class="advanced__form" method="get">

                <?php while ($collection = mysqli_fetch_assoc($collections_result)) : ?>
                    <div class="no-wrap">
                        <input type="radio" name="collection" value="<?php echo $collection['id']; ?>" id="<?php echo $collection['id']; ?>" <?php if (isset($_GET['collection']) && $_GET['collection'] == $collection['id']) : ?> checked <?php endif; ?>>
                        <label for="<?php echo $collection['id']; ?>"><?php echo $collection['name']; ?>
                        </label>
                    </div>
                <?php endwhile; ?>
            </form>
        </div>
    </div>
</div>
<script>
    // change selection 
    const trailSelectForm = document.querySelectorAll('.trail-select__form')[0];
    trailSelectForm.addEventListener('change', (event) => {
        trailSelectForm.submit();
        sessionStorage.removeItem('collectionSelected');
        optionsWrapper.classList.remove('expanded');
    })

    // change collection
    const optionsForm = document.querySelectorAll('.advanced__form')[0];
    optionsForm.addEventListener('change', (event) => {
        if (event.target.type === 'radio' && event.target.checked) {
            sessionStorage.setItem('collectionSelected', true); // Set flag on selection
            optionsForm.submit();
        }
    });

    const optionsButton = document.querySelectorAll('.advanced__button--toggle')[0];
    const optionsWrapper = document.querySelectorAll('.advanced__form-wrapper')[0];

    optionsButton.addEventListener('click', () => {
        optionsWrapper.classList.toggle('expanded');
        optionsButton.classList.toggle('active');
        document.getElementById('app').scrollIntoView({
            behavior: "smooth",
            block: "start"
        });

    });

    const clearButton = document.querySelector('.clear-button');

    clearButton.addEventListener('click', () => {
        sessionStorage.removeItem('collectionSelected');
        optionsWrapper.classList.remove('expanded');
        window.location.href = 'home.php';
    });

    // Check for existing flag on page load
    const collectionSelected = sessionStorage.getItem('collectionSelected');
    if (collectionSelected) {
        optionsWrapper.classList.add('expanded');
        optionsButton.classList.toggle('active');
        document.getElementById('app').scrollIntoView({
            behavior: "smooth",
            block: "start"
        });

    }
</script>