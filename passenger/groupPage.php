<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Group Dropdown</title>
    <!-- FONTS -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- LEAFLET CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />


    <!-- Custom Styling -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Glass Styling -->
    <!-- <link rel="stylesheet" href="../assets/css/glass.css"> -->

    <style>
        .group-selector {
            background-color: #009688;
            color: #fff;
            border-radius: 999px;
            padding: 0.5rem 1.5rem;
            cursor: pointer;
            user-select: none;
            display: inline-flex;
            align-items: center;
        }

        .group-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.5rem;
        }

        .action-button {
            background-color: #D9D9D9;
            border: none;
            padding: 0.4rem 1rem;
        }

        .group-container {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            padding: 2.5rem;
            margin-top: 0.5rem;
            margin-top: 3rem;
            z-index: 3;
            display: none;
        }

        #member-container {
            position: fixed;
            top: 100vh;
            left: 0;
            right: 0;
            height: 100vh;
            overflow-y: auto;
            background: white;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
            border-top: 1px solid #ccc;
            transition: top 0.3s ease-in-out;
            z-index: 2;
            padding-top: 70px;
            padding-bottom: 100px;
        }

        #toggle-button {
            position: fixed;
            background-color: #2EBCBC;
            bottom: 10vh;
            right: 10px;
            z-index: 6;
        }
    </style>
</head>

<body>
    <!-- MODAL BOOTSTRAP -->
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
    <!-- MAP FULLSCREEN -->
    <div class="position-absolute top-0 start-0 w-100 h-100 z-1" id="map-container">
        <div id="map" class="w-100 h-100" style="pointer-events: auto;"></div>
    </div>

    <!-- Toggle button to show/hide the group container -->
    <button id="toggle-button" class="btn btn-primary rounded-circle glass-toggle p-4 text-dark d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
        <i class="bi bi-people-fill"></i>
    </button>

    <div class=" py-3 py-sm-1 container-fluid position-fixed top-0 text-center start-0 end-0 bg-transparent" style="z-index: 4;" id="header-color">
        <div class="d-inline-flex align-items-center px-5 py-1 rounded-pill glass-selector"
            style="background-color: #2ebcbc!important; cursor: pointer; user-select: none;"
            id="group-selector">
            <h4 class="m-0">
                Group 1
                </h2>
                <i class="bi bi-caret-down-fill ms-2" id="caret-icon"></i>
        </div>
    </div>

    <!-- Group container -->
    <div class="pt-2 pt-lg-5 group-container shadow rounded-bottom-5 bg-white" id="group-container">
        <button type="button" class="d-flex align-items-center my-4 p-0 border-0 bg-transparent">
            <img src="../assets/images/group-photo.png" alt="Group 1" class="group-image">
            <div class="ms-2">Group 1</div>
        </button>

        <button type="button" class="d-flex align-items-center my-4 p-0 border-0 bg-transparent">
            <img src="../assets/images/group-photo.png" alt="Group 2" class="group-image">
            <div class="ms-2">Group 2</div>
        </button>
        <div class="d-flex mt-3">
            <a href="./createCircle.php" class="text-decoration-none">
                <button type="button" class="btn rounded-pill action-button mx-3" style="font-size: 16px;">
                    Create Circle
                </button>
            </a>
            <a href="./joinCircle.php" class="text-decoration-none">
                <button type="button" class="btn rounded-pill action-button position-absolute end-3" style="font-size: 16px;">
                    Join Circle
                </button>
            </a>
        </div>
    </div>

    <div class="container-fluid px-2" id="member-container">
        <div id="member-content">
            <!--Member (to be populated in DB) JSON example -->
        </div>
    </div>


    <!-- NAVBAR -->
    <div class="position-relative" style="z-index: 5">
        <?php include '../assets/shared/navbarPassenger.php'; ?>
    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- LEAFLET JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- MAP JS -->
    <script src="../assets/js/sharedMap.js"></script>

    <!-- NAVBAR DROPDOWN -->
    <script src="../assets/js/groupPage/navbar.js"></script>

    <!-- SCROLLING AND POPULATE-->
    <script src="../assets/js/groupPage/members.js"></script>

    <!-- Turf js to handle polygons -->
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>


</body>

</html>