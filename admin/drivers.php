<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TODARescue | Passengers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rethink+Sans:wght@600;800&display=swap"
        rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>

<body class="bg-white d-flex justify-content-center align-items-start min-vh-100 pt-5">

    <div class="container px-4" style="max-width: 400px;">
        <h3 class="fw-bold text-center mb-3">TODA Rescue</h3>
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-3">Drivers</h5>
            <button onclick="location.href='addDrivers.php'"
                class="btn rounded-pill d-flex align-items-center justify-content-center mb-3"
                style="background-color: #2EBCBC; border: none; width: 60px; height: 30px; padding: 0;">
                <i class="bi bi-plus" style="font-size: 20px; color: white;"></i>
            </button>

        </div>


        <?php include 'searchBar.php'; ?>

        <div class="mt-4 d-flex flex-column gap-3">

            <div class="card border-0 clickable-card"
                style="background-color: #D9D9D9; border-radius: 30px; cursor: pointer;"
                onclick="goToPassengerView(event)">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 40px; height: 40px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                        <span class="text-dark">Driver 1</span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="editProfileDriver.php" class="btn btn-info btn-sm rounded-circle text-white"
                            onclick="event.stopPropagation();">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="#" class="btn btn-danger btn-sm rounded-circle text-white"
                            onclick="event.stopPropagation();">
                            <i class="bi bi-trash-fill"></i>
                        </a>
                    </div>
                </div>
            </div>

           

        </div>

    </div>

    <?php include '../assets/shared/navbarAdmin.php'; ?>




    <script>
        function goToPassengerView(event) {
            window.location.href = "driverView.php";
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>