<?php
session_start();
require_once '../shared/connect.php';

if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = intval($_SESSION['userId']);

if (isset($_GET['visibility'])) {
    $visibility = $_GET['visibility'];

    $statusQuery = "UPDATE users SET isRiding = $visibility WHERE userId = $userId";
    $statusResult = executeQuery($statusQuery);

    echo json_encode(['success' => $statusResult ? 0 : 2]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
