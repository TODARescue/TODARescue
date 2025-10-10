<?php
session_start();
include '../assets/shared/connect.php';
include '../assets/php/checkLogin.php';

// Initialize counts
$driverCount = $passengerCount = 0;
$inactiveDriverCount = $inactivePassengerCount = 0;

// Active users
$driverQuery = "SELECT COUNT(*) AS total FROM users WHERE role = 'driver' AND isDeleted = 0";
$passengerQuery = "SELECT COUNT(*) AS total FROM users WHERE role = 'passenger' AND isDeleted = 0";

// Inactive users
$inactiveDriverQuery = "SELECT COUNT(*) AS total FROM users WHERE role = 'driver' AND isDeleted = 1";
$inactivePassengerQuery = "SELECT COUNT(*) AS total FROM users WHERE role = 'passenger' AND isDeleted = 1";

// Execute queries
if ($result = $conn->query($driverQuery)) {
    $row = $result->fetch_assoc();
    $driverCount = $row['total'];
}

if ($result = $conn->query($passengerQuery)) {
    $row = $result->fetch_assoc();
    $passengerCount = $row['total'];
}

if ($result = $conn->query($inactiveDriverQuery)) {
    $row = $result->fetch_assoc();
    $inactiveDriverCount = $row['total'];
}

if ($result = $conn->query($inactivePassengerQuery)) {
    $row = $result->fetch_assoc();
    $inactivePassengerCount = $row['total'];
}

$conn->close();
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TODARescue | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-white d-flex flex-column align-items-center pt-5 min-vh-100">

    <div class="container px-4" style="max-width: 400px;">
        <h3 class="fw-bold text-center mb-3">TODA Rescue</h3>
        <h5 class="fw-semibold mb-4">Admin Dashboard</h5>

        <div class="d-flex flex-wrap gap-2 position-relative z-1">

            <a href="drivers.php" class="text-decoration-none flex-fill" style="min-width: 140px;">
                <div
                    class="bg-secondary-subtle rounded-5 p-3 d-flex flex-column justify-content-between text-start h-100">
                    <span class="fw-semibold text-dark">Active Drivers:</span>
                    <span class="fw-bold fs-4" style="color: #2DAAA7;"><?php echo $driverCount; ?></span>
                </div>
            </a>

            <a href="passengers.php" class="text-decoration-none flex-fill" style="min-width: 140px;">
                <div
                    class="bg-secondary-subtle rounded-5 p-3 d-flex flex-column justify-content-between text-start h-100">
                    <span class="fw-semibold text-dark">Active Passengers:</span>
                    <span class="fw-bold fs-4" style="color: #2DAAA7;"><?php echo $passengerCount; ?></span>
                </div>
            </a>


            <a href="drivers.php" class="text-decoration-none flex-fill" style="min-width: 140px;">
                <div
                    class="bg-secondary-subtle rounded-5 p-3 d-flex flex-column justify-content-between text-start h-100">
                    <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Inactive Drivers:</span>
                    <span class="fw-bold fs-4" style="color: #000000ff;"><?php echo $inactiveDriverCount; ?></span>
                </div>
            </a>


            <a href="passengers.php" class="text-decoration-none flex-fill" style="min-width: 140px;">
                <div
                    class="bg-secondary-subtle rounded-5 p-3 d-flex flex-column justify-content-between text-start h-100">
                    <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Inactive Passengers:</span>
                    <span class="fw-bold fs-4" style="color: #000000ff;"><?php echo $inactivePassengerCount; ?></span>
                </div>
            </a>

        </div>

        <div class="mt-5 w-100 text-center">
            <h6 class="fw-semibold mb-3">User Distribution</h6>
            <div class="w-75 mx-auto mb-4">
                <canvas id="userChart" width="300" height="300"></canvas>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarAdmin.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>

    <script>
        const ctx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Active Drivers', 'Active Passengers', 'Inactive Drivers', 'Inactive Passengers'],
                datasets: [{
                    data: [
                        <?php echo $driverCount; ?>,
                        <?php echo $passengerCount; ?>,
                        <?php echo $inactiveDriverCount; ?>,
                        <?php echo $inactivePassengerCount; ?>
                    ],
                    backgroundColor: ['#2DAAA7', '#09eeebff', '#000000ff', '#4e4b4bff'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>

</html>