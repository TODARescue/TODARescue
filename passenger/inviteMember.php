<?php
session_start();
require_once '../assets/php/connect.php';

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

// Check if user has a circle or is a member of a circle
$query = "SELECT c.circleId, c.circleName, c.inviteCode, cm.role FROM circles c 
          INNER JOIN circlemembers cm ON c.circleId = cm.circleId 
          WHERE cm.userId = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$circle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$circle) {
    header('Location: circle.php');
    exit;
}

$circleId = $circle['circleId'];
$circleName = $circle['circleName'];
$inviteCode = $circle['inviteCode'];
$userRole = $circle['role'];

// Check if the user is an admin or owner
if ($userRole !== 'admin' && $userRole !== 'owner') {
    header('Location: circleDetails.php');
    exit;
}

// Generate new invite code if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generateNewCode'])) {
    // Generate a random 7-character code (3 numbers, dash, 3 letters)
    $numbers = rand(100, 999);
    $letters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
    $newInviteCode = $numbers . ' - ' . $letters;
    
    // Update the invite code in the database
    $updateQuery = "UPDATE circles SET inviteCode = ? WHERE circleId = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    
    if ($updateStmt->execute([$newInviteCode, $circleId])) {
        $inviteCode = $newInviteCode;
        $successMsg = 'New invite code generated successfully!';
    } else {
        $errorMsg = 'Failed to generate new invite code. Please try again.';
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TODA Rescue - Invite Member</title>
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
            <div class="col-12 col-md-6 col-lg-4" >
                
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
                        <p class="text-muted mb-0">This code is active for 2 days</p>
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
</body>

</html>