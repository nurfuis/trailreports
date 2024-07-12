<?php

require_once realpath("../../db_connect.php");

$selectedFeature = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedFeature = trim(htmlspecialchars($_POST["feature"]));
}

$sqlFeatures = "SELECT id, name FROM features WHERE collections_id IN (2, 5, 6, 10, 1, 12)";

$resultFeatures = $mysqli->query($sqlFeatures);
if (!$resultFeatures) {
    die("Error retrieving features: " . $mysqli->error);
}
?>

<h1>Trail Report Form</h1>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

    <label for="feature">Select Feature:</label>
    <select name="feature" id="feature">
        <option value="">Select Feature</option>
        <?php while ($row = $resultFeatures->fetch_assoc()): ?>
            <option value="<?php echo $row["id"]; ?>" <?php echo ($selectedFeature == $row["id"]) ? "selected" : ""; ?>>
                <?php echo $row["name"]; ?>
            </option>
        <?php endwhile; ?>
    </select>



    <br><br>

    <button type="submit">Submit Report</button>

</form>

<?php
$mysqli->close();
?>