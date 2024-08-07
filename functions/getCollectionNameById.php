<?php
function getCollectionNameById($collectionId) {
    global $mysqli;

    $sql = "SELECT name FROM collections WHERE id = ?";
    $stmt = mysqli_prepare($mysqli, $sql);
    mysqli_stmt_bind_param($stmt, "i", $collectionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['name'];
    } else {
        return  
 'Features';
    }
}
