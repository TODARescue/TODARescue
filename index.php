<?php
include("assets/php/connect.php");
session_start();
session_destroy();
session_start();

$error = "";

$storedContact = isset($_COOKIE['contactNumber']) ? $_COOKIE['contactNumber'] : "";
$storedPassword = isset($_COOKIE['password']) ? $_COOKIE['password'] : "";
$status = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contactNumber = $_POST['contactNumber'];
    $password = $_POST['password'];

    $contactNumber = mysqli_real_escape_string($conn, $contactNumber);
    $password = mysqli_real_escape_string($conn, $password);

    $query = "SELECT * FROM users WHERE contactNumber = '$contactNumber'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if ($user['password'] === $password) {
            $_SESSION['userId'] = $user['userId'];
            $_SESSION['role'] = $user['role'];

            if (isset($_POST['rememberMe'])) {
                setcookie('contactNumber', $contactNumber, time() + (30 * 24 * 60 * 60), "/");
                setcookie('password', $password, time() + (30 * 24 * 60 * 60), "/");
            }

            $updateStatusQuery = "UPDATE users SET isRiding = 2 WHERE userId = {$user['userId']}";
            executeQuery($updateStatusQuery);
            // Redirect based on role
            if ($user['role'] === 'passenger') {
                header("Location: passenger/index.php");
            } elseif ($user['role'] === 'driver') {
                header("Location: driver/index.php");
            } elseif ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                $error = "Unknown role assigned.";
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <div class="bg-white w-100 h-100 d-flex flex-column justify-content-center px-4"
                    style="max-width: 100%; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <div class="text-center mb-5" style="margin-top: -200px;">
                        <img src="assets/images/Logo.png" alt="TODA Rescue Logo" style="width: 80px;">
                        <h4 class="fw-bold mt-2">TODA Rescue</h4>
                    </div>

                    <div class="mx-auto" style="width: 100%; max-width: 350px;">
                        <h5 class="fw-bold mb-3 text-center">Login</h5>
                        <form method="POST" action="">
                            <?php if (!empty($error)) { ?>
                                <div class="alert alert-danger text-center py-1 small"><?php echo $error; ?></div>
                            <?php } ?>

                            <div class="mb-3">
                                <input type="text" name="contactNumber" class="form-control"
                                    placeholder="Contact Number" required
                                    value="<?php echo htmlspecialchars($storedContact); ?>"
                                    style="border-radius: 25px; background-color: #D9D9D9; border: none;">
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password"
                                    required value="<?php echo htmlspecialchars($storedPassword); ?>"
                                    style="border-radius: 25px; background-color: #D9D9D9; border: none;">
                                <button type="button" id="togglePassword"
                                    class="btn btn-sm position-absolute end-0 top-0 mt-1 me-3"
                                    style="border: none; background: transparent;">
                                    <i id="toggleIcon1" class="bi bi-eye-fill"></i>
                                </button>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="rememberMe" <?php if (!empty($storedContact))
                                                                                                                        echo "checked"; ?>>
                                <label class="form-check-label small" for="rememberMe">Remember me</label>
                            </div>

                            <div class="d-flex justify-content-center my-4">
                                <button type="submit"
                                    class="btn custom-hover text-white fw-bold px-4 py-2 rounded-pill">
                                    Login
                                </button>
                            </div>
                        </form>

                    </div>
                    <p class="text-center mb-1">
                        Don't have an account?
                        <a href="signUp.php" class="text-black text-decoration-underline fw-semibold">Sign Up to
                            TODARescue App</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password
        document.getElementById('togglePassword').addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon1');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye-fill', isHidden);
            icon.classList.toggle('bi-eye-slash-fill', !isHidden);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>