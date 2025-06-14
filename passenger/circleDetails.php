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

                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-start shadow px-4"
                        style="border-bottom-left-radius: 43px; border-bottom-right-radius: 43px; background-color: #fff; height: 100px;">
                        <a href="#" class="me-2 fs-5 fw-bold text-decoration-none text-dark">&#8592;</a>
                        <h5 class="mb-0 fw-bold">Group 1 Name </h5>
                    </div>

                    <!-- Options List -->
                    <div class="list-group list-group-flush mt-2 w-100">

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

                        <a href="../passenger/changeAdminStatusPassenger.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light">
                                Change Admin Status
                            </div>
                        </a>


                        <a href="../passenger/inviteMember.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light">
                                Add Circle Members
                            </div>
                        </a>

                        <a href="../passenger/removeCircleMember.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light">
                                Remove Circle Members
                            </div>
                        </a>

                        <a href="../passenger/leaveCircleModal.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light">
                                Leave Circle
                            </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Bottom Navigation (Placeholder for PHP Include) -->
    <?php include '../assets/shared/navbarPassenger.php'; ?>

    <!-- Bootstrap Icons (for pencil icon) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>