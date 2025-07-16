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
$query = "SELECT c.circleId, cm.role, (SELECT COUNT(*) FROM circlemembers WHERE circleId = c.circleId) as memberCount 
          FROM circles c 
          INNER JOIN circlemembers cm ON c.circleId = cm.circleId 
          WHERE cm.userId = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$userCircle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userCircle) {
    // User is not in any circle, redirect to circle page
    header('Location: circle.php');
    exit;
}

$circleId = $userCircle['circleId'];
$userRole = $userCircle['role'];
$memberCount = $userCircle['memberCount'];

// Start transaction
$pdo->beginTransaction();

try {
    // If user is owner and there are other members
    if ($userRole === 'owner' && $memberCount > 1) {
        // Find another member to promote to owner (prioritize admins)
        $findNewOwnerQuery = "SELECT userId FROM circlemembers 
                             WHERE circleId = ? AND userId != ? AND role = 'admin' 
                             ORDER BY RAND() LIMIT 1";
        $newOwnerStmt = $pdo->prepare($findNewOwnerQuery);
        $newOwnerStmt->execute([$circleId, $userId]);
        $newOwnerId = $newOwnerStmt->fetchColumn();
        
        // If no admin found, get any member
        if (!$newOwnerId) {
            $findAnyMemberQuery = "SELECT userId FROM circlemembers 
                                  WHERE circleId = ? AND userId != ? 
                                  ORDER BY RAND() LIMIT 1";
            $anyMemberStmt = $pdo->prepare($findAnyMemberQuery);
            $anyMemberStmt->execute([$circleId, $userId]);
            $newOwnerId = $anyMemberStmt->fetchColumn();
        }
        
        // Promote the selected member to owner
        $promoteQuery = "UPDATE circlemembers SET role = 'owner' WHERE circleId = ? AND userId = ?";
        $promoteStmt = $pdo->prepare($promoteQuery);
        $promoteStmt->execute([$circleId, $newOwnerId]);
    }
    
    // If user is the last member, delete the circle
    if ($memberCount <= 1) {
        $deleteCircleQuery = "DELETE FROM circles WHERE circleId = ?";
        $deleteCircleStmt = $pdo->prepare($deleteCircleQuery);
        $deleteCircleStmt->execute([$circleId]);
    }
    
    // Remove the user from the circle
    $removeUserQuery = "DELETE FROM circlemembers WHERE userId = ? AND circleId = ?";
    $removeUserStmt = $pdo->prepare($removeUserQuery);
    $removeUserStmt->execute([$userId, $circleId]);
    
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
    
    // Set error message
    $_SESSION['circleErrorMsg'] = 'An error occurred while trying to leave the circle. Please try again.';
    
    // Redirect back to circle details
    header('Location: circleDetails.php');
    exit;
}
?>