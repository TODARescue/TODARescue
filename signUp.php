<?php
include("assets/php/connect.php");

session_start();

$_SESSION['userId'] = "";
$_SESSION['firstName'] = "";
$_SESSION['lastName'] = "";
$_SESSION['email'] = "";
$_SESSION['contact'] = "";

$error = "";

if (isset($_POST['btnSignUp'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $signUpQuery = "SELECT * FROM users WHERE email = '$email'";
    $signUpResult = mysqli_query($conn, $signUpQuery);

    if (mysqli_num_rows($signUpResult) > 0) {
        $error = "EMAIL_EXISTS";
    } elseif ($password == $confirmPassword) {
        $userInsertQuery = "INSERT INTO users (firstName, lastName, email, contactNumber, password, role, isRiding) 
                            VALUES ('$firstName', '$lastName', '$email', '$contact', '$password', 'passenger', 0)";
        mysqli_query($conn, $userInsertQuery);

        $_SESSION['firstName'] = $firstName;
        $_SESSION['lastName'] = $lastName;
        $_SESSION['email'] = $email;
        $_SESSION['contact'] = $contact;

        header("Location: index.php");
        exit();
    } else {
        $error = "PASSWORD_MISMATCH";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <div class="card bg-white w-100 h-100 d-flex flex-column justify-content-center px-4"
                    style="max-width: 100%; border-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <div class="text-center mb-4" style="margin-top: -80px;">
                        <img src="assets/images/Logo.png" alt="TODA Rescue Logo" style="width: 100px;">
                        <h1 class="mt-1" style="font-weight: 800;">TODA Rescue</h1>
                    </div>

                    <div class="mx-auto" style="width: 100%; max-width: 350px;">
                        <h5 class="fw-bold mb-3">Sign Up</h5>

                        <?php if ($error == "EMAIL_EXISTS") { ?>
                            <div class="alert alert-warning text-center mb-3">Email already exists.</div>
                        <?php } elseif ($error == "PASSWORD_MISMATCH") { ?>
                            <div class="alert alert-danger text-center mb-3">Passwords do not match.</div>
                        <?php } ?>

                        <form method="POST" action="signUp.php">
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="First Name" name="firstName"
                                    required style="border-radius: 25px; background-color: #D9D9D9; border: none;">
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Last Name" name="lastName" required
                                    style="border-radius: 25px; background-color: #D9D9D9; border: none;">
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Email" name="email" required
                                    style="border-radius: 25px; background-color: #D9D9D9; border: none;">
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Contact Number" name="contact"
                                    required style="border-radius: 25px; background-color: #D9D9D9; border: none;">
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="password" id="password" class="form-control" placeholder="Password"
                                    name="password" required
                                    style="border-radius: 25px; background-color: #D9D9D9; border: none; padding-right: 40px;">
                                <button type="button" id="togglePassword"
                                    class="btn btn-sm position-absolute end-0 top-0 mt-1 me-3"
                                    style="border: none; background: transparent;">
                                    <i id="toggleIcon1" class="bi bi-eye-fill"></i>
                                </button>
                            </div>

                            <div class="mb-3 position-relative">
                                <input type="password" id="confirmPassword" class="form-control"
                                    placeholder="Confirm Password" name="confirmPassword" required
                                    style="border-radius: 25px; background-color: #D9D9D9; border: none; padding-right: 40px;">
                                <button type="button" id="toggleConfirmPassword"
                                    class="btn btn-sm position-absolute end-0 top-0 mt-1 me-3"
                                    style="border: none; background: transparent;">
                                    <i id="toggleIcon2" class="bi bi-eye-fill"></i>
                                </button>
                            </div>


                            <div class="d-flex justify-content-center my-3">
                                <button type="submit" name="btnSignUp"
                                    class="btn text-white fw-bold px-4 py-2 rounded-pill"
                                    style="background-color: #2EBCBC; border: none;">
                                    Sign Up
                                </button>
                            </div>
                        </form>
                        <p class="text-center mb-1">
                            Already have an account?
                            <a href="index.php" class="text-black text-decoration-underline fw-semibold">Login to
                                TODARescue App</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Toggle Password
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon1');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye-fill', isHidden);
            icon.classList.toggle('bi-eye-slash-fill', !isHidden);
        });

        // Toggle Confirm Password
        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const input = document.getElementById('confirmPassword');
            const icon = document.getElementById('toggleIcon2');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye-fill', isHidden);
            icon.classList.toggle('bi-eye-slash-fill', !isHidden);
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>