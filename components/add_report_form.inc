<?php

require_once realpath("../../db_connect.php");

$selectedFeature = "";
$selectedCollections = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input (important for security!)
    $selectedFeature = trim(htmlspecialchars($_POST["feature"]));
    $selectedCollections = isset($_POST["collections"]) ? $_POST["collections"] : [];
}

$sqlFeatures = "SELECT f.id, f.name, c.name AS collection_name 
                FROM features f 
                INNER JOIN collections c ON f.collections_id = c.id
                WHERE f.collections_id IN (2, 5, 6, 10, 1, 12)";

$resultFeatures = $mysqli->query($sqlFeatures);
if (!$resultFeatures) {
    die("Error retrieving features: " . $mysqli->error);
}

$collectionName = "";
if ($selectedFeature) {
    $row = $resultFeatures->fetch_assoc();
    $collectionName = $row["collection_name"];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php if ($collectionName): ?>
        <title>Trail Report Form - <?php echo $collectionName; ?></title>
    <?php else: ?>
        <title>Trail Report Form</title>
    <?php endif; ?>
</head>

<body>

    <h1><?php echo $collectionName ? "Trail Report Form - " . $collectionName : "Trail Report Form"; ?></h1>
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

        <button type="submit">Enter Report</button>

    </form>

    <?php
    $mysqli->close();
    ?>

</body>

</html>