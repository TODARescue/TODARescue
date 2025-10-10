<?php
session_start();
require_once '../assets/shared/connect.php';
include '../assets/php/checkLogin.php';

$error = '';
$photoFileName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $firstName = trim($_POST['firstName']);
  $lastName = trim($_POST['lastName']);
  $contactNumber = trim($_POST['contactNumber']);
  $email = trim($_POST['email']);
  $photo = $_FILES['photo'];

  // Check for duplicate email/contact
  $checkStmt = $conn->prepare("SELECT userId FROM users WHERE (email = ? OR contactNumber = ?) AND userId != ?");
  $checkStmt->bind_param("ssi", $email, $contactNumber, $userId);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult->num_rows > 0) {
    $error = "Email or Contact Number already exists.";
  } else {
    // Always use passengers folder for this page
    $targetDir = '../assets/images/passengers/';

    if (!empty($photo['name'])) {
      $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
      if (in_array($photo['type'], $allowedTypes)) {
        $photoFileName = time() . '_' . basename($photo['name']);
        $targetPath = $targetDir . $photoFileName;

        if (!is_dir($targetDir)) {
          mkdir($targetDir, 0755, true);
        }

        if (!move_uploaded_file($photo['tmp_name'], $targetPath)) {
          $error = "Failed to upload photo. Please check file permissions.";
        }
      } else {
        $error = "Invalid file type. Please upload JPG, PNG, or GIF.";
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
        // ✅ Redirect to Settings page after saving
        header("Location: settings.php?updated=1&t=" . time());
        exit;
      } else {
        $error = "Update failed.";
      }
    }
  }
}

// Fetch current user data
$stmt = $conn->prepare("SELECT firstName, lastName, contactNumber, email, photo FROM users WHERE userId = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Profile image path with cache buster
$photoPath = !empty($user['photo'])
  ? "../assets/images/passengers/" . htmlspecialchars($user['photo']) . '?t=' . time()
  : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .preview-img {
      width: 100px;
      height: 100px;
      object-fit: cover;
    }
  </style>
</head>

<body class="text-white d-flex justify-content-center align-items-center">
  <div class="container">
    <div class="card bg-white text-dark p-4 rounded-4 shadow-lg">
      <h3 class="text-center mb-4">Edit Account</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="text-center mb-3">
          <img id="preview" src="<?= $photoPath ?>"
            onerror="this.onerror=null; this.src='../assets/images/profile-default.png';"
            class="rounded-circle preview-img mb-2" alt="Profile Photo">

          <input type="file" name="photo" class="form-control mt-2" accept="image/*" onchange="previewImage(event)">
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
          <button type="submit" class="btn btn-primary" style="background-color: #24b3a7; border-radius: 15px;">Save Changes</button>
        </div>
      </form>

      <div class="d-grid mt-3">
        <!-- ✅ FIX: Back button now goes to Settings -->
        <a href="settings.php" class="btn text-black w-100"
          style="background-color: #dcdcdc; border-radius: 15px;"> Settings</a>
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