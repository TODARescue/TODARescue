<?php
session_start();
require_once '../assets/php/connect.php';

// For testing purposes - set a default user ID
if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['userId'];
$errorMsg = '';
$successMsg = '';

// Check if user has a circle and is admin/owner
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

// Only admins and owners can remove members
if ($userRole !== 'admin' && $userRole !== 'owner') {
    header('Location: circleDetails.php');
    exit;
}

// Handle member removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'removeMember') {
    if (isset($_POST['memberId'])) {
        $memberId = intval($_POST['memberId']);
        
        // Check if trying to remove self
        if ($memberId === $userId) {
            echo json_encode(['success' => false, 'message' => 'You cannot remove yourself from the circle. Use the Leave Circle option instead.']);
            exit;
        }
        
        // Check if trying to remove the owner
        $checkRoleQuery = "SELECT role FROM circlemembers WHERE userId = ? AND circleId = ?";
        $checkRoleStmt = $pdo->prepare($checkRoleQuery);
        $checkRoleStmt->execute([$memberId, $circleId]);
        $memberRole = $checkRoleStmt->fetchColumn();
        
        if ($memberRole === 'owner') {
            echo json_encode(['success' => false, 'message' => 'You cannot remove the owner of the circle.']);
            exit;
        }
        
        // Check if admin is trying to remove another admin (only owner can do this)
        if ($userRole === 'admin' && $memberRole === 'admin') {
            echo json_encode(['success' => false, 'message' => 'You need to be the circle owner to remove other admins.']);
            exit;
        }
        
        // Remove member
        $removeQuery = "DELETE FROM circlemembers WHERE userId = ? AND circleId = ?";
        $removeStmt = $pdo->prepare($removeQuery);
        
        if ($removeStmt->execute([$memberId, $circleId])) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove member. Please try again.']);
        }
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get all members of the circle except the current user
$membersQuery = "SELECT cm.userId, u.firstName, u.lastName, cm.role 
                FROM circlemembers cm 
                INNER JOIN users u ON cm.userId = u.userId 
                WHERE cm.circleId = ? AND cm.userId != ?";
$membersStmt = $pdo->prepare($membersQuery);
$membersStmt->execute([$circleId, $userId]);
$members = $membersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Remove Circle Member</title>
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
                    <div class="container-fluid" style="padding-top: 30px;">
                        <div class="row">
                            <div class="col list-group list-group-flush px-0 w-100">
                                <div class="mb-1">
                                    <h4 class="fs-5 mt-3 px-4">Remove Members</h4>
                                </div>

                                <div class="container-fluid p-0">
                                    <div class="list-group">
                                        <?php if (count($members) === 0): ?>
                                            <div class="list-group-item py-4 px-4 text-center text-muted">
                                                No other members in this circle
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($members as $member): ?>
                                                <?php
                                                $fullName = $member['firstName'] . ' ' . $member['lastName'];
                                                $canRemove = $userRole === 'owner' || ($userRole === 'admin' && $member['role'] === 'member');
                                                ?>
                                                <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary"
                                                    <?php if ($canRemove): ?>onclick="openRemoveModal('<?php echo htmlspecialchars($fullName); ?>', <?php echo $member['userId']; ?>)"<?php endif; ?>>
                                                    <span class="fw-medium">
                                                        <?php echo htmlspecialchars($fullName); ?>
                                                        <?php if ($member['role'] === 'owner'): ?>
                                                            <span class="badge bg-primary ms-2">Owner</span>
                                                        <?php elseif ($member['role'] === 'admin'): ?>
                                                            <span class="badge bg-secondary ms-2">Admin</span>
                                                        <?php endif; ?>
                                                    </span>
                                                    <?php if ($canRemove): ?>
                                                        <img src="../assets/images/remove-member.svg" alt="Remove" style="max-width: 30px;" />
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Backdrop -->
                    <div id="modalBackdrop"
                        class="position-fixed top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center z-1"
                        style="background-color: rgba(255, 255, 255, 0.4);">
                        <!-- Modal Box -->
                        <div class="bg-white p-4 rounded-5 shadow text-center" style="width: 85%; max-width: 320px;">
                            <h5 class="fw-bold mb-2">Remove Member</h5>
                            <p class="mb-4" style="font-size: 0.95rem;">
                                Are you sure you want to remove <b id="modalMemberName">[Name]</b> from this circle?
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <button class="btn rounded-pill px-4" style="background-color: #dcdcdc; font-weight: 600;"
                                    onclick="closeModal()">No</button>
                                <button id="confirmRemoveBtn" class="btn rounded-pill px-4 text-white" 
                                    style="background-color: #1cc8c8; font-weight: 600;">
                                    Yes
                                </button>
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
        let currentMemberId = null;

        function openRemoveModal(name, memberId) {
            document.getElementById('modalMemberName').innerText = name;
            currentMemberId = memberId;
            document.getElementById('modalBackdrop').classList.remove('d-none');
            document.getElementById('modalBackdrop').classList.add('d-flex');
        }

        function closeModal() {
            document.getElementById('modalBackdrop').classList.remove('d-flex');
            document.getElementById('modalBackdrop').classList.add('d-none');
            currentMemberId = null;
        }

        document.getElementById('confirmRemoveBtn').addEventListener('click', function() {
            if (currentMemberId) {
                // Show loading state
                this.disabled = true;
                this.innerText = 'Removing...';
                
                // Send AJAX request to remove member
                fetch('removeCircleMember.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=removeMember&memberId=${currentMemberId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh page to show updated member list
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to remove member');
                        closeModal();
                        this.disabled = false;
                        this.innerText = 'Yes';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the member');
                    closeModal();
                    this.disabled = false;
                    this.innerText = 'Yes';
                });
            }
        });
    </script>
</body>

</html>