<?php
include '../assets/shared/connect.php';

if (isset($_GET['userId'])) {
    $userId = (int) $_GET['userId'];

    // Step 1: Delete related locations for the user's ride history
    $historyQuery = "SELECT historyId FROM history WHERE userId = ?";
    $stmt = $conn->prepare($historyQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $historyResult = $stmt->get_result();

    while ($historyRow = $historyResult->fetch_assoc()) {
        $historyId = $historyRow['historyId'];
        $stmtDel = $conn->prepare("DELETE FROM locations WHERE historyID = ?");
        $stmtDel->bind_param("i", $historyId);
        $stmtDel->execute();
    }

    // Step 2: Delete history entries
    $stmt = $conn->prepare("DELETE FROM history WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Step 3: Check if user is owner of any circles
    $stmt = $conn->prepare("SELECT circleId FROM circles WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $ownerCircles = $stmt->get_result();

    while ($circleRow = $ownerCircles->fetch_assoc()) {
        $circleId = $circleRow['circleId'];

        // Try to find earliest joined admin
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
            // No admin found, try to find earliest joined member
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
                $newOwnerId = null; // No eligible new owner
            }
        }

        // Assign new owner if found
        if ($newOwnerId !== null) {
            // Update circles table
            $stmtUpdateCircle = $conn->prepare("UPDATE circles SET userId = ? WHERE circleId = ?");
            $stmtUpdateCircle->bind_param("ii", $newOwnerId, $circleId);
            $stmtUpdateCircle->execute();

            // Update circlemembers role
            $stmtUpdateRole = $conn->prepare("UPDATE circlemembers SET role = 'owner' WHERE circleId = ? AND userId = ?");
            $stmtUpdateRole->bind_param("ii", $circleId, $newOwnerId);
            $stmtUpdateRole->execute();
        }
    }

    // Step 4: Delete only user's circlemembers entry
    $stmt = $conn->prepare("DELETE FROM circlemembers WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Step 5: Soft-delete user in users table
    $stmt = $conn->prepare("UPDATE users SET isDeleted = 1 WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Optional: remove from drivers table if exists
    $stmt = $conn->prepare("DELETE FROM drivers WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    header("Location: passengers.php");
    exit();
} else {
    header("Location: passengers.php");
    exit();
}
