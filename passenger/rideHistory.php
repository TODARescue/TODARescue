<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TODA Rescue | Ride History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/style.css" />
</head>

<body style="font-family: 'Inter', sans-serif;">
    <div class="container-fluid position-fixed top-0 start-0 end-0 bg-white shadow rounded-bottom-5" style="z-index: 1030;">
        <div class="row">
            <div class="col d-flex align-items-center p-3">
                    <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Back" style="height: 40px;" />
                <h3 class="fw-bold m-0">Ride History</h3>
            </div>
        </div>
    </div>

    <div class="container d-flex justify-content-center align-items-center mt-5 pt-5">
        <div class="card text-center p-4 m-5 rounded-5" style="background-color: #D9D9D9; max-width: 300px;">
            <div class="fw-bold fs-5 mb-0">Ride History Expiration</div>
            <div class="text-muted">You can only see ride history within the span of 7 days.</div>
        </div>
    </div>
        <div class="container mb-5">
            <div class="row">
                <div class="col list-group list-group-flush px-0 w-100 ">
                    <div class="list-group-item list-group-item-action py-3 px-4 text-black border-bottom border-secondary w-100 bg-light"
                        data-bs-toggle="collapse" data-bs-target="#rideDetails">
                        April 06, 2025 20:12:36 : 20:16:29
                    </div>

                    <!-- Collapsible Ride Details -->
                    <div id="rideDetails" class="collapse">
                        <div class="container py-3">
                            <div class="row justify-content-center">
                                <div class="col-auto">
                                    <div class="h4 fs-5 fw-bolder text-center">Rode With</div>
                                </div>
                            </div>

                            <div class="container d-flex justify-content-center align-items-center mt-1">
                                <div class="card text-center d-flex justify-content-center align-items-center p-4 m-3 rounded-5 w-100" style="background-color: #D9D9D9; max-width: 600px;">
                                    <img src="../assets/images/profile-default.png" alt="Profile" class="img-fluid mb-3" style="max-width: 100px;" />

                                    <div class="d-flex justify-content-center align-items-center mb-2">
                                        <h5 class="mb-0 me-2">Juan Dela Cruz</h5>
                                        <img src="../assets/images/verified.png" alt="Verified" style="width: 16px;">
                                    </div>

                                    <div class="row pt-3 w-100">
                                        <div class="col text-start">
                                            <p class="mb-1"><strong>Tricycle Details:</strong></p>
                                            <p>HONDA CIVIC - XHY-IWU</p>
                                            <p class="mb-1"><strong>Tricycle Number:</strong></p>
                                            <p>1232423534634</p>
                                            <p class="mb-1"><strong>Permanent Address:</strong></p>
                                            <p>address ito</p>
                                            <p class="mb-1"><strong>Toda Registration:</strong></p>
                                            <p>toda123344</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="list-group-item list-group-item-action py-3 px-4 text-black border-bottom border-secondary w-100 bg-light"
                        onclick="toggleRodeWith()">
                        April 06, 2025 20:12:36 : 20:16:29
                    </div>
                    <div class="list-group-item list-group-item-action py-3 px-4 text-black border-bottom border-secondary w-100 bg-light"
                        onclick="toggleRodeWith()">
                        April 06, 2025 20:12:36 : 20:16:29
                    </div>
                    <div class="list-group-item list-group-item-action py-3 px-4 text-black border-bottom border-secondary w-100 bg-light"
                        onclick="toggleRodeWith()">
                        April 06, 2025 20:12:36 : 20:16:29
                    </div>

                </div>
            </div>
        </div>
        

    <?php include '../assets/shared/navbarPassenger.php'; ?>
    <script>
        function toggleRodeWith() {
            const card = document.getElementById('rodeWithCard');
            card.classList.toggle('d-none');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
