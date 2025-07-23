<?php
include("../assets/php/connect.php");
session_start();

$userId = $_SESSION['userId'] ?? null;
$historyId = null;
$driverId = null;
$driverName = null;
$plateNumber = null;
$model = null;
$todaRegistration = null;
$photo = null;
$contact = null;
$passengerCount = 0;

$hasArrived = isset($_GET['arrived']) && $_GET['arrived'] == '1';

if (!$userId) {
    die("User not logged in.");
}

// Check if the user is currently riding
$checkRidingQuery = "SELECT driverId, historyId FROM history WHERE userId = $userId AND dropoffTime IS NULL;";
$checkRidingResult = executeQuery($checkRidingQuery);

if ($checkRidingResult && mysqli_num_rows($checkRidingResult) > 0) {
    $row = mysqli_fetch_assoc($checkRidingResult);
    $driverId = $row['driverId'];
    $historyId = $row['historyId'];
    executeQuery("UPDATE users SET isRiding = 1 WHERE userId = $userId;");
    $isRiding = true;
} else {
    executeQuery("UPDATE users SET isRiding = 0 WHERE userId = $userId;");
    $isRiding = false;
}

// Get driverId from drivers table based on userId
$getDriverQuery = "SELECT * FROM drivers WHERE userId = $userId";
$getDriverResult = executeQuery($getDriverQuery);

if ($driverRow = mysqli_fetch_assoc($getDriverResult)) {
    $driverId = $driverRow['driverId'];
    $_SESSION['driverId'] = $driverId;

    $plateNumber = $driverRow['plateNumber'] ?? '';
    $model = $driverRow['model'] ?? '';
    $todaRegistration = $driverRow['todaRegistration'] ?? '';
    $photo = $driverRow['photo'] ?? 'profile-default.png';
    $contact = $driverRow['contact'] ?? '';

    // Get driver name
    $getNameQuery = "
    SELECT u.firstName, u.lastName
    FROM users u
    JOIN drivers d ON u.userId = d.userId
    WHERE u.userId = $userId
";
    $getNameResult = executeQuery($getNameQuery);
    if ($nameRow = mysqli_fetch_assoc($getNameResult)) {
        $driverName = $nameRow['firstName'] . ' ' . $nameRow['lastName'];
        $_SESSION['driverName'] = $driverName;
    }
}

// Count passengers
if ($driverId !== null) {
    $countPassengersQuery = "SELECT COUNT(*) AS passengerCount FROM history WHERE driverId = $driverId AND dropoffTime IS NULL;";
    $countPassengersResult = executeQuery($countPassengersQuery);
    if ($countPassengersResult && $row = mysqli_fetch_assoc($countPassengersResult)) {
        $passengerCount = $row['passengerCount'];
    }
}

