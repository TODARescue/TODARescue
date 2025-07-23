<?php
header('Content-Type: application/json');
session_start();

require_once '../php/connect.php';

$userId = $_SESSION['userId'];

if ($userId) {

    // === GET CIRCLES THE USER IS A PART OF ===
    $getCirclesQuery = "
        SELECT c.circleId, c.circleName 
        FROM circles c
        INNER JOIN circlemembers cm ON c.circleId = cm.circleId
        WHERE cm.userId = $userId
    ";

    $getCirclesResult = executeQuery($getCirclesQuery);

    $circles = [];
    if ($getCirclesResult && mysqli_num_rows($getCirclesResult) > 0) {
        while ($row = mysqli_fetch_assoc($getCirclesResult)) {
            $circles[] = $row;
        }
    }

    echo json_encode($circles);
    exit;
}
