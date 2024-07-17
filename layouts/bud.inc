<div class="wide__wrapper">
    <div class="go-back">
        <?php
        if ($previousPage) {
            echo "<a href='$previousPage'>Top of the List</a>";
        } else {

            echo "<a href='/'>Go Back</a>";
        }
        ?>
    </div>