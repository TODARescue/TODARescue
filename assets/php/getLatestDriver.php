<?php
header('Content-Type: application/json');
require_once '../php/connect.php';

$selectedUserId = isset($_GET['userId']) ? intval($_GET['userId']) : null;

if (!$selectedUserId) {
    echo json_encode(['error' => 'Missing userId parameter.']);
    exit;
}

// 1. Check if there's an ongoing ride
$ongoingRideQuery = "
    SELECT h.driverId, u.firstName, u.lastName, u.contactNumber, u.photo, u.userId, h.dropOffTime, d.plateNumber, d.model, d.todaRegistration
    FROM history h
    INNER JOIN drivers d ON h.driverId = d.driverId
    INNER JOIN users u ON d.userId = u.userId
    WHERE h.userId = $selectedUserId AND h.dropOffTime IS NULL
    ORDER BY h.pickupTime DESC
    LIMIT 1
";

$ongoingResult = executeQuery($ongoingRideQuery);

if ($ongoingResult && mysqli_num_rows($ongoingResult) > 0) {
    $row = mysqli_fetch_assoc($ongoingResult);
} else {
    // 2. No ongoing ride? Get the latest completed one
    $completedRideQuery = "
        SELECT h.driverId, u.firstName, u.lastName, u.contactNumber, u.photo, u.userId, h.dropOffTime, d.plateNumber, d.model, d.todaRegistration
        FROM history h
        INNER JOIN drivers d ON h.driverId = d.driverId
        INNER JOIN users u ON d.userId = u.userId
        WHERE h.userId = $selectedUserId AND h.dropOffTime IS NOT NULL
        ORDER BY h.dropOffTime DESC
        LIMIT 1
    ";
    $completedResult = executeQuery($completedRideQuery);
    $row = ($completedResult && mysqli_num_rows($completedResult) > 0) ? mysqli_fetch_assoc($completedResult) : null;
}

if ($row) {
    $driver = [
        'driverId'    => $row['driverId'],
        'userId'      => $row['userId'],
        'driverName'  => $row['firstName'] . ' ' . $row['lastName'],
        'profilePic'  => !empty($row['photo']) ? '../assets/images/drivers/' . $row['photo'] : '../assets/images/profile-default.png',
        'dropOffTime' => $row['dropOffTime'] ?? null,
        'plateNo'     => $row['plateNumber'],
        'model'       => $row['model'],
        'todaReg'     => $row['todaRegistration'],
        'contactNo'   => $row['contactNumber']
    ];

    echo json_encode($driver);
} else {
    echo json_encode(['message' => 'No recent driver found.']);
}

exit;
