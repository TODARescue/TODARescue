<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c2c2c;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }

        .login-card {
            border-radius: 25px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);
        }

        .form-control {
            border-radius: 25px;
            background-color: #e2e2e2;
            border: none;
        }

        .login-btn {
            border-radius: 25px;
            background-color: #1bcfd4;
            color: white;
            font-weight: bold;
        }

        .login-btn:hover {
            background-color: #18b5b9;
        }

        @media (max-width: 576px) {
            .login-card {
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-center">
                <div class="card p-4 login-card bg-white w-100 h-100 d-flex flex-column justify-content-center px-4"
                    style="max-width: 100%;">

                    <div class="text-center mb-5" style="margin-top: -200px;">
                        <img src="assets/images/Logo.png" alt="TODA Rescue Logo" style="width: 80px;">
                        <h4 class="fw-bold mt-2">TODA Rescue</h4>
                    </div>

                    <div class="mx-auto" style="width: 100%; max-width: 350px;">
                        <h5 class="fw-bold mb-3 text-center">Login</h5>

                        <form>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Contact Number" required>
                            </div>
                            <div class="mb-3">
                                <input type="password" class="form-control" placeholder="Password" required>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label small" for="rememberMe">Remember me</label>
                            </div>

                            <div class="d-flex justify-content-center" style="margin-top: 30px;">
                                <button type="submit" class="btn login-btn" style="width: 150px;">Login</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>