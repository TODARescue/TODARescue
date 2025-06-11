<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Leave Circle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
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
                        <h5 class="mb-0 fw-bold">Group 1 Name</h5>
                    </div>

                    <!-- Content (blurred background + modal simulated) -->
                    <div class="position-relative d-flex justify-content-center align-items-center flex-grow-1"
                        style="background-color: rgba(255, 255, 255, 0.4);">

                        <!-- Modal -->
                        <div class="bg-white p-4 rounded-4 shadow text-center"
                            style="width: 85%; max-width: 320px;">
                            <h5 class="fw-bold mb-2">Leaving Circle</h5>
                            <p class="mb-4" style="font-size: 0.95rem;">
                                You will no longer see or share locations with this Circle. Are you sure you want to leave?
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <button class="btn rounded-pill px-4"
                                    style="background-color: #dcdcdc; font-weight: 600;">No</button>
                                <button class="btn rounded-pill px-4 text-white"
                                    style="background-color: #1cc8c8; font-weight: 600;">Yes</button>
                            </div>
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
