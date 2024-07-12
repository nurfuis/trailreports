<?php

require_once realpath("../../db_connect.php");

$selectedFeature = "";
$selectedCollections = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input (important for security!)
    $selectedFeature = trim(htmlspecialchars($_POST["feature"]));
    $selectedCollections = isset($_POST["collections"]) ? $_POST["collections"] : []; // Handle checkbox selection
}

$sqlFeatures = "SELECT id, name FROM features WHERE collections_id = (2, 5, 6, 10, 1, 12)";
$sqlCollections = "SELECT id, name FROM collections";

$resultFeatures = $mysqli->query($sqlFeatures);
if (!$resultFeatures) {
    die("Error retrieving features: " . $mysqli->error);
}

$resultCollections = $mysqli->query($sqlCollections);
if (!$resultCollections) {
    die("Error retrieving collections: " . $mysqli->error);
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