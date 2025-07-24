<?php
header('Content-Type: application/json');
session_start();

require_once '../php/connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'store_location') {
    $userId = intval($_POST['userId']);
    $longitude = $_POST['longitude'];
    $latitude = $_POST['latitude'];

    $checkRidingQuery = "SELECT locationID FROM locations WHERE userId = $userId";
    $checkRidingResult = executeQuery($checkRidingQuery);

    if (mysqli_num_rows($checkRidingResult) > 0) {

        $updateLocationQuery = "UPDATE locations SET longitude = '$longitude', latitude = '$latitude' WHERE userId = $userId;";
        executeQuery($updateLocationQuery);

        echo json_encode(["success" => $updateLocationQuery ? true : false]);
    } else {
        // Insert location
        $insertQuery = "INSERT INTO locations (userId, longitude, latitude) VALUES ($userId, '$longitude', '$latitude')";
        $insertResult = executeQuery($insertQuery);

        echo json_encode(["success" => $insertResult ? true : false]);
    }
    exit;
}
