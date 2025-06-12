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
    <!-- Header for Profile Info -->
    <div class="container-fluid">
        <div class="row">
            <div class="col header d-flex flex-row p-2 rounded-5">
                <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Profile Information"
                    class="img-fluid m-3" />
                <div class="h3 p-3 m-1 px-1 fw-bolder">Profile Information</div>

            </div>
        </div>
    </div>


    <div class="container-fluid">
        <div class="d-flex justify-content-center py-3">
            <div class="card border-0">
                <div class="card-body p-4 text-center">
                    <!-- Driver Photo -->
                    <div class="row">
                        <div class="col mb-4 py-1">
                            <div class="rounded-circle d-flex justify-content-center align-items-center mx-auto"
                                style="width: 120px; height: 120px; background-color: #958D8D">
                                <span class="text-white fw-bold">Driver Photo</span>
                            </div>
                        </div>
                    </div>


                    <!-- Tricycle Details -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Tricycle Details</h6>
                            <p class="mb-1 text-dark">HONDA CIVIC - XHY - IWU</p>
                        </div>
                    </div>


                    <!-- Tricycle number -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Tricycle Number</h6>
                            <p class="mb-1 text-dark" id="tricycle-number">12345678910</p>
                        </div>
                    </div>


                    <!-- Permanent Address -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Permanent Address</h6>
                            <p class="mb-1 text-dark">Lorem Ipsum Dolor</p>
                        </div>
                    </div>


                    <!-- Toda Registration -->
                    <div class="row">
                        <div class="col mb-4 py-1">
                            <h6 class="-2 text-dark">TODA Registration</h6>
                            <p class="mb-1 text-dark">Lorem Ipsum Dolor</p>
                        </div>
                    </div>


                    <!-- QR Code -->
                    <div class="row">
                        <div class="col mb-3">
                            <img alt="QR Code" class="img-fluid" id="qr-code">
                        </div>
                    </div>


                    <!-- Generate QR -->
                    <div class="row">
                        <div class="col-auto">
                            <button class="btn custom-hover text-white fw-bold px-4 py-2 rounded-pill" type="submit"
                                onclick="" style="background-color: #2DAAA7;">Download QR Code</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar for Driver -->
    <?php include '../assets/shared/navbarDriver.php'; ?>


    <script>
        // Generate QR
        function generateQr() {
            const tricycleNumber = document.getElementById("tricycle-number").textContent;
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(tricycleNumber)}`;
            document.getElementById("qr-code").src = qrUrl;
            console.log("QR Code generated for tricycle number", tricycleNumber);
        }
        window.addEventListener('DOMContentLoaded', generateQr);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>