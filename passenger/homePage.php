<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODARescue | Home Page</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Custom Styling -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="container mt-4">
        <!-- Icon and Text -->
        <div class="d-flex align-items-center mb-3">
            <img src="../assets/images/Logo.png" alt="TodaRescue" class="small-logo me-2">
            <span class="fw-bold fs-5">TodaRescue</span>
        </div>

        <!-- Leaflet Map -->
        <div class="px-2">
            <div id="map" class="border rounded-3 shadow-lg" style="height: 50vh;"></div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="card rounded-3 shadow px-4 py-4" style="background-color: #2EBCBC;">
            <div class="d-flex flex-row align-items-center justify-content-between profile-container" id="profile-details">

                <!-- Profile Picture -->
                <div class="me-3 profile-pic">
                    <img src="../assets/images/profile-default.png" alt="Profile" class="rounded-circle" width="50" height="50">
                </div>

                <!-- User Name -->
                <div class="flex-grow-1">
                    <h5 class="mb-0">Juan Dela Cruz</h5>
                    <div class="align-items-center">
                        <small class="">Driver</small>
                        <img src="../assets/images/verified.png" alt="Verified">
                    </div>
                    <div class="collapse mt-3" id="driver-details">
                        <div class="border-top border-dark pt-2">
                            <p class="mb-1"><strong>License No:</strong> DRV-2023-0012</p>
                            <p class="mb-1"><strong>Vehicle Plate:</strong> NAX 1234</p>
                            <p class="mb-1"><strong>Contact:</strong> +63 912 345 6789</p>
                            <p class="mb-0"><strong>Address:</strong> Tanauan City, Batangas</p>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Arrow Icon -->
                <button class="btn p-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#driver-details" aria-expanded="false" aria-controls="driverDetails">
                    <img src="../assets/images/drop-down.png" alt="Dropdown" class="drop-arrow" id="arrow-icon" width="20">
                </button>

            </div>
        </div>

        <div class="container-fluid my-4 align-items-center rounded-5 text-center">
            <button class="arrive-button rounded-pill text-bold">ARRIVED SAFELY</button>
        </div>
    </div>

    <!-- NAVBAR -->
    <?php include '../assets/shared/navbarPassenger.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- For map integration -->
    <script src="../assets/js/homePage/map.js"></script>

    <!-- Toggling Details -->
    <script src="../assets/js/homePage/details.js"></script>
</body>

</html>