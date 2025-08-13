<?php
header('Content-Type: application/json');

session_start();

require_once '../shared/connect.php'; // Make sure this doesn't produce any output

header('Content-Type: application/json');
session_start();

$response = ["success" => false];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'store_location') {
        $userId = intval($_POST['userId']);
        $longitude = $_POST['longitude'];
        $latitude = $_POST['latitude'];

        date_default_timezone_set('Asia/Manila');
        $phTime = date('Y-m-d H:i:s');

        $checkRidingQuery = "SELECT locationID FROM locations WHERE userId = $userId";
        $checkRidingResult = executeQuery($checkRidingQuery);

        if ($checkRidingResult && mysqli_num_rows($checkRidingResult) > 0) {
            $updateLocationQuery = "UPDATE locations 
                SET longitude = '$longitude', 
                    latitude = '$latitude', 
                    timestamp = '$phTime' 
                WHERE userId = $userId";
            $updateResult = executeQuery($updateLocationQuery);
            $response["success"] = $updateResult ? true : false;
        } else {
            $insertQuery = "INSERT INTO locations (userId, longitude, latitude, timestamp) 
                VALUES ($userId, '$longitude', '$latitude', '$phTime')";
            $insertResult = executeQuery($insertQuery);
            $response["success"] = $insertResult ? true : false;
        }
    }
} catch (Exception $e) {
    // Optional: write to a log file
    file_put_contents("error_log.txt", $e->getMessage(), FILE_APPEND);
    $response["error"] = "Exception occurred.";
}

echo json_encode($response);
exit;
