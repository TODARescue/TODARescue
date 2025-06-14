<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .list-group-item-action:hover {
            background-color: #e0e0e0 !important;
            cursor: pointer;
        }

        .modal-backdrop.show {
            opacity: 0.7;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">

                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-top-left-radius: 0; border-top-right-radius: 0; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <!-- Header -->
                    <div class="container-fluid position-fixed top-0 start-0 end-0 bg-white shadow rounded-5"
                        style="z-index: 1030;">
                        <div class="row">
                            <div class="col d-flex align-items-center p-2 rounded-bottom-4">
                                <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Back" class="img-fluid m-2"
                                    style="height: 40px;" />
                                <h3 class="fw-bold m-0 ps-2">Settings</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Settings List -->
                    <div class="list-group list-group-flush px-0 w-100" style="padding-top: 80px;">

                        <!-- Subheader -->
                        <div class="px-3 pt-3 pb-1 text-secondary fw-bold text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Circle Settings
                        </div>

                        <a href="../passenger/circleDetails.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light">
                                Circle Management
                            </div>
                        </a>

                        <div
                            class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom border-secondary w-100 bg-light">
                            <span>Location Sharing</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" checked>
                            </div>
                        </div>

                        <!-- Universal Settings -->
                        <div class="px-3 pt-3 pb-1 text-secondary fw-bold text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Universal Settings
                        </div>

                        <a href="../passenger/accountView.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light">
                                Account
                            </div>
                        </a>

                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light"
                            onclick="handleClick('privacySecurity')">
                            Privacy and Security
                        </div>

                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light"
                            onclick="handleClick('about')">
                            About
                        </div>

                        <!-- Leave Circle / Log Out (with modal) -->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#leaveCircleModal"
                            style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light">
                                Log Out
                            </div>
                        </a>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Leave Circle Modal -->
    <div class="modal fade" id="leaveCircleModal" tabindex="-1" aria-labelledby="leaveCircleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">
                <div class="modal-header bg-light border-bottom-0">
                    <h5 class="modal-title" id="leaveCircleModalLabel">Confirm Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    Are you sure you want to Log Out?
                </div>
                <div class="modal-footer justify-content-center border-top-0">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <a href="../passenger/leaveCircleAction.php" class="btn btn-danger px-4">Yes</a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarPassenger.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>