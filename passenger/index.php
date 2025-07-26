<?php
include("../assets/shared/connect.php");
session_start();
// For testing purposes
// $driverId = 2;

$userId = $_SESSION['userId'];
$historyId = null;

// To ensure different modal popup

// Set flag for my sharedMap.js to show modal
$hasArrived = isset($_GET['arrived']) && $_GET['arrived'] == '1';

$getPhotoQuery = "SELECT photo, role FROM users WHERE userId = $userId;";
$getPhotoResult = executeQuery($getPhotoQuery);

if (mysqli_num_rows($getPhotoResult) > 0) {
    $row = mysqli_fetch_assoc($getPhotoResult);
    $userRole = !empty($row['role']) ? $row['role'] : 'passenger';
    if ($userRole === 'passenger') {
        $profilePicture = !empty($row['photo'])
            ? '../assets/images/passengers/' . $row['photo']
            : '../assets/images/profile-default.png';
    } else {
        $profilePicture = !empty($row['photo'])
            ? '../assets/images/drivers/' . $row['photo']
            : '../assets/images/profile-default.png';
    }
}

if ($userRole === 'driver') {
    header("Location: ../driver/index.php");
}

$checkRidingQuery = "SELECT driverId, historyId
FROM history
WHERE userId = $userId
  AND dropoffTime IS NULL;";
$checkRidingResult = executeQuery($checkRidingQuery);

$checkPreviousDriverQuery = "
    SELECT driverId 
    FROM history 
    WHERE userId = $userId 
      AND dropoffTime IS NOT NULL 
    ORDER BY dropoffTime DESC 
    LIMIT 1;
";
$checkPreviousDriverResult = executeQuery($checkPreviousDriverQuery);

if (mysqli_num_rows($checkPreviousDriverResult) > 0) {
    $driver = mysqli_fetch_assoc($checkPreviousDriverResult);
    $previousDriverId = $driver['driverId'];

    $getDriverProfileQuery = "SELECT 
    driver.userId, 
    users.*
    FROM 
    drivers driver
    JOIN 
    users users ON driver.userId = users.userId
    WHERE
    driver.driverId = $previousDriverId;";
    $getDriverProfileResult = executeQuery($getDriverProfileQuery);

    // For storing of driver profile details
    if (mysqli_num_rows($getDriverProfileResult) > 0) {
        $row = mysqli_fetch_assoc($getDriverProfileResult);
        $previousDriverName = $row['firstName'] . " " . $row['lastName'];
    }

    $getDriverQuery = "SELECT plateNumber FROM drivers WHERE driverId = $previousDriverId;";
    $getDriverResult = executeQuery($getDriverQuery);

    if (mysqli_num_rows($getDriverResult) > 0) {
        $row = mysqli_fetch_assoc($getDriverResult);
        $previousPlateNumber = $row['plateNumber'];
    }
} else {
    $previousDriverId = null;
}

// Checker to determine if the user is currently riding
if (mysqli_num_rows($checkRidingResult) > /* == */ 0) {
    $row = mysqli_fetch_assoc($checkRidingResult);
    $driverId = $row['driverId'];
    $historyId = $row['historyId'];
    $setRidingQuery = "UPDATE users SET isRiding=1 WHERE userId = $userId;";
    $setRidingResult = executeQuery($setRidingQuery);
    $isRiding = true;

    $isRiding = $_SESSION['isRiding'] = true;


    $getDriverQuery = "SELECT d.plateNumber, d.todaRegistration, d.model, u.photo FROM drivers d JOIN users u ON d.userId = u.userId WHERE driverId = $driverId;";
    $getDriverResult = executeQuery($getDriverQuery);

    $getDriverProfileQuery = "SELECT 
    driver.userId, 
    users.*
    FROM 
    drivers driver
    JOIN 
    users users ON driver.userId = users.userId
    WHERE
    driver.driverId = $driverId;";
    $getDriverProfileResult = executeQuery($getDriverProfileQuery);

    // For storing of driver profile details
    if (mysqli_num_rows($getDriverProfileResult) > 0) {
        $row = mysqli_fetch_assoc($getDriverProfileResult);
        $name = $row['firstName'] . " " . $row['lastName'];
        $contact = $row['contactNumber'];
    }

    if (mysqli_num_rows($getDriverResult) > 0) {
        $row = mysqli_fetch_assoc($getDriverResult);
        $plateNumber = $row['plateNumber'];
        $model = $row['model'];
        $todaRegistration = $row['todaRegistration'];
        $photo = $row['photo'];
    }
} else {
    $setIdleQuery = "UPDATE users SET isRiding=2 WHERE userId = $userId;";
    $setIdleResult = executeQuery($setIdleQuery);
    $isRiding = false;

    $isRiding = $_SESSION['isRiding'] = false;
}


