<?php
// header('Content-Type: application/json');
session_start();

require_once '../php/connect.php';
$userID = $_GET['driverId'] ?? null;
if ($userID) {
    $query = "SELECT COUNT(*) AS passengerCount FROM history WHERE driverId = $userID AND dropoffTime IS NULL;";
    $result = executeQuery($query);
    $row = mysqli_fetch_assoc($result);

    echo json_encode(['passengerCount' => $row['passengerCount']]);
    exit;
}

echo json_encode(['error' => 'Missing driverId']);
