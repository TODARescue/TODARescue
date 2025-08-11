<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require_once '../assets/shared/connect.php';

if (!isset($_SESSION['userId'])) {
  header('Location: ../login.php');
  exit;
}

$userId = $_SESSION['userId'];
$error = '';
$success = '';
$photoFileName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $firstName = trim($_POST['firstName']);
  $lastName = trim($_POST['lastName']);
  $contactNumber = trim($_POST['contactNumber']);
  $email = trim($_POST['email']);
  $photo = $_FILES['photo'];

  // Check for duplicate email or contact across ALL users except self
  $checkStmt = $conn->prepare("SELECT userId FROM users WHERE (email = ? OR contactNumber = ?) AND userId != ?");
  $checkStmt->bind_param("ssi", $email, $contactNumber, $userId);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult->num_rows > 0) {
    $error = "Email or Contact Number already exists.";
  } else {
    // Determine upload directory based on role
    $roleStmt = $conn->prepare("SELECT role FROM users WHERE userId = ?");
    $roleStmt->bind_param("i", $userId);
    $roleStmt->execute();
    $roleResult = $roleStmt->get_result();
    $roleRow = $roleResult->fetch_assoc();
    $role = $roleRow['role'] ?? 'passenger'; // default to passenger

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

    // Proceed with update if no error
    if (!$error) {
      if (!empty($photoFileName)) {
        $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, contactNumber=?, email=?, photo=? WHERE userId=?");
        $stmt->bind_param("sssssi", $firstName, $lastName, $contactNumber, $email, $photoFileName, $userId);
      } else {
        $stmt = $conn->prepare("UPDATE users SET firstName=?, lastName=?, contactNumber=?, email=? WHERE userId=?");
        $stmt->bind_param("ssssi", $firstName, $lastName, $contactNumber, $email, $userId);
      }

      if ($stmt->execute()) {
        header("Location: accountView.php?updated=1");
        exit;
      } else {
        $error = "Update failed.";
      }
    }
  }
}

// Load user data for display
$stmt = $conn->prepare("SELECT firstName, lastName, contactNumber, email, photo, role FROM users WHERE userId = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Determine image path
$imageFolder = ($user['role'] === 'driver') ? 'driver' : 'passengers';
$photoPath = !empty($user['photo']) ? "../assets/images/$imageFolder/" . htmlspecialchars($user['photo']) : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .preview-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
    }
  </style>
</head>

<body class="bg-dark text-white d-flex justify-content-center align-items-center vh-100">
  <div class="container">
    <div class="card bg-white text-dark p-4 rounded-4 shadow-lg">
      <h3 class="text-center mb-4">Edit Account</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="text-center mb-3">
          <img id="preview"
            src="<?= !empty($user['photo']) ? '../assets/uploads/' . htmlspecialchars($user['photo']) : '' ?>"
            onerror="this.onerror=null; this.src='../assets/images/profile-default.png';"
            class="rounded-circle preview-img mb-2" alt="Profile Photo">

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

        <div class="d-grid">
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>

      <div class="d-grid mt-3">
        <a href="accountView.php" class="btn btn-secondary">Back to Profile</a>
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