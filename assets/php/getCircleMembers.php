<?php
header('Content-Type: application/json');
require_once '../php/connect.php';

$circleId = intval($_GET['circleId']);

$getMembersQuery = "
    SELECT u.firstName, u.lastName, u.photo, u.isRiding, u.userId
    FROM circlemembers cm
    INNER JOIN users u ON cm.userId = u.userId
    WHERE cm.circleId = $circleId
";

$getMembersResult = executeQuery($getMembersQuery);

$members = [];

if ($getMembersResult && mysqli_num_rows($getMembersResult) > 0) {
    while ($row = mysqli_fetch_assoc($getMembersResult)) {
        $members[] = [
            'userName'   => $row['firstName'] . ' ' . $row['lastName'],
            'profilePic' => !empty($row['photo']) ? '../assets/images/passengers/' . $row['photo'] : '../assets/images/profile-default.png',
            'status'     => $row['isRiding'],
            'userId'     => $row['userId']
        ];
    }
}

echo json_encode($members);
exit;
