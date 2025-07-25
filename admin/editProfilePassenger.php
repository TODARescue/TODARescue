<?php
include '../assets/shared/connect.php';

$userId = $_GET['userId'] ?? null;
if (!$userId) {
    echo "User ID missing.";
    exit;
}

$userResult = mysqli_query($conn, "SELECT * FROM users WHERE userId = $userId AND role = 'passenger'");
$user = mysqli_fetch_assoc($userResult);

if (!$user) {
    echo "Passenger not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $contactNumber = $_POST['contactNumber'];
    $email = $_POST['email'];

    $photoFilename = $user['photo'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoFilename = basename($_FILES['photo']['name']);
        $destination = '../assets/images/passengers/' . $photoFilename;
        move_uploaded_file($_FILES['photo']['tmp_name'], $destination);
    }

    $updateUser = "UPDATE users SET firstName=?, lastName=?, contactNumber=?, email=?, photo=? WHERE userId=?";
    $stmt = $conn->prepare($updateUser);
    $stmt->bind_param("sssssi", $firstName, $lastName, $contactNumber, $email, $photoFilename, $userId);
    $stmt->execute();

    header("Location: passengersView.php?userId=" . $userId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>TODA Rescue - Edit Passenger</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container min-vh-100 d-flex flex-column justify-content-start pt-4 position-relative z-1">

        <div class="row mb-3">
            <div class="col text-center">
                <h5 class="fw-bold m-0">TODA Rescue</h5>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col d-flex align-items-center ps-4">
                <a href="passengersView.php?userId=<?php echo urlencode($userId); ?>" class="me-2 text-decoration-none">
                    <img src="../assets/images/arrow-back-admin.svg" alt="Back" style="width: 15px; height: 15px;">
                </a>
                <h5 class="fw-semibold m-0">Edit Passenger</h5>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data" class="px-4">


            <div class="text-center mb-4">
                <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none">
                <label for="photoInput" style="cursor: pointer;">
                    <img src="../assets/images/passengers/<?php echo htmlspecialchars($user['photo']); ?>"
                        alt="Profile Photo" class="rounded-circle" style="width:100px; height:100px; object-fit: cover;"
                        id="profilePreview" title="Click to change photo">
                </label>
            </div>


            <input type="text" class="form-control border-0 border-bottom mb-3" name="firstName"
                placeholder="First Name" value="<?php echo htmlspecialchars($user['firstName']); ?>">

            <input type="text" class="form-control border-0 border-bottom mb-3" name="lastName" placeholder="Last Name"
                value="<?php echo htmlspecialchars($user['lastName']); ?>">

            <input type="text" class="form-control border-0 border-bottom mb-3" name="contactNumber"
                placeholder="Contact Number" value="<?php echo htmlspecialchars($user['contactNumber']); ?>">

            <input type="email" class="form-control border-0 border-bottom mb-4" name="email" placeholder="Email"
                value="<?php echo htmlspecialchars($user['email']); ?>">

            <div class="d-flex justify-content-center mt-3 mb-5">
                <button type="submit" class="btn w-50 rounded-pill border-0"
                    style="background-color: #2DAAA7; color: white;">
                    Save
                </button>
            </div>

        </form>
    </div>

    <script>
        const photoInput = document.getElementById('photoInput');
        const previewImg = document.getElementById('profilePreview');

        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                previewImg.src = URL.createObjectURL(file);
            }
        });
    </script>

</body>

</html>