<?php
session_start();
require_once '../shared/connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST['userId'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];

    if (strlen($newPassword) < 8) {
        header("Location: ../../admin/settings.php?error=New password must be at least 8 characters.");
        exit();
    }

    $query = "SELECT password FROM users WHERE userID = $userId";
    $result = executeQuery($query);

    if (!$result || mysqli_num_rows($result) === 0) {
        header("Location: ../../admin/settings.php?error=User not found.");
        exit();
    }

    $user = mysqli_fetch_assoc($result);

    if ($oldPassword !== $user['password']) {
        header("Location: ../../admin/settings.php?error=Old password is incorrect.");
        exit();
    }

    $updateQuery = "UPDATE users SET password = '$newPassword' WHERE userID = $userId";
    if (executeQuery($updateQuery)) {
        header("Location: ../../admin/settings.php?success=Password updated successfully!");
    } else {
        header("Location: ../../admin/settings.php?error=Failed to update password.");
    }
}
?>
