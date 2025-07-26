<?php
session_start();
require_once '../assets/shared/connect.php';

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
    SELECT u.userId, u.firstName, u.lastName, u.photo, u.isRiding
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

    $photoPath = !empty($row['photo']) ? '../assets/uploads/' . htmlspecialchars($row['photo']) : '';
    $status = $row['isRiding'] ? '🟢 Riding' : '⚪ Offline';
    $statusClass = $row['isRiding'] ? 'text-success' : 'text-secondary';

    echo '<div class="d-flex align-items-center mb-3">';
    echo '<img src="' . $photoPath . '" onerror="this.onerror=null; this.src=\'../assets/images/profile-default.png\';" class="rounded-circle me-3" width="40" height="40">';
    echo '<div>';
    echo '<div' . ($isCurrentUser ? ' class="fw-bold"' : '') . '>' . $fullName . ($isCurrentUser ? ' (You)' : '') . '</div>';
    echo '<div class="' . $statusClass . '">' . $status . '</div>';
    echo '</div>';
    echo '</div>';
}
