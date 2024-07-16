<div class="wide__wrapper">
    <div class="go-back">
        <?php
        if ($previousPage) {
            echo "<a href='$previousPage'>Back to Previous Page</a>";
        } else {

            echo "<a href='/'>Go Back</a>";
        }
        ?>
    </div>