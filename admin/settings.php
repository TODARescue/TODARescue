<?php
session_start();
require_once '../assets/shared/connect.php';
include '../assets/php/checkLogin.php';


$userId = $_SESSION['userId'] ?? null;

if (!$userId) {
    header("Location: ../login.php");
    exit();
}

// Fetch admin info
$query = "SELECT * FROM users WHERE userID = $userId AND role = 'admin' LIMIT 1";
$result = executeQuery($query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "Access Denied.";
    exit();
}

// Check for error or success messages
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$showPasswordModal = !empty($error);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin | Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100" style="background-color: #2c2c2c;">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">
                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-radius: 0 0 25px 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <!-- HEADER -->
                    <?php include '../assets/shared/header.php'; ?>

                    <!-- Settings List -->
                    <div class="list-group list-group-flush px-0 w-100" style="padding-top: 100px;">
                        <!-- Subheader -->
                        <div class="px-3 pt-1 my-2 pb-1 text-secondary fw-bold text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Admin Settings
                        </div>

                        <a href="../admin/about.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light">
                                About
                            </div>
                        </a>

                        <!-- Change Password Option -->
                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light"
                            data-bs-toggle="modal" data-bs-target="#passwordModal">
                            Update Password
                        </div>

                        <!-- ✅ Survey Form Button -->
                        <a href="https://forms.gle/EJ4QKFumr7bbLUdaA" target="_blank"
                            style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light">
                                Survey Form
                            </div>
                        </a>


                        <!-- Log Out -->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"
                            style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light">
                                Log Out
                            </div>
                        </a>
                    </div>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success m-4 mb-0"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Password Change Modal -->
    <div class="modal fade <?= $showPasswordModal ? 'show d-block' : '' ?>" id="passwordModal" tabindex="-1"
        aria-hidden="true" style="<?= $showPasswordModal ? 'display: block;' : '' ?>">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <h5 class="modal-title mb-3">Change Password</h5>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="../assets/php/updatePassword.php">
                    <div class="mb-3">
                        <label for="oldPassword" class="form-label">Old Password</label>
                        <input type="password" class="form-control" id="oldPassword" name="oldPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                    </div>
                    <input type="hidden" name="userId" value="<?= $userId ?>">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Log Out Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <h5 class="fw-bold mb-2">Confirm Log Out</h5>
                <p class="mb-4" style="font-size: 0.95rem;">Are you sure you want to Log Out?</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn rounded-pill px-4"
                        style="background-color: #dcdcdc; font-weight: 600;" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <a href="../logOut.php" class="btn rounded-pill px-4 text-white"
                        style="background-color: #1cc8c8; font-weight: 600;">
                        Yes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarAdmin.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($showPasswordModal): ?>
        <script>
            const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
            modal.show();
        </script>
    <?php endif; ?>
</body>

</html>
