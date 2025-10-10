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
$inviteCode = '';
$circleName = '';

$circleId = isset($_GET['circleId']) ? $_GET['circleId'] : null;

// If no circleId is provided, redirect to circle.php
if (!$circleId) {
    header('Location: circle.php');
    exit;
}

// Check if user is a member of this circle and get their role
$query = "SELECT c.circleId, c.circleName, c.inviteCode, cm.role 
          FROM circles c 
          INNER JOIN circlemembers cm ON c.circleId = cm.circleId 
          WHERE cm.userId = ? AND c.circleId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $circleId);
$stmt->execute();
$result = $stmt->get_result();
$circle = $result->fetch_assoc();


// If user is not a member of this circle, redirect to circle.php
if (!$circle) {
    header('Location: circle.php');
    exit;
}

$userRole = $circle['role'];

// Check if the user is an admin or owner
if ($userRole !== 'admin' && $userRole !== 'owner') {
    header('Location: circleDetails.php?circleId=' . $circleId);
    exit;
}
$circleName = $circle['circleName'];
$inviteCode = $circle['inviteCode'];

// Generate new invite code if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generateNewCode'])) {
    // Generate a random unique 6-character alphanumeric code
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $maxAttempts = 10; // Prevent infinite loop
    $attempts = 0;
    $isUnique = false;

    while (!$isUnique && $attempts < $maxAttempts) {
        $newInviteCode = '';
        for ($i = 0; $i < 6; $i++) {
            $newInviteCode .= $chars[rand(0, strlen($chars) - 1)];
        }

        // Check if this code already exists
        $checkQuery = "SELECT COUNT(*) FROM circles WHERE inviteCode = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("s", $newInviteCode);
        $checkStmt->execute();
        $checkStmt->bind_result($codeCount);
        $checkStmt->fetch();
        $checkStmt->close();

        $codeExists = ($codeCount > 0);

        if (!$codeExists) {
            $isUnique = true;
        }

        $attempts++;
    }

    if ($isUnique) {
        // Update the invite code in the database
        $updateQuery = "UPDATE circles SET inviteCode = ? WHERE circleId = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $newInviteCode, $circleId);
        if ($updateStmt->execute()) {
            $inviteCode = $newInviteCode;
            $successMsg = 'New invite code generated successfully!';
        } else {
            $errorMsg = 'Failed to generate new invite code. Please try again.';
        }
    } else {
        $errorMsg = 'Failed to generate a unique invite code. Please try again.';
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passenger | Invite Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="bg-light">
    <!-- HEADER -->
    <?php include '../assets/shared/header.php'; ?>

    <!-- NAVBAR -->
    <?php include '../assets/shared/navbarPassenger.php'; ?>

    <div class="container-fluid py-5 mt-5 d-flex justify-content-center">
        <!-- Invite members text -->
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-6 col-lg-4">

                <?php if ($errorMsg): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $errorMsg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($successMsg): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $successMsg; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="mb-4 pt-3">
                    <h3 class="fw-bold mb-2">Invite Members to <?php echo htmlspecialchars($circleName); ?></h3>
                    <p class="text-muted mb-0">Share your code personally with your trusted friends</p>
                </div>

                <!-- Card -->
                <div class="card border-0 rounded-4 mb-4" style="background-color: #D9D9D9">
                    <div class="card-body text-center py-5">
                        <h2 class="fw-bold mb-2 fs-2"><?php echo htmlspecialchars($inviteCode); ?></h2>
                    </div>
                </div>

                <!-- Generate new code button -->
                <div class="d-flex justify-content-center mb-4">
                    <form method="post" action="">
                        <input type="hidden" name="generateNewCode" value="1">
                        <button type="submit" class="btn rounded-pill px-4 text-white"
                            style="background-color: #1cc8c8; font-weight: 600;">
                            Generate New Code
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
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