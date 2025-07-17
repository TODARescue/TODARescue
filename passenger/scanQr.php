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

<body>
    <!-- HEADER -->
    <?php include '../assets/shared/header.php'; ?>

    <!-- Camera -->
    <div class="position-fixed top-0 start-0 w-100 vh-100" style="z-index: 1;">
        <video id="preview" autoplay playsinline class="w-100 h-100 object-fit-cover"></video>
        <canvas id="qr-canvas" style="display: none;"></canvas>
    </div>
    <!-- NAVBAR -->
    <?php include '../assets/shared/navbarPassenger.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        const video = document.getElementById('preview');
        const canvas = document.getElementById('qr-canvas');
        const ctx = canvas.getContext('2d');
        let scanning = true;

        function startScanner() {
            // Use rear camera on mobile devices
            const constraints = {
                video: {
                    facingMode: 'environment',
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    }
                }
            };

            navigator.mediaDevices.getUserMedia(constraints)
                .then((stream) => {
                    video.srcObject = stream;
                    video.addEventListener('loadedmetadata', () => {
                        video.play();
                        requestAnimationFrame(tick);
                    });
                })
                .catch((err) => {
                    console.error("Camera access error:", err);
                    alert('Camera access denied. Please allow camera access and reload.');
                });
        }

        function tick() {
            if (!scanning) return;

            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                try {
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert",
                    });

                    if (code) {
                        console.log("QR Code detected:", code.data);
                        scanning = false;
                        processQRCode(code.data);
                    }
                } catch (error) {
                    console.error("Error processing frame:", error);
                    scanning = true;
                }
            }

            if (scanning) {
                requestAnimationFrame(tick);
            }
        }

        function processQRCode(qrData) {
            console.log("Processing QR data:", qrData);

            fetch('../passenger/getDriver.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'qrData=' + encodeURIComponent(qrData)
                })
                .then(response => {
                    console.log("Response status:", response.status);
                    return response.json();
                })
                .then(data => {
                    console.log("Response data:", data);

                    if (data.success) {
                        window.location.href = 'verificationScreen.php?driverId=' + data.driverId;
                    } else {
                        alert(data.message || 'Invalid QR code');
                        setTimeout(() => {
                            scanning = true;
                            requestAnimationFrame(tick);
                        }, 1500);
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    alert('Network or server error. Please try again.');
                    scanning = true;
                    requestAnimationFrame(tick);
                });
        }

        document.addEventListener('DOMContentLoaded', startScanner);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>