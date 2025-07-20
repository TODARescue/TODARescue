<?php

include '../assets/shared/connect.php';

$userId = $_GET['userId'] ?? null;

if (!$userId) {
    echo "No user ID provided.";
    exit;
}

$userSql = "SELECT * FROM users WHERE userId = $userId";
$userResult = mysqli_query($conn, $userSql);
$userData = mysqli_fetch_assoc($userResult);

$driverSql = "SELECT * FROM drivers WHERE userId = $userId";
$driverResult = mysqli_query($conn, $driverSql);
$driverData = mysqli_fetch_assoc($driverResult);

if (!$userData || !$driverData) {
    echo "Driver not found.";
    exit;
}

?>

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

        <div class="d-flex align-items-center justify-content-between mb-4 ps-2">
            <div class="d-flex align-items-center">
                <a href="drivers.php" class="me-2 text-decoration-none">
                    <img src="../assets/images/arrow-back-admin.svg" alt="Back" style="width: 15px; height: 15px;">
                </a>
                <h5 class="fw-semibold m-0">Drivers</h5>
            </div>
            <div class="d-flex gap-2">
                <a href="editProfileDriver.php?userId=<?php echo $userId; ?>"
                    class="btn btn-info btn-sm rounded-circle text-white">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <button class="btn btn-danger btn-sm rounded-circle" data-bs-toggle="modal"
                    data-bs-target="#deleteModal" data-user-id="<?php echo $userId; ?>">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-4">
            <img src="../assets/images/drivers/<?php echo htmlspecialchars($userData['photo']); ?>" alt="Profile"
                class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover;" />
        </div>


        <div class="px-2" style="overflow-x: hidden;">
            <div class="row mb-2">
                <div class="col-6 fw-medium">First Name:</div>
                <div class="col-6 text-truncate"><?php echo htmlspecialchars($userData['firstName']); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Last Name:</div>
                <div class="col-6 text-truncate"><?php echo htmlspecialchars($userData['lastName']); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Contact Number:</div>
                <div class="col-6 text-truncate"><?php echo htmlspecialchars($userData['contactNumber']); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Tricycle Model:</div>
                <div class="col-6 text-truncate"><?php echo htmlspecialchars($driverData['model']); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Plate Number:</div>
                <div class="col-6 text-truncate"><?php echo htmlspecialchars($driverData['plateNumber']); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Permanent Address:</div>
                <div class="col-6 text-truncate"><?php echo htmlspecialchars($driverData['address']); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Toda Registration:</div>
                <div class="col-6 text-truncate"><?php echo htmlspecialchars($driverData['todaRegistration']); ?></div>
            </div>
            <div class="row mb-2">
                <div class="col-6 fw-medium">Verification:</div>
                <div class="col-6 text-truncate">
                    <?php echo $driverData['isVerified'] == 1 ? 'Verified' : 'Not Verified'; ?>
                </div>
            </div>
        </div>

        <?php include '../assets/shared/navbarAdmin.php'; ?>


        <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
 
                <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                    style="width: 85%; max-width: 320px; margin: auto;">
                    <h5 class="fw-bold mb-2" id="deleteModalLabel">Confirm Deletion</h5>
                    <p class="mb-4" style="font-size: 0.95rem;">
                        Are you sure you want to delete this driver? This action cannot be undone.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn rounded-pill px-4"
                            style="background-color: #dcdcdc; font-weight: 600;" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <a id="confirmDeleteBtn" href="#" class="btn rounded-pill px-4 text-white"
                            style="background-color: #1cc8c8; font-weight: 600;">
                            Yes
                        </a>
                    </div>
                </div>
            </div>
        </div>

</body>
<script>
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    deleteModal.addEventListener('show.bs.modal', function (event) {
        const triggerButton = event.relatedTarget;
        const userId = triggerButton.getAttribute('data-user-id');


        confirmDeleteBtn.href = 'deleteDriver.php?userId=' + userId;
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous"></script>


</html>