if (isset($_POST['arrive-button'])) {
    date_default_timezone_set('Asia/Manila');
    $dropoffTime = date('Y-m-d H:i:s');
    // Handle form submission
    $updateHistoryQuery = "UPDATE history SET dropoffTime = '$dropoffTime' WHERE historyId = $historyId;";
    $updateHistoryResult = executeQuery($updateHistoryQuery);

    // Set isRiding = 0
    $setIdleQuery = "UPDATE users SET isRiding=0 WHERE userId = 1;";
    $setIdleResult = executeQuery($setIdleQuery);

    $isRiding = $_SESSION['isRiding'] = false;
    // Prevent resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF'] . "?arrived=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger | Home Page</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">

    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Custom Styling -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Glass Styling -->
    <!-- <link rel="stylesheet" href="../assets/css/glass.css"> -->

    <style>
        body {
            overflow-y: hidden;
        }
    </style>
</head>

<body>
    <div class="modal fade" id="arrivalModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header text d-flex justify-content-center" style="color: black; background-color: #2ebcbc;">
                    <h4 class="modal-title fw-bold" id="infoModalLabel">Ride Completed!</h4>
                </div>
                <div class="modal-body p-4">
                    <p class="fs-5">You’ve successfully arrived at your destination. Thank you for riding with us!</p>
                    <ul class="list-unstyled mb-0">
                        <li><strong>Driver:</strong> <?php echo htmlspecialchars($previousDriverName ?? 'N/A'); ?></li>
                        <li><strong>Tricycle Plate:</strong> <?php echo htmlspecialchars($previousPlateNumber ?? 'N/A'); ?></li>
                    </ul>
                </div>
                <div class="modal-footer border-0 pb-4 px-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 me-3" data-bs-dismiss="modal" onclick="removeUrlParam()">Close</button>
                    <a href="rideHistory.php?historyId=<?php echo urlencode($historyId); ?>" class="btn rounded-pill px-4" style="color:#f4faff; background-color: #2ebcbc;">View History</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="gpsWarningModal" tabindex="-1" aria-labelledby="gpsWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2 border-teal">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="gpsWarningModalLabel"> <i class="bi bi-exclamation-triangle-fill me-2"></i>Location Outside Map Bounds</h5>
                </div>
                <div class="modal-body text-center">
                    Showing default location on the map.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-ok" data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fullscreen Map (Background) -->
    <div class="position-absolute top-0 start-0 w-100 h-100 z-0">
        <div id="map" class="w-100 h-100" style="pointer-events: auto;"></div>
    </div>

    <!-- Floating Content -->
    <div class="position-relative z-1 w-100 h-100 container py-3 pointer-pass">

        <!-- Logo and App Name -->
        <div class="d-flex align-items-center mb-3 position-absolute end-0 me-3 user-select-none">
            <img src="../assets/images/Logo.png" alt="TodaRescue" class="me-2" style="width: 40px; height: 40px;">
            <span class="fw-bold fs-5">TodaRescue</span>
        </div>

        <!-- Profile Card -->
        <?php if ($isRiding === true) { ?>
            <div class="p-0 m-0 position-relative vh-100 w-100 user-select-none" style="pointer-events: none;">
                <div class="card rounded-4 glass shadow px-4 py-4 mb-4 start-50 translate-middle-x"
                    style="background-color: #2ebcbc!important;top: 55%; width: 90%; max-width: 500px;" id="profile-card">
                    <div class="d-flex flex-row align-items-center justify-content-between profile-container" id="profile-details">

                        <!-- Profile Picture -->
                        <div class="me-3 custom-profile-icon">
                            <img src="../assets/images/drivers/<?php echo $photo ?>" onerror="this.onerror=null; this.src='../assets/images/profile-default.png';" onclick="goView()" alt="Profile" class="rounded-circle profile-icon-details" style="width: 50px!important; height: 50px!important;">
                        </div>

                        <!-- User Info -->
                        <div class="flex-grow-1 me-2">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 me-2"><?php echo $name; ?></h5>
                                <img src="../assets/images/verified.png" alt="Verified" style="width: 12px;">
                            </div>
                            <div class="align-items-center">
                                <small>Plate No:</small>
                                <b><?php echo $plateNumber; ?></b>
                            </div>
                            <div class="collapse mt-3" id="driver-details">
                                <div class="border-top border-dark pt-2">
                                    <p class="mb-1"><b>Tricycle Model:</b><?php echo $model; ?></p>
                                    <p class="mb-1"><b>Toda Registration:</b> <?php echo $todaRegistration; ?></p>
                                    <p class="mb-1"><b>Contact:</b> <?php echo $contact; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Toggle -->
                        <button class="btn p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#driver-details" aria-expanded="false">
                            <img src="../assets/images/drop-down.png" alt="Dropdown" width="13" class="drop-arrow text-center" id="arrow-icon">
                        </button>

                    </div>
                </div>
                <!-- Button -->
                <form method="POST" class="container-fluid my-4 align-items-center rounded-5 text-center">
                    <button type="submit" name="arrive-button" class="arrive-button glass rounded-pill text-bold position-absolute start-50 translate-middle-x px-4 py-3 fw-semibold"
                        style="background-color: rgb(46, 188, 188) !important; top: 75%; width: 90%; max-width: 200px;" id="arrive-button">
                        ARRIVED SAFELY
                    </button>
                </form>
            </div>

            <button id="toggle-btn" class="btn btn-primary rounded-circle glass-toggle p-4 text-dark d-flex align-items-center justify-content-center position-fixed"
                style="bottom: 10vh; right: 10px; z-index: 6; width: 48px; height: 48px; background-color: #2ebcbc; border: none;">
                <i class="bi bi-person-vcard-fill"></i>
            </button>
        <?php } ?>
    </div>
    <!-- NAVBAR -->
    <?php include '../assets/shared/navbarPassenger.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Storing of location to database -->
    <script>
        const userId = <?php echo $_SESSION['userId'] ?? 1; ?>;
    </script>

    <!-- Transfering of my hasArrived variable -->
    <script>
        window.hasArrived = <?php echo isset($hasArrived) && $hasArrived ? 'true' : 'false'; ?>;
        window.profilePicture = '<?php echo $profilePicture; ?>';
    </script>

    <!-- Turf js to handle polygons -->
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

    <!-- For map integration -->
    <script src="../assets/js/sharedMap.js"></script>

    <!-- Toggling Details -->
    <script src="../assets/js/homePage/details.js"></script>

</body>

</html>