<?php
session_start();

require_once '../assets/shared/connect.php';
include '../assets/php/checkLogin.php';

$userId = $_SESSION['userId'];
$error = '';
$photoFileName = '';
$plateNumber = '';
$todaRegistration = '';

// Load user info
$stmt = $conn->prepare("SELECT firstName, lastName, contactNumber, email, photo, role FROM users WHERE userId = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role = $user['role'] ?? 'driver';

// Load driver-specific info
if ($role === 'driver') {
    $driverStmt = $conn->prepare("SELECT plateNumber, todaRegistration FROM drivers WHERE userId = ?");
    $driverStmt->bind_param("i", $userId);
    $driverStmt->execute();
    $driverResult = $driverStmt->get_result();
    if ($driverData = $driverResult->fetch_assoc()) {
        $plateNumber = $driverData['plateNumber'];
        $todaRegistration = $driverData['todaRegistration'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $contactNumber = trim($_POST['contactNumber']);
    $email = trim($_POST['email']);
    $plateNumber = trim($_POST['plateNumber'] ?? '');
    $todaRegistration = trim($_POST['todaRegistration'] ?? '');
    $photo = $_FILES['photo'];

    // Check duplicate email/contact
    $checkStmt = $conn->prepare("SELECT userId FROM users WHERE (email = ? OR contactNumber = ?) AND userId != ?");
    $checkStmt->bind_param("ssi", $email, $contactNumber, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $error = "Email or Contact Number already exists.";
    } else {
        if (!empty($photo['name'])) {
            $photoFileName = time() . '_' . basename($photo['name']);
            $targetDir = ($role === 'driver') ? '../assets/images/driver/' : '../assets/images/passengers/';
            $targetPath = $targetDir . $photoFileName;

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (!move_uploaded_file($photo['tmp_name'], $targetPath)) {
                $error = "Failed to upload photo. Please check file permissions.";
            }
        }

        if (!$error) {
            if (!empty($photoFileName)) {
                $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, contactNumber=?, email=?, photo=? WHERE userId=?");
                $stmt->bind_param("sssssi", $firstName, $lastName, $contactNumber, $email, $photoFileName, $userId);
            } else {
                $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, contactNumber=?, email=? WHERE userId=?");
                $stmt->bind_param("ssssi", $firstName, $lastName, $contactNumber, $email, $userId);
            }

            if ($stmt->execute()) {
                if ($role === 'driver') {
                    $updateDriverStmt = $conn->prepare("UPDATE drivers SET plateNumber=?, todaRegistration=? WHERE userId=?");
                    $updateDriverStmt->bind_param("ssi", $plateNumber, $todaRegistration, $userId);
                    $updateDriverStmt->execute();
                }
                // ✅ FIX: Redirect back to ACCOUNT VIEW after saving
                header("Location: accountView.php?updated=1");
                exit;
            } else {
                $error = "Update failed.";
            }
        }
    }
}

// Set profile photo path
$imageFolder = ($role === 'driver') ? 'driver' : 'passengers';
$photoPath = !empty($user['photo']) ? "../assets/images/$imageFolder/" . htmlspecialchars($user['photo']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="text-white d-flex justify-content-center align-items-center p-2">
    <div class="container">
        <div class="card bg-white text-dark p-4 rounded-4 shadow-lg">
            <h3 class="text-center mb-4">Edit Account</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="text-center mb-3">
                    <img id="preview"
                        src="<?= $photoPath ?>"
                        onerror="this.onerror=null; this.src='../assets/images/profile-default.png';"
                        class="rounded-circle img-fluid mb-2"
                        style="width: 120px; height: 120px; object-fit: cover;"
                        alt="Profile Photo">
                    <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewImage(event)">
                </div>

                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstName" class="form-control" required
                        value="<?= htmlspecialchars($user['firstName']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastName" class="form-control" required
                        value="<?= htmlspecialchars($user['lastName']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="contactNumber" class="form-control" required
                        value="<?= htmlspecialchars($user['contactNumber']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" required
                        value="<?= htmlspecialchars($user['email']) ?>">
                </div>

                <?php if ($role === 'driver'): ?>
                    <div class="mb-3">
                        <label class="form-label">Plate Number</label>
                        <input type="text" name="plateNumber" class="form-control" value="<?= htmlspecialchars($plateNumber) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">TODA Registration</label>
                        <input type="text" name="todaRegistration" class="form-control" value="<?= htmlspecialchars($todaRegistration) ?>" required>
                    </div>
                <?php endif; ?>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" style="background-color: #24b3a7; border-radius: 15px;">Save Changes</button>
                </div>
            </form>

            <div class="d-grid mt-3">
                <a href="settings.php" class="btn text-black w-100"
                    style="background-color: #dcdcdc; border-radius: 15px;">Settings</a>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('preview').src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>
