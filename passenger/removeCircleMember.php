<?php
session_start();
require_once '../assets/shared/connect.php';

// For testing purposes - set a default user ID
if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['userId'];
$errorMsg = '';
$successMsg = '';

// Get circleId from URL parameter
$circleId = isset($_GET['circleId']) ? $_GET['circleId'] : null;

// If no circleId is provided, redirect to circle.php
if (!$circleId) {
    header('Location: circle.php');
    exit;
}

// Check if user is a member of this circle and get their role
$query = "SELECT cm.role FROM circlemembers cm
          WHERE cm.userId = ? AND cm.circleId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $circleId);
$stmt->execute();
$result = $stmt->get_result();
$roleResult = $result->fetch_assoc();


if (!$roleResult) {
    header('Location: circle.php');
    exit;
}

$userRole = $roleResult['role'];

// Only admins and owners can remove members
if ($userRole !== 'admin' && $userRole !== 'owner') {
    header('Location: circleDetails.php?circleId=' . $circleId);
    exit;
}

// Handle member removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'removeMember') {
    if (isset($_POST['memberId'])) {
        $memberId = intval($_POST['memberId']);

        error_log("Remove member request: memberId=$memberId, circleId=$circleId, userRole=$userRole");

        if ($memberId === $userId) {
            echo json_encode(['success' => false, 'message' => 'You cannot remove yourself from the circle. Use the Leave Circle option instead.']);
            exit;
        }

        // Check if member exists
        $checkMemberQuery = "SELECT COUNT(*) FROM circlemembers WHERE userId = ? AND circleId = ?";
        $checkMemberStmt = $conn->prepare($checkMemberQuery);
        $checkMemberStmt->bind_param("ii", $memberId, $circleId);
        $checkMemberStmt->execute();
        $result = $checkMemberStmt->get_result();
        $memberExists = $result->fetch_row()[0];

        if ($memberExists == 0) {
            echo json_encode(['success' => false, 'message' => 'This user is not a member of the circle.']);
            exit;
        }

        // Get member role
        $checkRoleQuery = "SELECT role FROM circlemembers WHERE userId = ? AND circleId = ?";
        $checkRoleStmt = $conn->prepare($checkRoleQuery);
        $checkRoleStmt->bind_param("ii", $memberId, $circleId);
        $checkRoleStmt->execute();
        $roleResult = $checkRoleStmt->get_result()->fetch_assoc();
        $memberRole = $roleResult['role'];

        error_log("Member role: $memberRole");

        if ($memberRole === 'owner') {
            echo json_encode(['success' => false, 'message' => 'You cannot remove the owner of the circle.']);
            exit;
        }

        if ($userRole === 'admin' && $memberRole === 'admin') {
            echo json_encode(['success' => false, 'message' => 'You need to be the circle owner to remove other admins.']);
            exit;
        }

        try {
            $conn->begin_transaction();

            $removeQuery = "DELETE FROM circlemembers WHERE userId = ? AND circleId = ?";
            $removeStmt = $conn->prepare($removeQuery);
            $removeStmt->bind_param("ii", $memberId, $circleId);
            $removeStmt->execute();

            error_log("Remove query executed. Affected rows: " . $removeStmt->affected_rows);

            if ($removeStmt->affected_rows > 0) {
                $conn->commit();
                echo json_encode(['success' => true, 'message' => 'Member removed successfully']);
            } else {
                $conn->rollback();
                echo json_encode(['success' => false, 'message' => 'Failed to remove member.']);
            }
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Exception: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid request: Missing member ID']);
    exit;
}


// Get all members of the circle except the current user
$membersQuery = "SELECT cm.userId, u.firstName, u.lastName, cm.role 
                 FROM circlemembers cm 
                 INNER JOIN users u ON cm.userId = u.userId 
                 WHERE cm.circleId = ? AND cm.userId != ?";
$membersStmt = $conn->prepare($membersQuery);
$membersStmt->bind_param("ii", $circleId, $userId);
$membersStmt->execute();
$result = $membersStmt->get_result();
$members = [];
while ($row = $result->fetch_assoc()) {
    $members[] = $row;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Passenger | Remove Circle Member</title>
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
                                    <button type="button" id="successBtn" class="btn rounded-pill px-4 text-white"
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
                                                    <?php if ($canRemove): ?>onclick="openRemoveModal('<?php echo htmlspecialchars($fullName); ?>', <?php echo $member['userId']; ?>)" <?php endif; ?>>
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
                const confirmBtn = this;

                const circleId = <?php echo $circleId; ?>;
                console.log(`Attempting to remove member ${currentMemberId} from circle ${circleId}`);

                // Send AJAX request to remove member
                fetch('removeCircleMember.php?circleId=' + circleId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=removeMember&memberId=${currentMemberId}`
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Server responded with status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response:', data);
                        closeModal();

                        if (data.success) {
                            // Show success modal and redirect to circle details page
                            showSuccessModal('Member removed successfully');

                            // Redirect to circle details page
                            setTimeout(() => {
                                window.location.href = 'circleDetails.php?circleId=' + circleId;
                            }, 1500);
                        } else {
                            showErrorModal(data.message || 'Failed to remove member');
                            confirmBtn.disabled = false;
                            confirmBtn.innerText = 'Yes';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        closeModal();
                        showErrorModal('An error occurred while removing the member: ' + error.message);
                        confirmBtn.disabled = false;
                        confirmBtn.innerText = 'Yes';
                    });
            }
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