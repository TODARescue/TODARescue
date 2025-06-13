<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>TODA Rescue - Remove Circle Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter&family=Rethink+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex justify-content-center align-items-center vh-100"
    style="background-color: #2c2c2c; font-family: 'Inter', sans-serif; margin: 0;">

    <div class="container-fluid p-0 m-0 h-100">
        <div class="row h-100 g-0">
            <div class="col-12 d-flex justify-content-center align-items-start h-100">
                <div class="card bg-white w-100 h-100 d-flex flex-column p-0"
                    style="border-bottom-left-radius: 25px; border-bottom-right-radius: 25px; box-shadow: 0 0 30px rgba(0, 0, 0, 0.4);">

                    <!-- Header -->
                    <div class="container-fluid position-fixed top-0 start-0 end-0 bg-white shadow rounded-bottom-5"
                        style="z-index: 1030;">
                        <div class="row">
                            <div class="col d-flex align-items-center p-3">
                                <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Back" style="height: 40px;" />
                                <h3 class="fw-bold m-0 ms-2">Change Admin Status</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Member List -->
                    <div class="container-fluid mt-5 pt-5">
                        <div class="row">
                            <div class="col list-group list-group-flush px-0 w-100">
                                <div class="mb-1">
                                    <h4 class="fs-5 mt-5 px-4">Admin Status</h4>
                                </div>

                                <div class="container-fluid p-0">
                                    <div class="list-group">
                                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary">
                                            <span class="fw-medium">John Doe</span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="johnDoeToggle">
                                                <label for="johnDoeToggle" class="form-check-label"></label>
                                            </div>
                                        </div>

                                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary">
                                            <span class="fw-medium">Elon Musk</span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="elonMuskToggle" checked>
                                                <label for="elonMuskToggle" class="form-check-label"></label>
                                            </div>
                                        </div>

                                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-4 text-black bg-light w-100 border-0 border-bottom border-secondary">
                                            <span class="fw-medium">Bato Dela Rosa</span>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="batoToggle">
                                                <label for="batoToggle" class="form-check-label"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../assets/shared/navbarDriver.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll(".form-check-input").forEach(toggle => {
            toggle.addEventListener("change", function() {
                console.log(`${this.id} is now ${this.checked ? "enabled" : "disabled"}  `);
            });
        });
    </script>
</body>

</html>