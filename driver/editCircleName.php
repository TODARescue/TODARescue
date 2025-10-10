<?php
session_start();
require_once '../assets/shared/connect.php';
include '../assets/php/checkLogin.php';

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['userId'];
$errorMsg = '';
$successMsg = '';
$circleName = '';
$circleId = '';


$circleId = isset($_GET['circleId']) ? $_GET['circleId'] : null;

// If no circleId is provided, redirect to circle.php
if (!$circleId) {
    header('Location: circle.php');
    exit;
}

// Check if user is a member of this circle and get their role and circle name
$query = "SELECT c.circleName, cm.role FROM circles c 
          INNER JOIN circlemembers cm ON c.circleId = cm.circleId 
          WHERE cm.userId = ? AND c.circleId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $circleId);
$stmt->execute();
$result = $stmt->get_result();
$circle = $result->fetch_assoc();


if (!$circle) {
    header('Location: circle.php');
    exit;
}

$circleName = $circle['circleName'];
$userRole = $circle['role'];

// Check if the user is an admin or owner
if ($userRole !== 'admin' && $userRole !== 'owner') {
    header('Location: circleDetails.php?circleId=' . $circleId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newCircleName'])) {
    $newCircleName = trim($_POST['newCircleName']);

    // Validate new circle name
    if (empty($newCircleName)) {
        $errorMsg = 'Circle name cannot be empty';
    } elseif (strlen($newCircleName) > 255) {
        $errorMsg = 'Circle name is too long (max 255 characters)';
    } else {
        $checkQuery = "SELECT circleId FROM circles WHERE circleName = ? AND circleId != ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("si", $newCircleName, $circleId);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $errorMsg = 'This circle name is already taken. Please choose another one.';
        } else {
            // Update circle name
            $updateQuery = "UPDATE circles SET circleName = ? WHERE circleId = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $newCircleName, $circleId);
            if ($updateStmt->execute()) {

                $circleName = $newCircleName;
                $successMsg = 'Circle name updated successfully!';
                // Show success modal and redirect
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const successModal = new bootstrap.Modal(document.getElementById("successModal"));
                        successModal.show();
                        
                        // Redirect after modal is closed
                        document.getElementById("successModal").addEventListener("hidden.bs.modal", function () {
                            window.location.href = "circleDetails.php?circleId=' . $circleId . '";
                        });
                    });
                </script>';
            } else {
                $errorMsg = 'Failed to update circle name. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Driver | Edit Circle Name</title>
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

                    <div class="container-fluid mt-5 pt-5">
                        <div class="row">
                            <div class="col-12 px-4 pt-4">
                                <h4 class="fs-5 mb-4">Edit Circle Name</h4>

                                <?php if ($errorMsg): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $errorMsg; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($successMsg): ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php echo $successMsg; ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="">
                                    <div class="mb-3">
                                        <label for="newCircleName" class="form-label">Circle Name</label>
                                        <input type="text" class="form-control" id="newCircleName" name="newCircleName"
                                            value="<?php echo htmlspecialchars($circleName); ?>" required>
                                    </div>
                                    <div class="d-flex gap-3 mt-4 justify-content-center">
                                        <a href="circleDetails.php" class="btn rounded-pill px-4"
                                            style="background-color: #dcdcdc; font-weight: 600;">
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn rounded-pill px-4 text-white"
                                            style="background-color: #1cc8c8; font-weight: 600;">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal fade" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <h5 class="fw-bold mb-2" id="successModalLabel">Success</h5>
                <p class="mb-4" id="successModalMessage" style="font-size: 0.95rem;">
                    Circle name updated successfully!
                </p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn rounded-pill px-4 text-white"
                        style="background-color: #1cc8c8; font-weight: 600;" data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarPassenger.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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