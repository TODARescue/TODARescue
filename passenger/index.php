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

    <!-- Glass Styling -->
    <!-- <link rel="stylesheet" href="../assets/css/glass.css"> -->
</head>

<body>
    <div class="modal fade" id="gpsWarningModal" tabindex="-1" aria-labelledby="gpsWarningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-2 border-teal">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title" id="gpsWarningModalLabel">📌 Location Outside Map Bounds</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

    <div class="position-relative vh-100 w-100">

        <!-- Fullscreen Map (Background) -->
        <div class="position-absolute top-0 start-0 w-100 h-100 z-0">
            <div id="map" class="w-100 h-100" style="pointer-events: auto;"></div>
        </div>

        <!-- Floating Content -->
        <div class="position-relative z-1 w-100 h-100 container py-3 pointer-pass">

            <!-- Logo and App Name -->
            <div class="d-flex align-items-center mb-3 position-absolute end-0 me-3">
                <img src="../assets/images/Logo.png" alt="TodaRescue" class="me-2" style="width: 40px; height: 40px;">
                <span class="fw-bold fs-5">TodaRescue</span>
            </div>

            <!-- Profile Card -->
            <div class="card rounded-4 glass shadow px-4 py-4 mb-4 start-50 translate-middle-x"
                style="background-color: #2ebcbc!important;top: 55%; width: 90%; max-width: 500px;">
                <div class="d-flex flex-row align-items-center justify-content-between profile-container" id="profile-details">

                    <!-- Profile Picture -->
                    <div class="me-3 profile-pic">
                        <img src="../assets/images/profile-default.png" alt="Profile" class="rounded-circle" width="50" height="50">
                    </div>

                    <!-- User Info -->
                    <div class="flex-grow-1 me-2">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 me-2">Juan Dela Cruz</h5>
                            <img src="../assets/images/verified.png" alt="Verified" style="width: 12px;">
                        </div>
                        <div class="align-items-center">
                            <small>Plate No:</small>
                            <b>NAX 1234</b>
                        </div>
                        <div class="collapse mt-3" id="driver-details">
                            <div class="border-top border-dark pt-2">
                                <p class="mb-1"><b>Tricycle Model:</b>DRV-2023-0012</p>
                                <p class="mb-1"><b>Toda Registration:</b> NAX 1234</p>
                                <p class="mb-1"><b>Contact:</b> +63 912 345 6789</p>
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
            <div class="container-fluid my-4 align-items-center rounded-5 text-center">
                <button class="arrive-button glass rounded-pill text-bold position-absolute start-50 translate-middle-x px-4 py-4"
                    style="background-color: rgb(46, 188, 188) !important; top: 75%; width: 90%; max-width: 200px;" id="arrive-button">
                    ARRIVED SAFELY
                </button>
            </div>
        </div>

        <!-- NAVBAR -->
        <?php include '../assets/shared/navbarPassenger.php'; ?>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

        <!-- For map integration -->
        <script src="../assets/js/sharedMap.js"></script>

        <!-- Toggling Details -->
        <script src="../assets/js/homePage/details.js"></script>
</body>

</html>