<?php
session_start();
require_once '../assets/shared/connect.php';

header('Content-Type: application/json');

$circles = [];

if (!isset($_SESSION['userId'])) {
    echo json_encode($circles);
    exit;
}

$userId = $_SESSION['userId'];

$sql = "
    SELECT c.circleId, c.circleName
    FROM circlemembers cm
    INNER JOIN circles c ON cm.circleId = c.circleId
    WHERE cm.userId = ?
";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $circles[] = [
            'circleId' => $row['circleId'],
            'circleName' => $row['circleName']
        ];
    }

    echo json_encode($circles);
} else {
    echo json_encode($circles);
}
