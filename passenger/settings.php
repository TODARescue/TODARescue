<?php
session_start();
require_once '../assets/shared/connect.php';

$userId = $_SESSION['userId'] ?? null;
$isSharing = 1; // default

// Fetch user's sharing status from circlemembers table
$circleQuery = "SELECT isSharing FROM circlemembers WHERE userId = $userId LIMIT 1";
$result = executeQuery($circleQuery);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $isSharing = (int) $row['isSharing'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Passenger | Settings</title>
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

        .form-check-input {
            border-color: #2daaa7;
            --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%232daaa7'/%3e%3c/svg%3e") !important;
        }

        .form-check-input:checked {
            background-color: #2daaa7;
            border-color: #2daaa7;
            --bs-form-switch-bg: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='white'/%3e%3c/svg%3e") !important;
        }

        .form-check-input:checked::before {
            background-color: #2daaa7 !important;
        }

        .form-check-input::before {
            background-color: #fff !important;
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

                    <!-- HEADER -->
                    <?php include '../assets/shared/header.php'; ?>

                    <!-- Settings List -->
                    <div class="list-group list-group-flush px-0 w-100" style="padding-top: 100px;">

                        <!-- Subheader -->
                        <div class="px-3 pt-1 my-2 pb-1 text-secondary fw-bolder text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Circle Settings
                        </div>

                        <a href="../passenger/circle.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light">
                                Circle Management
                            </div>
                        </a>

                        <div
                            class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom w-100 bg-light">
                            <span>Location Sharing</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="sharing-toggle"
                                    <?= $isSharing === 1 ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <!-- Universal Settings -->
                        <div class="px-3 pt-1 my-2 pb-1 text-secondary fw-bolder text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Universal Settings
                        </div>

                        <a href="../passenger/accountView.php" style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light">
                                Account
                            </div>
                        </a>

                        <a href="../passenger/about.php" style="text-decoration: none; color: inherit;">
                            <div class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light"
                                onclick="handleClick('about')">
                                About
                            </div>
                        </a>

                        <!--: Survey Forms Button -->
                        <a href="https://forms.gle/EJ4QKFumr7bbLUdaA" target="_blank"
                            style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light">
                                Survey Form
                            </div>
                        </a>

                        <!-- Log Out (with modal) -->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#leaveCircleModal"
                            style="text-decoration: none; color: inherit;">
                            <div
                                class="list-group-item list-group-item-action py-3 text-black border-bottom w-100 bg-light">
                                Log Out
                            </div>
                        </a>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Log Out Confirmation Modal -->
    <div id="leaveCircleModal" class="modal fade" tabindex="-1" aria-labelledby="leaveCircleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <h5 class="fw-bold mb-2" id="leaveCircleModalLabel">Confirm Log Out</h5>
                <p class="mb-4" style="font-size: 0.95rem;">
                    Are you sure you want to Log Out?
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn rounded-pill px-4"
                        style="background-color: #dcdcdc; font-weight: 600;" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <a href="../logout.php" class="btn rounded-pill px-4 text-white"
                        style="background-color: #1cc8c8; font-weight: 600;">
                        Yes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarPassenger.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('sharing-toggle').addEventListener('change', function () {
            const isChecked = this.checked ? 1 : 0;

            fetch('../assets/php/updateSharingStatus.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'isSharing=' + isChecked
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        alert('Failed to update sharing status.');
                    }
                })
                .catch(error => {
                    console.error("Error updating sharing status:", error);
                    alert("Something went wrong while updating.");
                });
        });
    </script>
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