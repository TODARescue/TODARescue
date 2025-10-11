<?php
include '../assets/shared/connect.php';

$showSuccessModal = false;
$showErrorModal = false;
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Check if required fields are empty
    if (
        empty($_POST['firstName']) || empty($_POST['lastName']) || empty($_POST['email']) ||
        empty($_POST['password']) || empty($_POST['contactNumber']) || empty($_POST['model']) ||
        empty($_POST['plateNumber']) || empty($_POST['address']) || empty($_POST['todaRegistration'])
    ) {
        $showErrorModal = true;
        $errorMessage = "All fields are required.";
    } elseif (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $showErrorModal = true;
        $errorMessage = "Driver photo is required.";
    } else {
        // ✅ Continue processing if no errors
        $photoName = $_FILES['photo']['name'];
        $photoTmp = $_FILES['photo']['tmp_name'];
        $uploadPath = '../assets/images/drivers/' . $photoName;
        move_uploaded_file($photoTmp, $uploadPath);

        // ✅ Fix: no (int) casting, all trimmed strings
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']); // as entered, no hash
        $contactNumber = trim($_POST['contactNumber']); // keep leading zero
        $model = trim($_POST['model']);
        $plateNumber = trim($_POST['plateNumber']);
        $address = trim($_POST['address']);
        $todaRegistration = trim($_POST['todaRegistration']);
        $isVerified = ($_POST['verification'] === 'verified') ? 1 : 0;

        // ========================
        // Insert into users
        // ========================
        $insertUser = "INSERT INTO users (firstName, lastName, email, role, contactNumber, password, photo, isRiding)
                       VALUES (?, ?, ?, 'driver', ?, ?, ?, 0)";
        $stmtUser = $conn->prepare($insertUser);

        if ($stmtUser) {
            // ✅ All parameters as strings (fixed from ssssis → ssssss)
            $stmtUser->bind_param(
                "ssssss",
                $firstName,
                $lastName,
                $email,
                $contactNumber,
                $password,
                $photoName
            );

            if ($stmtUser->execute()) {
                $userId = $stmtUser->insert_id;

                // ========================
                // Insert into drivers
                // ========================
                $insertDriver = "INSERT INTO drivers (plateNumber, model, address, todaRegistration, isVerified, photo, userId)
                                 VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtDriver = $conn->prepare($insertDriver);

                if ($stmtDriver) {
                    $stmtDriver->bind_param(
                        "ssssisi",
                        $plateNumber,
                        $model,
                        $address,
                        $todaRegistration,
                        $isVerified,
                        $photoName,
                        $userId
                    );

                    if ($stmtDriver->execute()) {
                        header("Location: driverView.php?userId=" . $userId);
                        exit();
                    } else {
                        $showErrorModal = true;
                        $errorMessage = "Driver insert failed: " . $stmtDriver->error;
                    }
                } else {
                    $showErrorModal = true;
                    $errorMessage = "Driver prepare failed: " . $conn->error;
                }
            } else {
                $showErrorModal = true;
                $errorMessage = "User insert failed: " . $stmtUser->error;
            }
        } else {
            $showErrorModal = true;
            $errorMessage = "User prepare failed: " . $conn->error;
        }
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
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/logo.png" type="image/png">
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
                    <img src="../assets/images/arrow-back-admin.svg" alt="Back" style="width: 15px; height: 15px;"
                        onclick="history.back();">
                </a>
                <h5 class="fw-semibold m-0">Add Drivers</h5>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data" id="driverForm">
            <div class="row justify-content-center mb-4">
                <div class="col-auto text-center">
                    <label for="profile-upload" style="cursor: pointer;">
                        <img id="profile-preview" src="../assets/images/logo.png" alt="Profile" class="rounded-circle"
                            style="width:100px; height:100px; object-fit: cover;">
                        <div class="small mt-2">Click to upload photo</div>
                    </label>
                    <input type="file" id="profile-upload" name="photo" accept="image/*" class="d-none"
                        onchange="previewPhoto(event)">
                </div>
            </div>

            <div class="row">
                <div class="col px-4">
                    <input type="text" name="firstName" class="form-control border-0 border-bottom mb-3"
                        placeholder="First Name" required>
                    <input type="text" name="lastName" class="form-control border-0 border-bottom mb-3"
                        placeholder="Last Name" required>

                    <!-- ✅ Changed to type='tel' to preserve leading zero -->
                    <input type="tel" name="contactNumber" class="form-control border-0 border-bottom mb-3"
                        placeholder="Contact Number" pattern="[0-9]{11}" maxlength="11" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '');">

                    <input type="email" name="email" class="form-control border-0 border-bottom mb-4"
                        placeholder="Email" required>
                    <input type="text" name="password" class="form-control border-0 border-bottom mb-4"
                        placeholder="Password" required>
                    <input type="text" name="model" class="form-control border-0 border-bottom mb-4"
                        placeholder="Tricycle Model" required>
                    <input type="text" name="plateNumber" class="form-control border-0 border-bottom mb-4"
                        placeholder="Plate Number" required>
                    <input type="text" name="address" class="form-control border-0 border-bottom mb-4"
                        placeholder="Permanent Address" required>
                    <input type="text" name="todaRegistration" class="form-control border-0 border-bottom mb-4"
                        placeholder="Toda Registration" required>

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
                    <button type="submit" class="btn w-50 rounded-pill border-0"
                        style="background-color: #2DAAA7; color: white;">
                        Save
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 text-center">
                <h5 class="modal-title text-success">✅ Driver Registered</h5>
                <div class="modal-body">
                    The driver has been successfully saved.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 text-center">
                <h5 class="modal-title text-danger">⚠️ Registration Error</h5>
                <div class="modal-body">
                    <?= isset($errorMessage) ? htmlspecialchars($errorMessage) : "An error occurred."; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Validation Modal -->
    <div class="modal fade" id="validationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">

                <h5 class="fw-bold mb-2 text-danger">Missing Photo</h5>
                <p class="mb-4" style="font-size: 0.95rem;">Please upload a driver photo before saving the form.</p>

                <div class="d-flex justify-content-center">
                    <button type="button" class="btn rounded-pill px-4 text-white"
                        style="background-color: #1cc8c8; font-weight: 600;" data-bs-dismiss="modal">
                        Okay
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if ($showSuccessModal): ?>
        <script>
            var myModal = new bootstrap.Modal(document.getElementById('successModal'));
            myModal.show();
        </script>
    <?php endif; ?>

    <?php if ($showErrorModal): ?>
        <script>
            var myModal = new bootstrap.Modal(document.getElementById('errorModal'));
            myModal.show();
        </script>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function validateForm(e) {
            const fileInput = document.getElementById('profile-upload');
            if (!fileInput.value) {
                e.preventDefault();
                const modal = new bootstrap.Modal(document.getElementById('validationModal'));
                modal.show();
            }
        }

        document.getElementById("driverForm").addEventListener("submit", validateForm);

        function previewPhoto(event) {
            const output = document.getElementById('profile-preview');
            output.style.display = "block";
            output.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

</body>

</html>