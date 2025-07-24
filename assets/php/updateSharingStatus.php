<?php
session_start();
require_once '../php/connect.php';

$userId = $_SESSION['userId'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isSharing'])) {
    $isSharing = intval($_POST['isSharing']);

    $updateQuery = "UPDATE circlemembers SET isSharing = ? WHERE userId = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $isSharing, $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update']);
    }
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
exit;
