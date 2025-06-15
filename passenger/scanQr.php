<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
</head>

<body class="overflow-hidden">
    <div class="container-fluid position-fixed top-0 start-0 end-0 bg-white shadow rounded-bottom-5" style="z-index: 1030;">
        <div class="row">
            <div class="col d-flex align-items-center p-3">
                <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Back" style="height: 40px;" />
                <h3 class="fw-bold m-0 ms-2">TODA Rescue</h3>
            </div>
        </div>
    </div>

    <!-- Camera -->
    <div class="position-fixed top-0 start-0 w-100 vh-100" style="z-index: 1;">
        <video id="preview" autoplay playsinline class="w-100 h-100 object-fit-cover bg-dark"></video>
    </div>


    <script>
        const video = document.getElementById('preview');

        // Start camera
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then((stream) => {
                video.srcObject = stream;
            })
            .catch((err) => {
                console.error("No cameras found!", err);
            });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>