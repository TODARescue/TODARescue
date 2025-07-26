<?php
session_start();
require_once '../assets/shared/connect.php';

// For testing purposes - set a default user ID
if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['userId'];

// Get circles the user is a member of
$query = "SELECT c.circleId, c.circleName, cm.role 
          FROM circles c 
          INNER JOIN circlemembers cm ON c.circleId = cm.circleId 
          WHERE cm.userId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userCircles = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Passenger | Circle Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .circle-link {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }

        .circle-link:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">

            <!-- HEADER -->
            <?php include '../assets/shared/header.php'; ?>

                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-top-left-radius: 0; border-top-right-radius: 0; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <div style="padding-top: 100px;">
                        <div class="p-4">
                            <h6 class="fw-bold mb-4">Circle List</h6>

                            <?php if (count($userCircles) > 0): ?>
                                <?php foreach ($userCircles as $circle): ?>
                                    <!-- Circle item -->
                                    <a href="circleDetails.php?circleId=<?php echo $circle['circleId']; ?>" class="circle-link">
                                        <div class="d-flex align-items-center mb-3 p-2 rounded">
                                            <div class="rounded-circle d-flex justify-content-center align-items-center me-3"
                                                style="width: 48px; height: 48px; background-color: #1cc8c8;">
                                                <i class="bi bi-people-fill text-white fs-5"></i>
                                            </div>
                                            <span class="fw-medium"><?php echo htmlspecialchars($circle['circleName']); ?></span>
                                            <span class="ms-auto badge <?php echo $circle['role'] == 'owner' ? 'bg-danger' : ($circle['role'] == 'admin' ? 'bg-primary' : 'bg-secondary'); ?>">
                                                <?php echo ucfirst($circle['role']); ?>
                                            </span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-info">You're not a member of any circles yet.</div>
                            <?php endif; ?>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-center pt-2 pb-0 mt-4 mb-0 gap-4">
                                <a href="../passenger/createCircle.php"><button class="btn btn-sm rounded-pill px-4" style="background-color: #dcdcdc;">Create Circle</button></a>
                                <a href="../passenger/joinCircle.php"><button class="btn btn-sm rounded-pill px-4" style="background-color: #dcdcdc;">Join Circle</button></a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarPassenger.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/groupPage/navbar.js"></script>
</body>

</html>