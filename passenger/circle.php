<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Circle Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 vh-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">

                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-top-left-radius: 0; border-top-right-radius: 0; border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <!-- Header -->
                    <div class="d-flex align-items-center justify-content-start shadow px-4"
                        style="border-bottom-left-radius: 43px; border-bottom-right-radius: 43px; background-color: #fff; height: 100px;">
                        <a href="#" class="me-2 fs-5 fw-bold text-decoration-none text-dark">&#8592;</a>
                        <h5 class="mb-0 fw-bold">Circle Management</h5>
                    </div>

                    <!-- Circle List Section -->
                    <div class="p-4">
                        <h6 class="fw-bold mb-4">Circle List</h6>

                        <!-- Group 1 -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex justify-content-center align-items-center me-3"
                                style="width: 48px; height: 48px; background-color: #1cc8c8;">
                                <i class="bi bi-people-fill text-white fs-5"></i>
                            </div>
                            <span class="fw-medium">Group 1 Name</span>
                        </div>

                        <!-- Group 2 -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle d-flex justify-content-center align-items-center me-3"
                                style="width: 48px; height: 48px; background-color: #1cc8c8;">
                                <i class="bi bi-people-fill text-white fs-5"></i>
                            </div>
                            <span class="fw-medium">Group 2 Name</span>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-sm rounded-pill px-4" style="background-color: #dcdcdc;">Create
                                Circle</button>
                            <button class="btn btn-sm rounded-pill px-4" style="background-color: #dcdcdc;">Join
                                Circle</button>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarDriver.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>