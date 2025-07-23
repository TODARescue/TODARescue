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

// Get circleId from URL parameter
$circleId = isset($_GET['circleId']) ? $_GET['circleId'] : null;

if (!$circleId) {
    header('Location: circle.php');
    exit;
}

// Check if user is a member of this circle and get their role
$query = "SELECT cm.role FROM circlemembers cm
          WHERE cm.userId = ? AND cm.circleId = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId, $circleId]);
$roleResult = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$roleResult) {
    header('Location: circle.php');
    exit;
}

$userRole = $roleResult['role'];

// Only owners can change admin status
if ($userRole !== 'owner') {
    header('Location: circleDetails.php?circleId=' . $circleId);
    exit;
}

// Process AJAX request for toggling admin status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggleAdmin') {
    if (isset($_POST['memberId'], $_POST['isAdmin'])) {
        $memberId = intval($_POST['memberId']);
        $isAdmin = $_POST['isAdmin'] === 'true' ? 'admin' : 'member';
        
        // Debug info
        error_log("Toggle admin request: memberId=$memberId, isAdmin=$isAdmin, circleId=$circleId");
        
        // Check if member exists in circle
        $checkMemberQuery = "SELECT COUNT(*) FROM circlemembers WHERE userId = ? AND circleId = ?";
        $checkMemberStmt = $pdo->prepare($checkMemberQuery);
        $checkMemberStmt->execute([$memberId, $circleId]);
        $memberExists = (int)$checkMemberStmt->fetchColumn();
        
        if ($memberExists === 0) {
            echo json_encode(['success' => false, 'message' => 'This user is not a member of the circle.']);
            exit;
        }
        
        // Check if trying to change owner status
        $checkOwnerQuery = "SELECT role FROM circlemembers WHERE userId = ? AND circleId = ?";
        $checkStmt = $pdo->prepare($checkOwnerQuery);
        $checkStmt->execute([$memberId, $circleId]);
        $currentRole = $checkStmt->fetchColumn();
        
        error_log("Current role: $currentRole");
        
        if ($currentRole === 'owner') {
            echo json_encode(['success' => false, 'message' => 'Cannot change owner status']);
            exit;
        }
        
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Update member role
            $updateQuery = "UPDATE circlemembers SET role = ? WHERE userId = ? AND circleId = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateResult = $updateStmt->execute([$isAdmin, $memberId, $circleId]);
            
            error_log("Update query executed. Result: " . ($updateResult ? "Success" : "Failed"));
            error_log("Affected rows: " . $updateStmt->rowCount());
            
            if ($updateResult && $updateStmt->rowCount() > 0) {
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Admin status updated successfully']);
            } else {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Failed to update status. No changes made.']);
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Exception: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Invalid request: Missing required parameters']);
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

                    <!-- Error Modal -->
                    <div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                                style="width: 85%; max-width: 320px; margin: auto;">
                                <h5 class="fw-bold mb-2" id="errorModalLabel">Error</h5>
                                <p class="mb-4" id="errorModalMessage" style="font-size: 0.95rem;">
                                    An error occurred.
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
                    
                    <!-- Success Modal -->
                    <div id="successModal" class="modal fade" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                                style="width: 85%; max-width: 320px; margin: auto;">
                                <h5 class="fw-bold mb-2" id="successModalLabel">Success</h5>
                                <p class="mb-4" id="successModalMessage" style="font-size: 0.95rem;">
                                    Operation completed successfully.
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
        // Function to show error modal
        function showErrorModal(message) {
            document.getElementById('errorModalMessage').textContent = message;
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        }
        
        // Function to show success modal
        function showSuccessModal(message) {
            document.getElementById('successModalMessage').textContent = message;
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        }
        
        document.querySelectorAll(".admin-toggle").forEach(toggle => {
            toggle.addEventListener("change", function() {
                const memberId = this.getAttribute('data-member-id');
                const isAdmin = this.checked;
                const circleId = <?php echo $circleId; ?>;
                const toggleElement = this;
                
                // Show loading indicator
                this.disabled = true;
                console.log(`Attempting to change admin status for member ${memberId} to ${isAdmin ? 'admin' : 'member'}`);
                
                // Send AJAX request to update admin status
                fetch('changeAdminStatusPassenger.php?circleId=' + circleId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=toggleAdmin&memberId=${memberId}&isAdmin=${isAdmin}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server responded with status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response:', data);
                    if (!data.success) {
                        // Revert toggle if request failed
                        toggleElement.checked = !isAdmin;
                        showErrorModal(data.message || 'Failed to update admin status');
                    } else {
                        // Success feedback
                        const statusBadge = document.createElement('span');
                        statusBadge.className = 'badge bg-success ms-2 status-update';
                        statusBadge.textContent = 'Updated';
                        toggleElement.parentNode.appendChild(statusBadge);
                        
                        // Show success modal and redirect to circle details after closing
                        showSuccessModal('Admin status updated successfully');
                        
                        // Redirect to circle details page after a brief delay to show the success badge
                        setTimeout(() => {
                            window.location.href = 'circleDetails.php?circleId=' + circleId;
                        }, 1500);
                    }
                    toggleElement.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    toggleElement.checked = !isAdmin;
                    toggleElement.disabled = false;
                    showErrorModal('An error occurred while updating admin status: ' + error.message);
                });
            });
        });
    </script>
</body>

</html>