<?php
include '../assets/shared/connect.php';

if (isset($_GET['userId'])) {
    $userId = (int) $_GET['userId'];

    // Step 1: Get driver's driverId
    $driverQuery = "SELECT driverId FROM drivers WHERE userId = ?";
    $stmt = $conn->prepare($driverQuery);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $driverId = $row['driverId'];

        // Step 2: Delete associated locations
        $historyQuery = "SELECT historyId FROM history WHERE driverId = ?";
        $stmt = $conn->prepare($historyQuery);
        $stmt->bind_param("i", $driverId);
        $stmt->execute();
        $historyResult = $stmt->get_result();

        while ($historyRow = $historyResult->fetch_assoc()) {
            $historyId = $historyRow['historyId'];

            $stmtDelete = $conn->prepare("DELETE FROM locations WHERE historyID = ?");
            $stmtDelete->bind_param("i", $historyId);
            $stmtDelete->execute();
        }

        // Step 3: Delete history entries
        $stmt = $conn->prepare("DELETE FROM history WHERE driverId = ?");
        $stmt->bind_param("i", $driverId);
        $stmt->execute();

        // Step 4: Delete driver entry
        $stmt = $conn->prepare("DELETE FROM drivers WHERE driverId = ?");
        $stmt->bind_param("i", $driverId);
        $stmt->execute();
    }

    // Step 5: Handle circle ownership transfer if necessary
    $stmt = $conn->prepare("SELECT circleId FROM circles WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $ownerCircles = $stmt->get_result();

    while ($circleRow = $ownerCircles->fetch_assoc()) {
        $circleId = $circleRow['circleId'];

        // Find earliest admin in the circle
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
            // Update circles table
            $stmtUpdateCircle = $conn->prepare("UPDATE circles SET userId = ? WHERE circleId = ?");
            $stmtUpdateCircle->bind_param("ii", $newOwnerId, $circleId);
            $stmtUpdateCircle->execute();

            // Promote to owner in circlemembers
            $stmtUpdateRole = $conn->prepare("UPDATE circlemembers SET role = 'owner' WHERE circleId = ? AND userId = ?");
            $stmtUpdateRole->bind_param("ii", $circleId, $newOwnerId);
            $stmtUpdateRole->execute();
        }
    }

    // Step 6: Delete circlemembers record of the driver
    $stmt = $conn->prepare("DELETE FROM circlemembers WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Step 7: Soft-delete user
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
