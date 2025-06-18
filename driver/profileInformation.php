<?php
// Include database connection
require_once '../assets/php/connect.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['testUser'])) {
    $_SESSION['userId'] = (int)$_GET['testUser'];
}

if (!isset($_SESSION['userId'])) {
    $_SESSION['userId'] = 1;
}

$userId = $_SESSION['userId'];

$stmt = $conn->prepare("SELECT d.driverId, d.plateNumber, d.model, d.address, d.todaRegistration, 
                        d.isVerified, d.photo, d.qrCode, CONCAT(u.firstName, ' ', u.lastName) as fullName
                        FROM drivers d
                        JOIN users u ON d.userId = u.userId
                        WHERE d.userId = ?");

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "You are not registered as a driver. Please contact support.";
} else {
    $driver = $result->fetch_assoc();
}

$stmt->close();

if (isset($_POST['generateQR']) && isset($driver)) {
    $qrData = $driver['plateNumber'];
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);
    
    // Save QR code URL to database
    $stmt = $conn->prepare("UPDATE drivers SET qrCode = ? WHERE driverId = ?");
    $stmt->bind_param("si", $qrUrl, $driver['driverId']);
    $stmt->execute();
    $stmt->close();
    
    $driver['qrCode'] = $qrUrl;

    $success = "QR Code has been generated successfully!";
}

$downloadUrl = isset($driver['qrCode']) ? $driver['qrCode'] : '';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TODA Rescue - Driver Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- Header for Profile Info -->
    <div class="container-fluid">
        <div class="row">
            <div class="col header d-flex flex-row p-2 rounded-5">
                <a href="javascript:history.back()">
                    <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Back"
                        class="img-fluid m-3" />
                </a>
                <div class="h3 p-3 m-1 px-1 fw-bolder">Profile Information</div>
            </div>
        </div>
    </div>

    <?php if (isset($error)): ?>
    <div class="container-fluid mt-4">
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    </div>
    <?php elseif (isset($success)): ?>
    <div class="container-fluid mt-4">
        <div class="alert alert-success" role="alert">
            <?php echo $success; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($driver)): ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-center py-3">
            <div class="card border-0">
                <div class="card-body p-4 text-center">
                    <!-- Driver Photo -->
                    <div class="row">
                        <div class="col mb-4 py-1">
                            <div class="rounded-circle d-flex justify-content-center align-items-center mx-auto"
                                style="width: 120px; height: 120px; background-color: #958D8D; overflow: hidden;">
                                <?php if (!empty($driver['photo']) && file_exists($driver['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($driver['photo']); ?>" alt="Driver Photo" 
                                         class="img-fluid w-100 h-100 object-fit-cover">
                                <?php else: ?>
                                    <span class="text-white fw-bold">Driver Photo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Driver Name -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h5 class="fw-bold"><?php echo htmlspecialchars($driver['fullName']); ?>
                                <?php if ($driver['isVerified']): ?>
                                    <span class="badge bg-success rounded-circle">✓</span>
                                <?php endif; ?>
                            </h5>
                        </div>
                    </div>

                    <!-- Tricycle Details -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Tricycle Details</h6>
                            <p class="mb-1 text-dark"><?php echo htmlspecialchars($driver['model'] . ' - ' . $driver['plateNumber']); ?></p>
                        </div>
                    </div>

                    <!-- Tricycle number -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Tricycle Number</h6>
                            <p class="mb-1 text-dark" id="tricycle-number"><?php echo htmlspecialchars($driver['plateNumber']); ?></p>
                        </div>
                    </div>

                    <!-- Permanent Address -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Permanent Address</h6>
                            <p class="mb-1 text-dark"><?php echo htmlspecialchars($driver['address']); ?></p>
                        </div>
                    </div>

                    <!-- Toda Registration -->
                    <div class="row">
                        <div class="col mb-4 py-1">
                            <h6 class="mb-2 text-dark">TODA Registration</h6>
                            <p class="mb-1 text-dark"><?php echo htmlspecialchars($driver['todaRegistration']); ?></p>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="row">
                        <div class="col mb-3">
                            <?php if (!empty($driver['qrCode'])): ?>
                                <img src="<?php echo htmlspecialchars($driver['qrCode']); ?>" alt="QR Code" class="img-fluid" id="qr-code">
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No QR code generated yet. Click the button below to generate your QR code.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- QR Code Actions -->
                    <div class="row justify-content-center">
                        <?php if (empty($driver['qrCode'])): ?>
                            <div class="col-auto mb-3">
                                <form method="post">
                                    <button type="submit" name="generateQR" class="btn custom-hover text-white fw-bold px-4 py-2 rounded-pill"
                                        style="background-color: #2DAAA7;">Generate QR Code</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="col-auto mb-3">
                                <a href="<?php echo htmlspecialchars($driver['qrCode']); ?>" download="qr-code.png" 
                                   class="btn custom-hover text-white fw-bold px-4 py-2 rounded-pill"
                                   style="background-color: #2DAAA7;">Download QR Code</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navbar for Driver -->
    <?php include '../assets/shared/navbarDriver.php'; ?>


    <script>
        // Generate QR
        function generateQr() {
            const tricycleNumber = document.getElementById("tricycle-number").textContent;
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(tricycleNumber)}`;
            document.getElementById("qr-code").src = qrUrl;
            console.log("QR Code generated for tricycle number", tricycleNumber);
        }
        window.addEventListener('DOMContentLoaded', generateQr);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>