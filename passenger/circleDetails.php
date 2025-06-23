<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Circle Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">

                <!-- Main Card -->
                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <!-- HEADER -->
                    <?php include '../assets/shared/header.php'; ?>

                    <!-- Options List -->
                    <div class="list-group list-group-flush w-100"  style="padding-top: 120px;">

                        <div class="px-3 pt-3 pb-1 text-secondary fw-bold text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Circle Details
                        </div>

                        <div class="list-group-item py-3 text-black border-bottom border-secondary bg-light">
                            <span>Edit Circle Name <i class="bi bi-pencil-fill ms-1"></i></span>
                        </div>

                        <div class="px-3 pt-3 pb-1 text-secondary fw-bold text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Circle Management
                        </div>

                        <a href="../passenger/changeAdminStatusPassenger.php"
                            style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary bg-light">
                                Change Admin Status
                            </div>
                        </a>

                        <a href="../passenger/inviteMember.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary bg-light">
                                Add Circle Members
                            </div>
                        </a>

                        <a href="../passenger/removeCircleMember.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary bg-light">
                                Remove Circle Members
                            </div>
                        </a>

                        <!-- Modal Trigger -->
                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary bg-light"
                            data-bs-toggle="modal" data-bs-target="#leaveCircleModal">
                            Leave Circle
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Circle Modal -->
        <div id="leaveCircleModal" class="modal fade" tabindex="-1" aria-labelledby="leaveCircleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                    style="width: 85%; max-width: 320px; margin: auto;">
                    <h5 class="fw-bold mb-2" id="leaveCircleModalLabel">Leaving Circle</h5>
                    <p class="mb-4" style="font-size: 0.95rem;">
                        You will no longer see or share locations with this Circle. Are you sure you want to leave?
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn rounded-pill px-4"
                            style="background-color: #dcdcdc; font-weight: 600;" data-bs-dismiss="modal">
                            No
                        </button>
                        <a href="../passenger/leaveCircleAction.php" class="btn rounded-pill px-4 text-white"
                            style="background-color: #1cc8c8; font-weight: 600;">
                            Yes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <?php include '../assets/shared/navbarPassenger.php'; ?>

        <!-- Scripts -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </div>

</body>

</html>