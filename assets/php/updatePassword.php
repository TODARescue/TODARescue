<?php
session_start();
require_once '../shared/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST['userId'] ?? '';
    $oldPassword = trim($_POST['oldPassword'] ?? '');
    $newPassword = trim($_POST['newPassword'] ?? '');

    // Validate basic input
    if (!$userId || !$oldPassword || !$newPassword) {
        header("Location: ../../admin/settings.php?error=Please fill in all fields.");
        exit();
    }

    // Fetch current password from database
    $query = "SELECT password FROM users WHERE userID = $userId LIMIT 1";
    $result = executeQuery($query);
    if (!$result || mysqli_num_rows($result) === 0) {
        header("Location: ../../admin/settings.php?error=User not found.");
        exit();
    }

    $user = mysqli_fetch_assoc($result);
    $currentPassword = $user['password'];

    // Check if old password matches
    if ($oldPassword !== $currentPassword) {
        header("Location: ../../admin/settings.php?error=Old password is incorrect.");
        exit();
    }

    // Check new password length
    if (strlen($newPassword) < 8) {
        header("Location: ../../admin/settings.php?error=New password must be at least 8 characters.");
        exit();
    }

    // Check if new password is the same as old one
    if ($newPassword === $oldPassword) {
        header("Location: ../../admin/settings.php?error=New password cannot be the same as the old password.");
        exit();
    }

    // Update password in the database
    $updateQuery = "UPDATE users SET password = '$newPassword' WHERE userID = $userId";
    if (executeQuery($updateQuery)) {
        header("Location: ../../admin/settings.php?success=Password updated successfully!");
        exit();
    } else {
        header("Location: ../../admin/settings.php?error=Failed to update password. Try again.");
        exit();
    }
}
?>