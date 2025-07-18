<?php
session_start();
require_once '../assets/php/connect.php';

if (!isset($_SESSION['userId'])) {
    echo '<p class="text-danger">Unauthorized access.</p>';
    exit;
}

if (!isset($_GET['circleId'])) {
    echo '<p class="text-danger">Missing circleId.</p>';
    exit;
}

$circleId = intval($_GET['circleId']);
$currentUserId = $_SESSION['userId'];

$sql = "
    SELECT u.userId, u.firstName, u.lastName, u.photo, u.status
    FROM circlemembers cm
    JOIN users u ON cm.userId = u.userId
    WHERE cm.circleId = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $circleId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $fullName = htmlspecialchars($row['firstName'] . ' ' . $row['lastName']);
    $isCurrentUser = $row['userId'] == $currentUserId;
    $photo = $row['photo'] ?: 'profile-default.png';
    $status = $row['status'] === 'Riding' ? '🟢 Riding' : '⚪ Offline';
    $statusClass = $row['status'] === 'Riding' ? 'text-success' : 'text-secondary';

    echo '<div class="d-flex align-items-center mb-3">';
    echo '<img src="../assets/uploads/' . htmlspecialchars($photo) . '" class="rounded-circle me-3" width="40" height="40">';
    echo '<div>';
    echo '<div' . ($isCurrentUser ? ' class="fw-bold"' : '') . '>' . $fullName . ($isCurrentUser ? ' (You)' : '') . '</div>';
    echo '<div class="' . $statusClass . '">' . $status . '</div>';
    echo '</div>';
    echo '</div>';
}
