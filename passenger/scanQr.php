<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webrtc-adapter/3.3.3/adapter.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.1.10/vue.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Rethink+Sans:ital,wght@0,400..800;1,400..800&display=swap" rel="stylesheet">
</head>

<body>
    <!-- HEADER -->
    <?php include '../assets/shared/header.php'; ?>

    <div class="container">
        <div class="row">
            <div class="col-sm-12 d-flex justify-content-center align-items-center">
                <video id="preview" width="100%" class="video-frame object-cover-fit z-1" style="width: 100vw; height: 100vh; background-color: #000;"></video>
            </div>
        </div>
    </div>


    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
        scanner.addListener('scan', function(content){
            console.log("Scanned Id: ", content);
            fetchData(content);
        })
        Instascan.Camera.getCameras().then(function(cameras) {
            if(cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                console.error("No cameras found!");
            }
        }).catch (function (e){
            console.error(e);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>