<?php
session_start();
require_once '../assets/php/connect.php';

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
    <title>TODA Rescue - Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
</head>

<body class="bg-dark d-flex justify-content-center align-items-center min-vh-100">
    <div class="container-fluid p-0">
        <div class="row g-0">
            <div class="col-12">
                <div class="card bg-white min-vh-100 d-flex flex-column rounded-0 shadow-lg">

                    <?php include '../assets/shared/header.php'; ?>

                    <div class="list-group list-group-flush flex-grow-1 overflow-auto pt-5 px-3">

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <div class="text-secondary fw-bold text-uppercase small mb-3">
                            Profile
                        </div>

                        <form>
                            <div class="list-group-item list-group-item-action py-3 border-0 px-0 bg-transparent">
                                <div class="d-flex align-items-center">
                                    <?php
                                    $photoPath = !empty($user['photo']) ? '../assets/images/drivers/' . htmlspecialchars($user['photo']) : '';
                                    ?>
                                    <img src="<?= $photoPath ?>"
                                        onerror="this.onerror=null; this.src='../assets/images/profile-default.png';"
                                        alt="Profile Photo" class="rounded-circle me-3 flex-shrink-0"
                                        style="width: 65px; height: 65px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <?= htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-secondary fw-bold text-uppercase small">
                                Account Details
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Phone Number</label>
                                <input type="tel" class="form-control border-0 border-bottom rounded-0 shadow-none p-0"
                                    value="<?= htmlspecialchars($user['contactNumber']) ?>" readonly />
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Email Address</label>
                                <input type="email"
                                    class="form-control border-0 border-bottom rounded-0 shadow-none p-0"
                                    value="<?= htmlspecialchars($user['email']) ?>" readonly />
                            </div>

                            <a href="rideHistory.php" class="text-decoration-none text-dark">
                                <div class="mb-3">
                                    <input type="text"
                                        class="form-control border-0 border-bottom rounded-0 shadow-none p-0"
                                        value="View Ride History" readonly />
                                </div>
                            </a>

                            <div class="d-flex justify-content-center mt-4">
                                <a href="accountEdit.php" class="btn text-white px-5"
                                    style="background-color: #24b3a7; border-radius: 15px;">
                                    Edit Profile
                                </a>
                            </div>
                        </form>
                    </div>

                    <?php include '../assets/shared/navbarDriver.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
