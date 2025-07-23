<?php
session_start();
require_once '../php/connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['userId'];
$data = json_decode(file_get_contents('php://input'), true);
$isSharing = isset($data['isSharing']) ? (int)$data['isSharing'] : 0;

$sql = "UPDATE circlemembers SET isSharing = ? WHERE userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $isSharing, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update sharing']);
}
