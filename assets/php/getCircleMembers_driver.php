<?php
header('Content-Type: application/json');
require_once '../shared/connect.php';
session_start();

$circleId = intval($_GET['circleId']);
$userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : null;

$members = [];
$role = null;

// Safety checks
if (!$circleId || !$userId) {
    echo json_encode(['members' => [], 'role' => null, 'error' => 'Missing circleId or userId']);
    exit;
}

// Get current user's role in the circle (even if not sharing)
$getRoleQuery = "
    SELECT role 
    FROM circlemembers 
    WHERE userId = $userId AND circleId = $circleId
    LIMIT 1
";
$getRoleResult = executeQuery($getRoleQuery);

if ($getRoleResult && mysqli_num_rows($getRoleResult) > 0) {
    $roleRow = mysqli_fetch_assoc($getRoleResult);
    $role = $roleRow['role'];
}

// Get members who are sharing in this circle
$getMembersQuery = "
    SELECT u.firstName, u.lastName, u.photo, u.userId, u.userType, u.isRiding, cm.role
    FROM circlemembers cm
    INNER JOIN users u ON cm.userId = u.userId
    WHERE cm.circleId = $circleId AND cm.isSharing = 1
";
$getMembersResult = executeQuery($getMembersQuery);

// Build member list
if ($getMembersResult && mysqli_num_rows($getMembersResult) > 0) {
    while ($row = mysqli_fetch_assoc($getMembersResult)) {
        // Determine image folder by userType
        $folder = ($row['userType'] === 'driver') ? '../assets/images/drivers/' : '../assets/images/passengers/';
        $photoPath = !empty($row['photo']) ? $folder . $row['photo'] : '../assets/images/profile-default.png';

        $members[] = [
            'userName'   => $row['firstName'] . ' ' . $row['lastName'],
            'profilePic' => $photoPath,
            'status'     => $row['isRiding'],
            'userId'     => $row['userId'],
            'role'       => $row['role']
        ];
    }
}

echo json_encode(['members' => $members, 'role' => $role]);
exit;
