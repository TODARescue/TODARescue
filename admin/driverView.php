<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TODA Rescue - Drivers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-white">

    <div class="container-fluid pt-4" style="max-width: 100%; overflow-x: hidden;">
        <div class="text-center mb-3">
            <h5 class="fw-bold">TODA Rescue</h5>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4 px-3">
            <div class="d-flex align-items-center">
            <a href="#" class="me-2 text-decoration-none">
                    <img src="../assets/images/arrow-back-admin.svg" alt="Back" style="width: 15px; height: 15px;">
                </a>
                <h5 class="fw-semibold m-0">Drivers</h5>
            </div>
            <div class="d-flex gap-2">
            <button class="btn btn-info btn-sm rounded-circle">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm rounded-circle">
                            <i class="bi bi-trash-fill"></i>
                        </button>
            </div>
        </div>

        <!-- Profile Image -->
        <div class="d-flex justify-content-center mb-4">
            <img src="../assets/images/logo.png" alt="Profile" class="rounded-circle"
                style="width: 70px; height: 70px; object-fit: cover;" />
        </div>

        <!-- Driver Info -->
        <div class="px-2" style="overflow-x: hidden;">
            <div class="row mb-2">
                <div class="col-6 fw-medium">First Name:</div>
                <div class="col-6 text-truncate">Lorem Ipsum</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Last Name:</div>
                <div class="col-6 text-truncate">Lorem Ipsum</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Contact Number:</div>
                <div class="col-6 text-truncate">Lorem Ipsum</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Tricycle Details:</div>
                <div class="col-6 text-truncate">Lorem Ipsum</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Tricycle Number:</div>
                <div class="col-6 text-truncate">Lorem Ipsum</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Permanent Address:</div>
                <div class="col-6 text-truncate">Lorem Ipsum</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Toda Registration:</div>
                <div class="col-6 text-truncate">Lorem Ipsum</div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Verification:</div>
                <div class="col-6 text-truncate">Verified</div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarAdmin.php'; ?>

</body>

</html>
