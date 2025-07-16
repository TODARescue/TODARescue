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

// Check if user has a circle
$query = "SELECT c.circleId, cm.role FROM circles c 
          INNER JOIN circlemembers cm ON c.circleId = cm.circleId 
          WHERE cm.userId = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$userCircle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userCircle) {
    header('Location: circle.php');
    exit;
}

$circleId = $userCircle['circleId'];
$userRole = $userCircle['role'];

// Only owners can change admin status
if ($userRole !== 'owner') {
    header('Location: circleDetails.php');
    exit;
}

// Process AJAX request for toggling admin status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggleAdmin') {
    if (isset($_POST['memberId'], $_POST['isAdmin'])) {
        $memberId = intval($_POST['memberId']);
        $isAdmin = $_POST['isAdmin'] === 'true' ? 'admin' : 'member';
        
        // Wag ibahin status ni owner
        $checkOwnerQuery = "SELECT role FROM circlemembers WHERE userId = ? AND circleId = ?";
        $checkStmt = $pdo->prepare($checkOwnerQuery);
        $checkStmt->execute([$memberId, $circleId]);
        $currentRole = $checkStmt->fetchColumn();
        
        if ($currentRole === 'owner') {
            echo json_encode(['success' => false, 'message' => 'Cannot change owner status']);
            exit;
        }
        
        // Update member role
        $updateQuery = "UPDATE circlemembers SET role = ? WHERE userId = ? AND circleId = ?";
        $updateStmt = $pdo->prepare($updateQuery);
        
        if ($updateStmt->execute([$isAdmin, $memberId, $circleId])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get all members of the circle
$membersQuery = "SELECT cm.userId, u.firstName, u.lastName, cm.role 
                FROM circlemembers cm 
                INNER JOIN users u ON cm.userId = u.userId 
                WHERE cm.circleId = ?";
$membersStmt = $pdo->prepare($membersQuery);
$membersStmt->execute([$circleId]);
$members = $membersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Change Admin Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">
    <div class="container-fluid p-0 m-0 h-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">
                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <!-- HEADER -->
                    <?php include '../assets/shared/header.php'; ?>

                    <!-- Status Messages -->
                    <div class="container-fluid mt-5 pt-4">
                        <?php if ($errorMsg): ?>
                            <div class="alert alert-danger alert-dismissible fade show mx-4" role="alert">
                                <?php echo $errorMsg; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($successMsg): ?>
                            <div class="alert alert-success alert-dismissible fade show mx-4" role="alert">
                                <?php echo $successMsg; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Member List -->
                    <div class="container-fluid mt-2 pt-2">
                        <div class="row">
                            <div class="col list-group list-group-flush px-0 w-100">
                                <div class="mb-1">
                                    <h4 class="fs-5 mt-3 px-4">Admin Status</h4>
                                    <p class="text-muted small px-4">Toggle switches to change admin status</p>
                                </div>

                                <div class="container-fluid p-0">
                                    <div class="list-group">
                                        <?php foreach ($members as $member): ?>
                                            <?php
                                            $fullName = $member['firstName'] . ' ' . $member['lastName'];
                                            $isAdmin = $member['role'] === 'admin' || $member['role'] === 'owner';
                                            $isDisabled = $member['role'] === 'owner' || $member['userId'] == $userId;
                                            ?>
                                            <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary">
                                                <span class="fw-medium"><?php echo htmlspecialchars($fullName); ?>
                                                    <?php if ($member['role'] === 'owner'): ?>
                                                        <span class="badge bg-primary ms-2">Owner</span>
                                                    <?php endif; ?>
                                                </span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input admin-toggle" type="checkbox" 
                                                           data-member-id="<?php echo $member['userId']; ?>"
                                                           <?php echo $isAdmin ? 'checked' : ''; ?> 
                                                           <?php echo $isDisabled ? 'disabled' : ''; ?>>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarPassenger.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll(".admin-toggle").forEach(toggle => {
            toggle.addEventListener("change", function() {
                const memberId = this.getAttribute('data-member-id');
                const isAdmin = this.checked;
                
                // Show loading indicator
                this.disabled = true;
                
                // Send AJAX request to update admin status
                fetch('changeAdminStatusPassenger.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=toggleAdmin&memberId=${memberId}&isAdmin=${isAdmin}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        // Revert toggle if request failed
                        this.checked = !isAdmin;
                        alert(data.message || 'Failed to update admin status');
                    }
                    this.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.checked = !isAdmin;
                    this.disabled = false;
                    alert('An error occurred while updating admin status');
                });
            });
        });
    </script>
</body>

</html>