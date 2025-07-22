<?php
require_once '../php/connect.php';
session_start();

$userId = intval($_GET['userId']);

$getLocationQuery = "SELECT latitude, longitude FROM locations WHERE userId = $userId ORDER BY timestamp DESC LIMIT 1";
$getLocationResult = mysqli_query($conn, $getLocationQuery);

if ($row = mysqli_fetch_assoc($getLocationResult)) {
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'No location found']);
}