if (isset($_POST['arrive-button']) && $historyId !== null) {
    date_default_timezone_set('Asia/Manila');
    $dropoffTime = date('Y-m-d H:i:s');
    executeQuery("UPDATE history SET dropoffTime = '$dropoffTime' WHERE historyId = $historyId;");
    executeQuery("UPDATE users SET isRiding = 0 WHERE userId = $userId;");
    header("Location: " . $_SERVER['PHP_SELF'] . "?arrived=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver | Home Page</title>

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
    <div class="position-fixed start-0 px-2" style="z-index: 1055; bottom: 10vh;">
        <div id="safeToast"
            class="toast align-items-center border-0 small"
            role="alert" aria-live="assertive" aria-atomic="true"
            data-bs-delay="7000" data-bs-autohide="true"
            style="color: black!important; background-color: #2ebcbc!important; padding: 0.5rem 1rem; max-width: 300px; font-size: 1rem;">
            <div class="d-flex align-items-center">
                <div class="toast-body px-1">
                    You ride safely with your passenger!
                </div>
                <button type="button" class="btn-close ms-2 me-1 m-auto"
                    data-bs-dismiss="toast" aria-label="Close" style="color: black!important; transform: scale(0.85);"></button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="gpsWarningModal" tabindex="-1" aria-labelledby="gpsWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2 border-teal">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="gpsWarningModalLabel">📌 Location Outside Map Bounds</h5>
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
        <div class="p-0 m-0 position-relative vh-100 w-100 user-select-none" style="pointer-events: none;">
            <div class="card rounded-4 glass shadow px-4 py-4 mb-4 start-50 translate-middle-x"
                style="background-color: #2ebcbc!important;top: 55%; width: 90%; max-width: 500px;" id="profile-card">
                <div class="d-flex flex-row align-items-center justify-content-between profile-container" id="profile-details">

                    <!-- Profile Picture -->
                    <div class="me-3 profile-pic ">
                        <img src="../assets/images/<?php echo $photo ?>" onerror="this.onerror=null; this.src='../assets/images/profile-default.png';" alt="Profile" class="rounded-circle" width="50" height="50" onclick="goView()">
                    </div>

                    <!-- User Info -->
                    <div class="flex-grow-1 me-2">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 me-2"><?php echo htmlspecialchars($driverName ?? 'N/A'); ?></h5>
                            <img src="../assets/images/verified.png" alt="Verified" style="width: 12px;">
                        </div>
                        <div class="align-items-center">
                            <b id="passenger-count"></b>
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
                    <!-- <button class="btn p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#driver-details" aria-expanded="false">
                            <img src="../assets/images/drop-down.png" alt="Dropdown" width="13" class="drop-arrow text-center" id="arrow-icon">
                        </button> -->

                </div>
            </div>
        </div>

        <button id="toggle-btn" class="btn btn-primary rounded-circle glass-toggle p-4 text-dark d-flex align-items-center justify-content-center position-fixed"
            style="bottom: 10vh; right: 10px; z-index: 6; width: 48px; height: 48px; background-color: #2ebcbc; border: none;">
            <i class="bi bi-person-vcard-fill"></i>
        </button>
    </div>
    <!-- NAVBAR -->
    <?php include '../assets/shared/navbarDriver.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Storing of location to database -->
    <script>
        const userId = <?php echo $_SESSION['userId'] ?? 1; ?>;
        const driverId = <?php echo $_SESSION['driverId'] ?? 1; ?>;
    </script>

    <!-- Turf js to handle polygons -->
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>

    <!-- For map integration -->
    <script src="../assets/js/sharedMap.js"></script>

    <!-- Toggling Details -->
    <script src="../assets/js/homePage/details.js"></script>

    <script>
        // Poll every 5 seconds
        let lastPassengerCount = null;
        setInterval(() => {
            fetchPassengerCount();
        }, 5000);

        // Function to fetch passenger count
        function fetchPassengerCount() {
            fetch(`../assets/php/countPassenger.php?driverId=${driverId}`)
                .then(response => response.json())
                .then(data => {
                    const count = parseInt(data.passengerCount, 10);

                    if (!isNaN(count)) {
                        document.getElementById("passenger-count").textContent =
                            "Number of passengers: " + count;

                        // Show toast only if count decreased
                        if (lastPassengerCount !== null && count < lastPassengerCount) {
                            showSafeToast();
                        }

                        lastPassengerCount = count;
                    } else {
                        console.warn("Invalid passenger count:", data.passengerCount);
                    }
                })
                .catch(err => console.error("Fetch error:", err));
        }

        // Initial fetch on page load
        fetchPassengerCount();

        function showSafeToast() {
            const toastEl = document.getElementById('safeToast');
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    </script>
    <script>
        console.log("Driver Name:", <?php echo json_encode($_SESSION['driverName'] ?? 'N/A'); ?>);
        console.log("Driver Id:", <?php echo json_encode($_SESSION['driverId'] ?? 'N/A'); ?>);
    </script>
</body>

</html>