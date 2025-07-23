<?php
header('Content-Type: application/json');
require_once '../php/connect.php';

session_start();

$circleId = intval($_GET['circleId']);
$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : null;

$getMembersQuery = "
    SELECT u.firstName, u.lastName, u.photo, u.isRiding, u.userId, cm.role
    FROM circlemembers cm
    INNER JOIN users u ON cm.userId = u.userId
    WHERE cm.circleId = $circleId AND cm.isSharing = 1
";

$getMembersResult = executeQuery($getMembersQuery);

$getRoleQuery = "
    SELECT cm.role FROM circlemembers cm
    WHERE cm.userId = $userId AND cm.circleId = $circleId AND cm.isSharing = 1
";
$getRoleResult = executeQuery($getRoleQuery);

$members = [];
$role = null;

if ($getMembersResult && mysqli_num_rows($getMembersResult) > 0) {
    while ($row = mysqli_fetch_assoc($getMembersResult)) {
        $members[] = [
            'userName'   => $row['firstName'] . ' ' . $row['lastName'],
            'profilePic' => !empty($row['photo']) ? '../assets/images/passengers/' . $row['photo'] : '../assets/images/profile-default.png',
            'status'     => $row['isRiding'],
            'userId'     => $row['userId'],
            'role'       => $row['role']
        ];
    }
}

if ($getRoleResult && mysqli_num_rows($getRoleResult) > 0) {
    $roleRow = mysqli_fetch_assoc($getRoleResult);
    $role = $roleRow['role'];
}

echo json_encode(['members' => $members, 'role' => $role]);
exit;
