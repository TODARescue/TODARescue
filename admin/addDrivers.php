<?php
include '../assets/shared/connect.php';

$showSuccessModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoName = $_FILES['photo']['name'];
        $photoTmp = $_FILES['photo']['tmp_name'];
        $uploadPath = '../assets/images/drivers/' . $photoName;
        move_uploaded_file($photoTmp, $uploadPath);

        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $contactNumber = $_POST['contactNumber'];
        $model = $_POST['model'];
        $plateNumber = $_POST['plateNumber'];
        $address = $_POST['address'];
        $todaRegistration = $_POST['todaRegistration'];
        $isVerified = ($_POST['verification'] === 'verified') ? 1 : 0;

        $insertUser = "INSERT INTO users (firstName, lastName, email, role, contactNumber, password, photo, isRiding)
                       VALUES (?, ?, ?, 'driver', ?, ?, ?, 0)";
        $stmtUser = $conn->prepare($insertUser);
        $stmtUser->bind_param("ssssss", $firstName, $lastName, $email, $contactNumber, $password, $photoName);
        $stmtUser->execute();
        $userId = $stmtUser->insert_id;

        $insertDriver = "INSERT INTO drivers (plateNumber, model, address, todaRegistration, isVerified, photo, userId)
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtDriver = $conn->prepare($insertDriver);
        $stmtDriver->bind_param("ssssisi", $plateNumber, $model, $address, $todaRegistration, $isVerified, $photoName, $userId);
        $stmtDriver->execute();

        $showSuccessModal = true;
    } else {
        echo "<script>alert('Photo is required.'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TODA Rescue - Add Drivers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container min-vh-100 d-flex flex-column justify-content-start pt-4 position-relative z-1">

        <div class="row mb-3">
            <div class="col text-center">
                <h3 class="fw-bolder m-0">TODA Rescue</h3>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col d-flex align-items-center ps-4">
                <a href="drivers.php" class="me-2 text-decoration-none">
                    <img src="../assets/images/arrow-back-admin.svg" alt="Back" style="width: 15px; height: 15px;" onclick="history.back();">
                </a>
                <h5 class="fw-semibold m-0">Add Drivers</h5>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="row justify-content-center mb-4">
                <div class="col-auto text-center">
                    <label for="profile-upload" style="cursor: pointer;">
                        <img id="profile-preview" src="../assets/images/logo.png" alt="Profile" class="rounded-circle"
                             style="width:100px; height:100px; object-fit: cover;">
                        <div class="small mt-2">Click to upload photo</div>
                    </label>
                    <input type="file" id="profile-upload" name="photo" accept="image/*" class="d-none" required onchange="previewPhoto(event)">
                </div>
            </div>

            <div class="row">
                <div class="col px-4">
                    <input type="text" name="firstName" class="form-control border-0 border-bottom mb-3" placeholder="First Name" required>
                    <input type="text" name="lastName" class="form-control border-0 border-bottom mb-3" placeholder="Last Name" required>
                    <input type="text" name="contactNumber" class="form-control border-0 border-bottom mb-3" placeholder="Contact Number" required>
                    <input type="email" name="email" class="form-control border-0 border-bottom mb-4" placeholder="Email" required>
                    <input type="text" name="password" class="form-control border-0 border-bottom mb-4" placeholder="Password" required>
                    <input type="text" name="model" class="form-control border-0 border-bottom mb-4" placeholder="Tricycle Model" required>
                    <input type="text" name="plateNumber" class="form-control border-0 border-bottom mb-4" placeholder="Plate Number" required>
                    <input type="text" name="address" class="form-control border-0 border-bottom mb-4" placeholder="Permanent Address" required>
                    <input type="text" name="todaRegistration" class="form-control border-0 border-bottom mb-4" placeholder="Toda Registration" required>
                    <div class="mb-4">
                        <input type="radio" id="unverified" name="verification" value="unverified" required>
                        <label for="unverified">Unverified</label>
                        <input type="radio" id="verified" name="verification" value="verified" class="ms-3">
                        <label for="verified">Verified</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col d-flex justify-content-center mt-3 mb-5">
                    <button type="submit" class="btn w-50 rounded-pill border-0" style="background-color: #2DAAA7; color: white;">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div id="successModal" class="modal fade" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <h5 class="fw-bold mb-2" id="successModalLabel">Success</h5>
                <p class="mb-4" style="font-size: 0.95rem;">
                    Driver has been added successfully.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn rounded-pill px-4 text-white"
                        style="background-color: #1cc8c8; font-weight: 600;"
                        onclick="window.location.href='drivers.php'">
                        Okay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewPhoto(event) {
            const input = event.target;
            const preview = document.getElementById('profile-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        <?php if ($showSuccessModal): ?>
            window.addEventListener('DOMContentLoaded', function () {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            });
        <?php endif; ?>
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
