<?php
session_start();
include '../assets/shared/connect.php';
include '../assets/php/checkLogin.php';

$userId = $_GET['userId'] ?? null;

// Recover logic
if (isset($_POST['recover'])) {
    $recoverStmt = $conn->prepare("UPDATE users SET isDeleted = 0 WHERE userId = ?");
    $recoverStmt->bind_param("i", $userId);
    $recoverStmt->execute();
    header("Location: driverView.php?userId=" . $userId);
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

            <?php if ($userData['isDeleted'] == 1): ?>
                <button data-bs-toggle="modal" data-bs-target="#recoverModal"
                    class="btn btn-sm text-white rounded-pill px-3" style="background-color: #1cc8c8;">
                    <i class="bi bi-arrow-clockwise me-1"></i> Recover
                </button>
            <?php else: ?>
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
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-center mb-1">
            <img src="../assets/images/drivers/<?php echo htmlspecialchars($userData['photo']) ?: 'profile-default.png'; ?>"
                alt="Profile" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover;" />
        </div>

        <?php if ($userData['isDeleted'] == 1): ?>
            <p class="text-center text-danger fw-bold small mt-1 mb-3">Inactive account</p>
        <?php endif; ?>
        <div class="px-2 mt-4">
            <?php
            $fields = [
                'First Name' => htmlspecialchars($userData['firstName']),
                'Last Name' => htmlspecialchars($userData['lastName']),
                'Contact Number' => htmlspecialchars($userData['contactNumber']),
                'Tricycle Model' => htmlspecialchars($driverData['model']),
                'Plate Number' => htmlspecialchars($driverData['plateNumber']),
                'Permanent Address' => htmlspecialchars($driverData['address']),
                'Toda Registration' => htmlspecialchars($driverData['todaRegistration']),
                'Verification' => $driverData['isVerified'] == 1 ? 'Verified' : 'Not Verified'
            ];

            foreach ($fields as $label => $value) {
                echo '
        <div class="row py-2 border-bottom">
            <div class="col-6 fw-semibold text-secondary">' . $label . ':</div>
            <div class="col-6 text-break">' . $value . '</div>
        </div>';
            }
            ?>
        </div>

        <!-- Delete Modal -->
        <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                    style="width: 85%; max-width: 320px; margin: auto;">
                    <h5 class="fw-bold mb-2" id="deleteModalLabel">Confirm Deletion</h5>
                    <p class="mb-4" style="font-size: 0.95rem;">Are you sure you want to delete this driver? This action
                        cannot be undone.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn rounded-pill px-4"
                            style="background-color: #dcdcdc; font-weight: 600;" data-bs-dismiss="modal">Cancel</button>
                        <a id="confirmDeleteBtn" href="#" class="btn rounded-pill px-4 text-white"
                            style="background-color: #1cc8c8; font-weight: 600;">Yes</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recover Modal -->
        <div id="recoverModal" class="modal fade" tabindex="-1" aria-labelledby="recoverModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                    style="width: 85%; max-width: 320px; margin: auto;">
                    <h5 class="fw-bold mb-2" id="recoverModalLabel">Confirm Recovery</h5>
                    <p class="mb-4" style="font-size: 0.95rem;">Are you sure you want to recover this driver account?
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn rounded-pill px-4" style="background-color: #dcdcdc;"
                            data-bs-dismiss="modal">Cancel</button>
                        <form method="post">
                            <button type="submit" name="recover" class="btn rounded-pill px-4 text-white"
                                style="background-color: #1cc8c8;">Yes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php include '../assets/shared/navbarAdmin.php'; ?>

    <script>
        const deleteModal = document.getElementById('deleteModal');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const triggerButton = event.relatedTarget;
            const userId = triggerButton.getAttribute('data-user-id');
            confirmDeleteBtn.href = 'deleteDriver.php?userId=' + userId;
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>