<?php
require_once '../assets/shared/connect.php';
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['driverId']) || empty($_GET['driverId'])) {
    header("Location: scanQr.php");
    exit();
}

$driverId = isset($_GET['driverId']) ? intval($_GET['driverId']) : 0;
$userId = isset($_SESSION['userId']) ? intval($_SESSION['userId']) : 0;

if (!function_exists('sanitize_input')) {
    function sanitize_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

try {
    $stmt = $conn->prepare("SELECT d.driverId, d.plateNumber, d.model, d.address, d.todaRegistration, 
                         d.isVerified, d.photo, CONCAT(u.firstName, ' ', u.lastName) as fullName, u.userId
                         FROM drivers d
                         JOIN users u ON d.userId = u.userId
                         WHERE d.driverId = ?");

    if (!$stmt) {
        die("Query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("i", $driverId);
    $success = $stmt->execute();

    if (!$success) {
        die("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: scanQr.php");
        exit();
    }

    $driver = $result->fetch_assoc();
    $stmt->close();
} catch (Exception $e) {
    die("An error occurred: " . $e->getMessage());
}

if (isset($_POST['startRide'])) {
    date_default_timezone_set('Asia/Manila');
    $pickupTime = date('Y-m-d H:i:s');
    // Handle form submission
    $insertHistoryQuery = "INSERT INTO history (pickupTime, driverId, userId) VALUES ('$pickupTime', $driverId, $userId);";
    $insertHistoryResult = executeQuery($insertHistoryQuery);

    // Prevent resubmission on refresh
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passenger | Driver Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
</head>

<body>
    <?php include '../assets/shared/header.php'; ?>

    <div class="container-fluid">
        <div class="d-flex justify-content-center" style="padding-top: 100px;">
            <div class="card border-1 rounded-5 w-75 my-4" style="background-color: #D9D9D9; height: 55vh;">
                <div class="card-body p-4" style="overflow-y: auto;">
                    <!-- Driver Photo -->
                    <div class="row">
                        <div class="text-center mb-2">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 120px; height: 120px; background-color: #958D8D; overflow: hidden;">
                                <img src="<?php echo htmlspecialchars($driver['photo']) ?>" onerror="this.onerror=null; this.src='../assets/images/profile-default.png';" alt="Driver Photo"
                                    class="img-fluid w-100 h-100 object-fit-cover">
                            </div>
                        </div>
                    </div>

                    <!-- Driver Name with Verification Badge -->
                    <div class="row">
                        <div class="text-center mb-2">
                            <h6 class="fw-bold">
                                <?php echo htmlspecialchars($driver['fullName']); ?>
                                <?php if ($driver['isVerified']): ?>
                                    <img src="../assets/images/verified.png" alt="Verified" style="width: 18px;">
                                <?php endif; ?>
                            </h6>
                        </div>
                    </div>

                    <!-- Tricycle Details -->
                    <div class="row">
                        <div class="mb-4">
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2 text-dark">Tricycle Details:</h6>
                                <p class="mb-0 text-muted fw-bold">
                                    <?php echo htmlspecialchars($driver['model'] . ' - ' . $driver['plateNumber']); ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold mb-2 text-dark">Tricycle Number:</h6>
                                <p class="mb-0 text-muted fw-bold">
                                    <?php echo htmlspecialchars($driver['plateNumber']); ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold mb-2 text-dark">Permanent Address:</h6>
                                <p class="mb-0 text-muted fw-bold">
                                    <?php echo htmlspecialchars($driver['address']); ?>
                                </p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold mb-2 text-dark">TODA Registration:</h6>
                                <p class="mb-0 text-muted fw-bold">
                                    <?php echo htmlspecialchars($driver['todaRegistration']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start Ride Button -->
        <div class="row d-flex justify-content-center align-items-center mt-0">
            <div class="col-auto py-3">
                <form method="POST" class="container-fluid mt-2 mb-4 align-items-center rounded-5 text-center">
                    <button type="submit" name="startRide" class="arrive-button glass rounded-pill text-bold position-absolute start-50 translate-middle-x px-4 py-3 fw-semibold"
                        style="background-color: rgb(46, 188, 188) !important; top: 75%; width: 90%; max-width: 200px;" id="startRide-button">
                        START RIDE
                    </button>
                </form>
            </div>
        </div>
        <!-- NAVBAR -->
        <?php include '../assets/shared/navbarPassenger.php'; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const startRideBtn = document.getElementById('startRideBtn');
                const statusMessage = document.getElementById('statusMessage');

                startRideBtn.addEventListener('click', function() {
                    const driverId = <?php echo $driver['driverId']; ?>;
                    const userId = <?php echo $driver['userId']; ?>;
                    const riderId = <?php echo isset($_SESSION['userId']) ? $_SESSION['userId'] : 3; ?>;

                    startRideBtn.disabled = true;
                    startRideBtn.textContent = 'Starting ride...';
                    statusMessage.style.display = 'flex';

                    console.log(`Starting ride with driverId=${driverId}, riderId=${riderId}`);

                    setTimeout(function() {

                        statusMessage.innerHTML = `
                        <div class="col-10">
                            <div class="alert alert-success text-center">
                                <strong>Ride started successfully!</strong><br>
                                Redirecting to home page...
                            </div>
                        </div>
                    `;

                        setTimeout(function() {
                            window.location.href = 'index.php';
                        }, 1500);
                    }, 1000);
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
            crossorigin="anonymous"></script>
</body>

</html>