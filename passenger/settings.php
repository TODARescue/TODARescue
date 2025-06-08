<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">

                <!-- Removed px-3, replaced with px-0 to eliminate horizontal padding -->
                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-top-left-radius: 0; border-top-right-radius: 0; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-start shadow px-4"
                        style="border-bottom-left-radius: 43px; border-bottom-right-radius: 43px; background-color: #fff; height: 100px;">
                        <a href="#" class="me-2 fs-5 fw-bold text-decoration-none text-dark">&#8592;</a>
                        <h5 class="mb-0 fw-bold">Settings</h5>
                    </div>
                    <!-- Settings List -->
                    <div class="list-group list-group-flush mt-2 px-0 w-100">

                        <!-- Subheader -->
                        <div class="px-3 pt-3 pb-1 text-secondary fw-bold text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Circle Settings
                        </div>

                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light"
                            onclick="handleClick('circleManagement')">
                            Circle Management
                        </div>

                        <div
                            class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom border-secondary w-100 bg-light">
                            <span>Location Sharing</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" checked>
                            </div>
                        </div>

                        <!-- Universal Settings -->
                        <div class="px-3 pt-3 pb-1 text-secondary fw-bold text-uppercase"
                            style="font-size: 0.85rem; user-select: none;">
                            Universal Settings
                        </div>

                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light"
                            onclick="handleClick('privacySecurity')">
                            Account
                        </div>

                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light"
                            onclick="handleClick('privacySecurity')">
                            Privacy and Security
                        </div>

                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light"
                            onclick="handleClick('about')">
                            About
                        </div>

                        <div class="list-group-item list-group-item-action py-3 text-black border-bottom border-secondary w-100 bg-light"
                            onclick="handleClick('logout')">
                            Logout
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarPassenger.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>