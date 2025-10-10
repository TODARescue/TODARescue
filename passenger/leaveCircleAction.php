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

// Get circleId from URL parameter
$circleId = isset($_GET['circleId']) ? intval($_GET['circleId']) : null;

// If no circleId is provided, redirect to circle.php
if (!$circleId) {
    header('Location: circle.php');
    exit;
}

// Check if user is a member of this specific circle
$query = "SELECT cm.role, 
                 (SELECT COUNT(*) FROM circlemembers WHERE circleId = ?) AS memberCount 
          FROM circlemembers cm 
          WHERE cm.userId = ? AND cm.circleId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $circleId, $userId, $circleId);
$stmt->execute();
$result = $stmt->get_result();
$userCircle = $result->fetch_assoc();

if (!$userCircle) {
    header('Location: circle.php');
    exit;
}

$userRole = $userCircle['role'];
$memberCount = $userCircle['memberCount'];

// Start transaction
$conn->begin_transaction();

try {
    // Remove the user from the circle
    $removeUserQuery = "DELETE FROM circlemembers WHERE userId = ? AND circleId = ?";
    $removeUserStmt = $conn->prepare($removeUserQuery);
    $removeUserStmt->bind_param("ii", $userId, $circleId);
    $removeUserStmt->execute();

    // If this was the last member, delete the circle
    if ($memberCount <= 1) {
        $deleteCircleQuery = "DELETE FROM circles WHERE circleId = ?";
        $deleteCircleStmt = $conn->prepare($deleteCircleQuery);
        $deleteCircleStmt->bind_param("i", $circleId);
        $deleteCircleStmt->execute();
    } 
    // If user was owner and there are other members, transfer ownership
    else if ($userRole === 'owner') {
        // Find another admin to promote to owner
        $findNewOwnerQuery = "SELECT userId FROM circlemembers 
                              WHERE circleId = ? AND role = 'admin' 
                              ORDER BY RAND() LIMIT 1";
        $newOwnerStmt = $conn->prepare($findNewOwnerQuery);
        $newOwnerStmt->bind_param("i", $circleId);
        $newOwnerStmt->execute();
        $result = $newOwnerStmt->get_result();
        $newOwner = $result->fetch_assoc();
        $newOwnerId = $newOwner['userId'] ?? null;

        // If no admin found, get any member
        if (!$newOwnerId) {
            $findAnyMemberQuery = "SELECT userId FROM circlemembers 
                                   WHERE circleId = ? 
                                   ORDER BY RAND() LIMIT 1";
            $anyMemberStmt = $conn->prepare($findAnyMemberQuery);
            $anyMemberStmt->bind_param("i", $circleId);
            $anyMemberStmt->execute();
            $result = $anyMemberStmt->get_result();
            $anyMember = $result->fetch_assoc();
            $newOwnerId = $anyMember['userId'] ?? null;
        }

        // Promote new owner
        if ($newOwnerId) {
            $promoteQuery = "UPDATE circlemembers SET role = 'owner' WHERE circleId = ? AND userId = ?";
            $promoteStmt = $conn->prepare($promoteQuery);
            $promoteStmt->bind_param("ii", $circleId, $newOwnerId);
            $promoteStmt->execute();
        }
    }

    // Commit transaction
    $conn->commit();

    $_SESSION['circleSuccessMsg'] = 'You have successfully left the circle.';
    header('Location: circle.php');
    exit;

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error leaving circle: " . $e->getMessage());
    $_SESSION['circleErrorMsg'] = 'An error occurred while trying to leave the circle. Please try again.';
    header('Location: circleDetails.php?circleId=' . $circleId);
    exit;
}
?>
