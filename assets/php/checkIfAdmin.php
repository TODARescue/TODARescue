<?php
session_start();
require_once '../shared/connect.php'; 

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['isAdmin' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['circleId'])) {
    echo json_encode(['isAdmin' => false, 'error' => 'Missing circleId']);
    exit;
}

$circleId = intval($_GET['circleId']);
$userId = $_SESSION['userId'];

$sql = "
    SELECT role 
    FROM circlemembers 
    WHERE circleId = ? AND userId = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $circleId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $role = strtolower($row['role']);
    $isAdmin = ($role === 'admin' || $role === 'owner');
    echo json_encode(['isAdmin' => $isAdmin]);
} else {
    echo json_encode(['isAdmin' => false, 'error' => 'Not a member of this circle']);
}
