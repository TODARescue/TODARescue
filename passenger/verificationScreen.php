<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include '../assets/shared/header.php'; ?>
    
    <div class="container-fluid vh-100   overflow-auto">
        <div class="d-flex justify-content-center py-3">
            <div class="card border-1 rounded-5 w-75" style="background-color: #D9D9D9">
                <div class="card-body p-4 ">
                    <!-- Driver Photo -->
                    <div class="row">
                        <div class="text-center mb-4">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 120px; height: 120px; background-color: #958D8D;">
                                <img id="driverPhoto" src="" alt="Driver Photo" class="rounded-circle object-fit-cover" 
                                     style="width: 100%; height: 100%; display: none;">
                                <span class="text-white" id="photoPlaceholder">Driver Photo</span>
                            </div>
                        </div>
                    </div>


                    <!-- Driver Name -->
                    <div class="row">
                        <div class="text-center mb-4">
                            <h6 class="fw-bold" id="driverName">Juan De La Cruz <span class="badge bg-success rounded-circle" id="verificationBadge" >✓</span></h6>
                        </div>
                    </div>


                    <!-- Tricycle Details -->
                    <div class="row">
                        <div class="mb-4">
                            <div class="mb-3">
                                <h6 class="fw-bold mb-2 text-dark">Tricycle Details:</h6>
                                <p class="mb-0 text-muted" id="tricycleDetails">HONDA CIVIC - XHY-IWU</p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold mb-2 text-dark">Tricycle Number:</h6>
                                <p class="mb-0 text-muted" id="tricycleNumber">12345678910</p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold mb-2 text-dark">Permanent Address:</h6>
                                <p class="mb-0 text-muted" id="permanentAddress">Lorem Ipsum Dolor</p>
                            </div>

                            <div class="mb-3">
                                <h6 class="fw-bold mb-2 text-dark">TODA Registration:</h6>
                                <p class="mb-0 text-muted" id="todaRegistration">Lorem Ipsum Dolor</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Start Ride -->
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-auto py-5">
                <button class="btn btn-primary btn-lg rounded-pill custom-hover text-black fw-semibold">Start Ride</button>
            </div>
        </div>
    </div>
    
    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>