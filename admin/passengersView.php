<?php
include '../assets/shared/connect.php';
include '../assets/php/checkLogin.php';


$userId = $_GET['userId'] ?? null;

if (!$userId) {
    echo "User ID missing.";
    exit;
}

// Recover logic
if (isset($_POST['recover'])) {
    $recoverStmt = $conn->prepare("UPDATE users SET isDeleted = 0 WHERE userId = ?");
    $recoverStmt->bind_param("i", $userId);
    $recoverStmt->execute();
    header("Location: passengersView.php?userId=" . $userId);
    exit;
}

// Get user info
$userQuery = "SELECT * FROM users WHERE userId = ? AND role = 'passenger'";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

if (!$user) {
    echo "Passenger not found.";
    exit;
}

// Get ride history
$historyQuery = "
SELECT h.*, d.model, d.plateNumber,
       u.firstName AS driverFirstName, u.lastName AS driverLastName, 
       u.contactNumber AS driverContact, u.photo AS driverPhoto
FROM history h
JOIN drivers d ON h.driverId = d.driverId
JOIN users u ON d.userId = u.userId
WHERE h.userId = ?
ORDER BY h.dropoffTime DESC
";
$stmt = $conn->prepare($historyQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$historyResult = $stmt->get_result();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>TODARescue | Passengers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
</head>

<body class="bg-white d-flex justify-content-center align-items-start min-vh-100 pt-5">
    <?php include '../assets/shared/navbarAdmin.php'; ?>

    <div class="container px-4" style="max-width: 400px;">
        <div class="sticky-top rounded-bottom-2 bg-white pt-3 pb-1" style="z-index: 1020;">
            <h3 class="fw-bold text-center mb-3">TODA Rescue</h3>

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <a href="passengers.php" class="text-dark d-flex align-items-center me-1">
                        <img src="../assets/images/arrow-left.svg" alt="Back" width="15" height="15">
                    </a>
                    <h5 class="mb-0 fw">Passenger</h5>
                </div>

                <?php if ($user['isDeleted'] == 1): ?>
                    <button data-bs-toggle="modal" data-bs-target="#recoverModal" data-user-id="<?= $userId ?>"
                        class="btn btn-sm text-white rounded-pill px-3" style="background-color: #1cc8c8;">
                        <i class="bi bi-arrow-clockwise me-1"></i> Recover
                    </button>

                <?php else: ?>
                    <div class="d-flex gap-2">
                        <a href="editProfilePassenger.php?userId=<?= $userId ?>"
                            class="btn btn-info text-white btn-sm rounded-circle">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <button data-bs-toggle="modal" data-bs-target="#deleteModal" data-user-id="<?= $userId ?>"
                            class="btn btn-danger btn-sm rounded-circle">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mb-5">
            <img src="../assets/images/passengers/<?= htmlspecialchars($user['photo']) ?: 'profile-default.png' ?>"
                width="100" height="100" class="rounded-circle" style="object-fit: cover;">
            <h4 class="mt-2 mb-0 fw-bolder"><?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?></h4>
            <p class="mb-0 small text-center">Email: <?= htmlspecialchars($user['email']) ?></p>
            <p class="small text-center">Contact Number: <?= htmlspecialchars($user['contactNumber']) ?></p>
            <?php if ($user['isDeleted'] == 1): ?>
                <p class="text-danger fw-bold small mt-1">Inactive account</p>
            <?php endif; ?>
        </div>

        <h4 class="text-center mb-3">Ride History</h4>

        <?php if ($historyResult->num_rows > 0): ?>
            <?php while ($ride = $historyResult->fetch_assoc()): ?>
                <div class="card border-0 rounded-4 px-3 py-3 mb-4" style="background-color: #d9d9d9;">
                    <div class="d-flex align-items-center">
                        <img src="../assets/images/drivers/<?= htmlspecialchars($ride['driverPhoto']) ?: 'profile-default.png' ?>"
                            class="rounded-circle me-3" width="65" height="65" style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <p class="mb-1 fw-semibold">
                                <?= htmlspecialchars($ride['driverFirstName'] . ' ' . $ride['driverLastName']) ?>
                            </p>
                            <p class="mb-0 small">Tricycle Model: <?= htmlspecialchars($ride['model']) ?></p>
                            <p class="mb-0 small">Plate Number: <?= htmlspecialchars($ride['plateNumber']) ?></p>
                            <p class="mb-0 small">Contact Number: <?= htmlspecialchars($ride['driverContact']) ?></p>
                            <p class="mb-0 small">Arrived on: <?= date("F j, Y - g:i A", strtotime($ride['dropoffTime'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-muted">No ride history available.</p>
        <?php endif; ?>

        <div id="deleteModal" class="modal fade" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                    style="width: 85%; max-width: 320px; margin: auto;">
                    <h5 class="fw-bold mb-2" id="deleteModalLabel">Confirm Deletion</h5>
                    <p class="mb-4" style="font-size: 0.95rem;">
                        Are you sure you want to delete this passenger? This action cannot be undone.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn rounded-pill px-4" style="background-color: #dcdcdc;"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <a id="confirmDeleteBtn" href="#" class="btn rounded-pill px-4 text-white"
                            style="background-color: #1cc8c8;">
                            Yes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="recoverModal" class="modal fade" tabindex="-1" aria-labelledby="recoverModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                    style="width: 85%; max-width: 320px; margin: auto;">
                    <h5 class="fw-bold mb-2" id="recoverModalLabel">Confirm Recovery</h5>
                    <p class="mb-4" style="font-size: 0.95rem;">
                        Are you sure you want to recover this passenger account?
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn rounded-pill px-4" style="background-color: #dcdcdc;"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <form method="post">
                            <button type="submit" name="recover" class="btn rounded-pill px-4 text-white"
                                style="background-color: #1cc8c8;">
                                Yes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>
            const deleteModal = document.getElementById('deleteModal');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                confirmDeleteBtn.href = 'deletePassenger.php?userId=' + userId;
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>