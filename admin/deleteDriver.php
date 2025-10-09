<?php
include '../assets/shared/connect.php';
include '../assets/php/checkLogin.php';


if (isset($_GET['userId'])) {
    $userId = (int) $_GET['userId'];

    // Step 1: Transfer ownership of circles the user owns
    $stmt = $conn->prepare("SELECT circleId FROM circles WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $ownerCircles = $stmt->get_result();

    while ($circleRow = $ownerCircles->fetch_assoc()) {
        $circleId = $circleRow['circleId'];

        // Try to find a new owner (admin first)
        $stmtAdmin = $conn->prepare("
            SELECT userId FROM circlemembers 
            WHERE circleId = ? AND role = 'admin'
            ORDER BY joinedAt ASC LIMIT 1
        ");
        $stmtAdmin->bind_param("i", $circleId);
        $stmtAdmin->execute();
        $adminResult = $stmtAdmin->get_result();

        if ($adminRow = $adminResult->fetch_assoc()) {
            $newOwnerId = $adminRow['userId'];
        } else {
            // If no admin, try member
            $stmtMember = $conn->prepare("
                SELECT userId FROM circlemembers 
                WHERE circleId = ? AND role = 'member'
                ORDER BY joinedAt ASC LIMIT 1
            ");
            $stmtMember->bind_param("i", $circleId);
            $stmtMember->execute();
            $memberResult = $stmtMember->get_result();

            if ($memberRow = $memberResult->fetch_assoc()) {
                $newOwnerId = $memberRow['userId'];
            } else {
                $newOwnerId = null;
            }
        }

        if ($newOwnerId !== null) {
            // Transfer ownership to another user
            $stmtUpdateCircle = $conn->prepare("UPDATE circles SET userId = ? WHERE circleId = ?");
            $stmtUpdateCircle->bind_param("ii", $newOwnerId, $circleId);
            $stmtUpdateCircle->execute();

            // Promote the new owner in circlemembers
            $stmtUpdateRole = $conn->prepare("UPDATE circlemembers SET role = 'owner' WHERE circleId = ? AND userId = ?");
            $stmtUpdateRole->bind_param("ii", $circleId, $newOwnerId);
            $stmtUpdateRole->execute();
        } else {
            // No other members: delete the circle and its members
            $stmtDeleteMembers = $conn->prepare("DELETE FROM circlemembers WHERE circleId = ?");
            $stmtDeleteMembers->bind_param("i", $circleId);
            $stmtDeleteMembers->execute();

            $stmtDeleteCircle = $conn->prepare("DELETE FROM circles WHERE circleId = ?");
            $stmtDeleteCircle->bind_param("i", $circleId);
            $stmtDeleteCircle->execute();
        }
    }

    // Step 2: Remove user from all circlemembers entries
    $stmt = $conn->prepare("DELETE FROM circlemembers WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Step 3: Soft-delete the user (preserve other data)
    $stmt = $conn->prepare("UPDATE users SET isDeleted = 1 WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    header("Location: drivers.php");
    exit();
} else {
    header("Location: drivers.php");
    exit();
}
?>
