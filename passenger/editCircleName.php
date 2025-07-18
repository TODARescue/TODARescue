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
$stmt = $pdo->prepare($query);
$stmt->execute([$userId, $circleId]);
$circle = $stmt->fetch(PDO::FETCH_ASSOC);

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
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute([$newCircleName, $circleId]);
        if ($checkStmt->rowCount() > 0) {
            $errorMsg = 'This circle name is already taken. Please choose another one.';
        } else {
            // Update circle name
            $updateQuery = "UPDATE circles SET circleName = ? WHERE circleId = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            
            if ($updateStmt->execute([$newCircleName, $circleId])) {
                $circleName = $newCircleName;
                $successMsg = 'Circle name updated successfully!';
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
    <title>TODA Rescue - Edit Circle Name</title>
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

    <?php include '../assets/shared/navbarPassenger.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>