<div class="wide__wrapper">
    <div class="go-back">
        <?php
        if ($previousPage) {
            echo "<a href='$previousPage'>Go Back</a>";
        } else {

            echo "<a href='/home.php'>Go Back</a>";
        }
        ?>
    </div>