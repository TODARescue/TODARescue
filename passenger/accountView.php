<?php
session_start();
require_once '../assets/shared/connect.php';

if (!isset($_SESSION['userId'])) {
    header('Location: ../login.php');
    exit;
}

$userId = $_SESSION['userId'];

$stmt = $conn->prepare("SELECT firstName, lastName, contactNumber, email, photo FROM users WHERE userId = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$success = isset($_GET['updated']) ? "Profile updated successfully!" : '';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Driver | Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body class="bg-dark d-flex justify-content-center align-items-center vh-100">
    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">

                <div class="card bg-white w-100 h-100 d-flex flex-column p-0 rounded-0 rounded-bottom-4 shadow-lg"
                    style="--bs-border-radius-bottom: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <?php include '../assets/shared/header.php'; ?>

                    <div class="list-group list-group-flush m-2 px-0 w-100 flex-grow-1 overflow-auto"
                        style="padding-top: 110px;">

                        <?php if ($success): ?>
                            <div class="alert alert-success mx-3"><?= $success ?></div>
                        <?php endif; ?>

                        <!-- Profile Header -->
                        <div class="px-3 pt-3 pb-2 text-secondary fw-bold text-uppercase">
                            Profile
                        </div>

                        <form class="w-100 px-3">

                            <!-- Profile Info -->
                            <div class="list-group-item list-group-item-action py-3 border-0 px-0">
                                <div class="d-flex align-items-center">
                                    <?php
                                    $photoPath = !empty($user['photo'])
                                        ? '../assets/images/drivers/' . htmlspecialchars($user['photo'])
                                        : '';
                                    ?>
                                    <img src="<?= $photoPath ?>"
                                        onerror="this.onerror=null; this.src='../assets/images/profile-default.png';"
                                        alt="Profile Photo" class="rounded-circle me-3"
                                        style="width: 65px; height: 65px; object-fit: cover;">
                                    <div class="flex-grow-1 d-flex justify-content-between align-items-center">
                                        <span>
                                            <?= htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Details Header -->
                            <div class="pt-3 pb-1 text-secondary fw-bold text-uppercase small">
                                Account Details
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-4 my-2">
                                <label class="form-label fw-bold">Phone Number</label>
                                <input type="tel" class="form-control border-0 rounded-0 shadow-none p-0"
                                    value="<?= htmlspecialchars($user['contactNumber']) ?>" readonly
                                    style="border-bottom: 1px solid #dee2e6;" />
                            </div>

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" class="form-control border-0 rounded-0 shadow-none p-0"
                                    value="<?= htmlspecialchars($user['email']) ?>" readonly
                                    style="border-bottom: 1px solid #dee2e6;" />
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex mt-4">
                                <div class="d-flex justify-content-start gap-3 px-3 flex-nowrap" style="width: fit-content;">
                                    <a href="rideHistory.php" style="text-decoration: none;">
                                        <button type="button" class="btn text-black px-4"
                                            style="background-color: #dcdcdc; border-radius: 15px; white-space: nowrap;">
                                            View Ride History
                                        </button>
                                    </a>
                                    <a href="accountEdit.php" style="text-decoration: none;">
                                        <button type="button" class="btn text-white px-4"
                                            style="background-color: #24b3a7; border-radius: 15px; white-space: nowrap;">
                                            Edit Profile
                                        </button>
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>

                    <?php include '../assets/shared/navbarPassenger.php'; ?>

                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
                </div>

            </div>
        </div>
    </div>

    <!-- Change status -->
    <script>
        document.addEventListener("visibilitychange", () => {
            if (document.visibilityState === "hidden") {
                updateStatus(0);
            } else {
                updateStatus(2);
            }
        });

        function updateStatus(state) {
            fetch(`../assets/php/updateStatus.php?visibility=${state}`)
                .catch(err => console.error("Failed to update status:", err));
        }
    </script>
</body>


</html>