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

// If no circleId is provided, redirect to circle.php
if (!$circleId) {
    header('Location: circle.php');
    exit;
}

// Check if user is a member of this specific circle
$query = "SELECT cm.role, (SELECT COUNT(*) FROM circlemembers WHERE circleId = ?) as memberCount 
          FROM circlemembers cm 
          WHERE cm.userId = ? AND cm.circleId = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$circleId, $userId, $circleId]);
$userCircle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userCircle) {
    // User is not in this circle, redirect to circle page
    header('Location: circle.php');
    exit;
}

$userRole = $userCircle['role'];
$memberCount = $userCircle['memberCount'];

// Start transaction
$pdo->beginTransaction();

try {
    // First remove the user from the circle
    $removeUserQuery = "DELETE FROM circlemembers WHERE userId = ? AND circleId = ?";
    $removeUserStmt = $pdo->prepare($removeUserQuery);
    $removeUserStmt->execute([$userId, $circleId]);
    
    // If this was the last member, delete the circle
    if ($memberCount <= 1) {
        $deleteCircleQuery = "DELETE FROM circles WHERE circleId = ?";
        $deleteCircleStmt = $pdo->prepare($deleteCircleQuery);
        $deleteCircleStmt->execute([$circleId]);
    } 
    // If user was owner and there are other members, transfer ownership
    else if ($userRole === 'owner') {
        // Find another member to promote to owner (prioritize admins)
        $findNewOwnerQuery = "SELECT userId FROM circlemembers 
                             WHERE circleId = ? AND role = 'admin' 
                             ORDER BY RAND() LIMIT 1";
        $newOwnerStmt = $pdo->prepare($findNewOwnerQuery);
        $newOwnerStmt->execute([$circleId]);
        $newOwnerId = $newOwnerStmt->fetchColumn();
        
        // If no admin found, get any member
        if (!$newOwnerId) {
            $findAnyMemberQuery = "SELECT userId FROM circlemembers 
                                  WHERE circleId = ? 
                                  ORDER BY RAND() LIMIT 1";
            $anyMemberStmt = $pdo->prepare($findAnyMemberQuery);
            $anyMemberStmt->execute([$circleId]);
            $newOwnerId = $anyMemberStmt->fetchColumn();
        }
        
        // Promote the selected member to owner if found
        if ($newOwnerId) {
            $promoteQuery = "UPDATE circlemembers SET role = 'owner' WHERE circleId = ? AND userId = ?";
            $promoteStmt = $pdo->prepare($promoteQuery);
            $promoteStmt->execute([$circleId, $newOwnerId]);
        }
    }
    
    // Commit the transaction
    $pdo->commit();
    
    // Set success message
    $_SESSION['circleSuccessMsg'] = 'You have successfully left the circle.';
    
    // Redirect to circle page
    header('Location: circle.php');
    exit;
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    
    // Log the error
    error_log("Error leaving circle: " . $e->getMessage());
    
    // Set error message
    $_SESSION['circleErrorMsg'] = 'An error occurred while trying to leave the circle. Please try again.';
    
    // Redirect back to circle details
    header('Location: circleDetails.php?circleId=' . $circleId);
    exit;
}
?>