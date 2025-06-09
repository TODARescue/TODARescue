<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <style>
    body {
        font-family: 'Inter', san serif;
    }

    .header {
        box-shadow:
            0 -1px 6px 3px rgba(0, 0, 0, 0.1),
            0 0 18px 6px rgba(0, 0, 0, 0.15);
    }
    </style>
</head>

<body>
    <!-- Header for Profile Info -->
    <div class="container-fluid">
        <div class="row">
            <div class="col header d-flex flex-row p-2 rounded-5">
                <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Profile Information" class="img-fluid m-3" />
                <div class="h3 p-3 m-1 px-1 fw-bolder">Profile Information</div>

            </div>
        </div>
    </div>
    <!-- Navbar for Driver -->
    <?php include '../assets/shared/navbarAD.php'; ?>

    <div class="container d-flex justify-content-center py-4">
        <div class="card border-0">
            <div class="card-body p-4 text-center">
                <!-- Driver Photo -->
                <div class="mb-4 py-1">
                    <div class="rounded-circle bg-secondary d-flex justify-content-center align-items-center mx-auto" style="width: 120px; height: 120px;">
                        <span class="text-white fw-bold">Driver Photo</span>
                    </div>
                </div>

                <!-- Tricycle Details -->
                <div class="mb-3 py-1">
                    <h6 class="fw-bold mb-2 text-dark">Tricycle Details</h6>
                    <p class="mb-1 text-dark fw-semibold">HONDA CIVIC - XHY - IWU</p>
                </div>
                
                <!-- Tricycle number -->
                <div class="mb-3 py-1">
                    <h6 class="fw-bold mb-2 text-dark">Tricycle Number</h6>
                    <p class="mb-1 text-dark fw-semibold" id="tricycle-number">12345678910</p>
                </div>

                <!-- Permanent Address -->
                <div class="mb-3 py-1">
                    <h6 class="fw-bold mb-2 text-dark">Permanent Address</h6>
                    <p class="mb-1 text-dark fw-semibold">Lorem Ipsum Dolor</p>
                </div>

                <!-- Toda Registration -->
                <div class="mb-4 py-1">
                    <h6 class="fw-bold mb-2 text-dark">TODA Registration</h6>
                    <p class="mb-1 text-dark fw-semibold">Lorem Ipsum Dolor</p>
                </div>

                <!-- QR Code -->
                <div class="mb-3">
                    <img alt="QR Code" class="img-fluid" id="qr-code">
                </div>

                <!-- Generate QR -->
                <button class="btn btn-secondary rounded-5 " type="button" onclick="generateQr()">Generate QR Code</button>
            </div>
        </div>
    </div>


    <script>
        // Generate QR
        function generateQr(){
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