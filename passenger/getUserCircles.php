<?php
session_start();
require_once '../assets/php/connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['userId'];

$sql = "
    SELECT c.circleId, c.circleName
    FROM circlemembers cm
    JOIN circles c ON cm.circleId = c.circleId
    WHERE cm.userId = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$circles = [];
while ($row = $result->fetch_assoc()) {
    $circles[] = $row;
}

echo json_encode($circles);
