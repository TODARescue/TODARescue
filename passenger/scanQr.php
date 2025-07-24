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

    <!-- Camera with scanning frame -->
    <div class="position-fixed top-0 start-0 w-100 vh-100" style="z-index: 1;">
        <video id="preview" autoplay playsinline class="w-100 h-100 object-fit-cover"></video>
        <canvas id="qr-canvas" style="display: none;"></canvas>
        
        <!-- QR scanning frame -->
        <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 2; pointer-events: none;">
            <div class="d-flex align-items-center justify-content-center">
                <div style="width: 250px; height: 250px; border: 3px solid transparent; border-radius: 20px; position: relative;">
                    <!-- Corner markers -->
                    <div style="position: absolute; top: -3px; left: -3px; width: 30px; height: 30px; border-top: 6px solid white; border-left: 6px solid white; border-top-left-radius: 10px;"></div>
                    <div style="position: absolute; top: -3px; right: -3px; width: 30px; height: 30px; border-top: 6px solid white; border-right: 6px solid white; border-top-right-radius: 10px;"></div>
                    <div style="position: absolute; bottom: -3px; left: -3px; width: 30px; height: 30px; border-bottom: 6px solid white; border-left: 6px solid white; border-bottom-left-radius: 10px;"></div>
                    <div style="position: absolute; bottom: -3px; right: -3px; width: 30px; height: 30px; border-bottom: 6px solid white; border-right: 6px solid white; border-bottom-right-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Error Modal -->
    <div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                style="width: 85%; max-width: 320px; margin: auto;">
                <h5 class="fw-bold mb-2" id="errorModalLabel">Error</h5>
                <p class="mb-4" id="errorModalMessage" style="font-size: 0.95rem;">
                    An error occurred.
                </p>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn rounded-pill px-4 text-white"
                        style="background-color: #1cc8c8; font-weight: 600;" data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Non-Verified Driver Confirmation Modal -->
    <div id="nonVerifiedDriverModal" class="modal fade" tabindex="-1" aria-labelledby="nonVerifiedDriverModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white p-4 rounded-5 shadow text-center border-0"
                style="width: 95%; max-width: 360px; margin: auto;">
                <h5 class="fw-bold mb-2 text-danger" id="nonVerifiedDriverModalLabel">Warning: Non-Verified Driver</h5>
                
                <!-- Driver Profile Info -->
                <div class="driver-profile mb-3 border rounded p-3">
                    <div class="row">
                        <div class="col-4">
                            <img id="driverPhoto" src="../assets/img/profile.jpg" alt="Driver Photo" 
                                 class="img-fluid rounded-circle border" style="width: 80px; height: 80px; object-fit: cover;">
                        </div>
                        <div class="col-8 text-start">
                            <h6 id="driverName" class="fw-bold mb-1">Unknown Driver</h6>
                            <p id="driverPlate" class="mb-1 small"><strong>Plate:</strong> <span>-</span></p>
                            <p id="driverModel" class="mb-1 small"><strong>Vehicle:</strong> <span>-</span></p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-start">
                            <p id="driverAddress" class="mb-1 small"><strong>Address:</strong> <span>-</span></p>
                            <p id="driverTodaReg" class="mb-1 small"><strong>TODA Registration:</strong> <span>-</span></p>
                        </div>
                    </div>
                </div>
                
                <p class="mb-3 text-danger" style="font-size: 0.95rem;">
                    This driver has not been verified. Are you sure you want to ride with this driver?
                </p>
                
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" id="cancelRideBtn" class="btn rounded-pill px-4"
                        style="background-color: #dcdcdc; font-weight: 600;" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" id="confirmRideBtn" class="btn rounded-pill px-4 text-white"
                        style="background-color: #dc3545; font-weight: 600;">
                        Yes, Continue
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- NAVBAR -->
    <?php include '../assets/shared/navbarPassenger.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        const video = document.getElementById('preview');
        const canvas = document.getElementById('qr-canvas');
        const ctx = canvas.getContext('2d');
        let scanning = true;

        // Function to show error modal
        function showErrorModal(message) {
            document.getElementById('errorModalMessage').textContent = message;
            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        }
        
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
                    showErrorModal('Camera access denied. Please allow camera access and reload.');
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
                        // Check if driver is verified
                        if (data.isVerified === false) {
                            // Show confirmation modal for non-verified driver with all driver data
                            showNonVerifiedDriverModal(data.driverId, data);
                        } else {
                            // Driver is verified, proceed to verification screen
                            window.location.href = 'verificationScreen.php?driverId=' + data.driverId;
                        }
                    } else {
                        showErrorModal(data.message || 'Invalid QR code');
                        
                        // Resume scanning after modal is closed
                        document.getElementById('errorModal').addEventListener('hidden.bs.modal', function () {
                            scanning = true;
                            requestAnimationFrame(tick);
                        }, { once: true });
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    showErrorModal('Network or server error. Please try again.');
                    
                    // Resume scanning after modal is closed
                    document.getElementById('errorModal').addEventListener('hidden.bs.modal', function () {
                        scanning = true;
                        requestAnimationFrame(tick);
                    }, { once: true });
                });
        }
        
        // Function to show non-verified driver confirmation modal
        function showNonVerifiedDriverModal(driverId, driverData) {
            const modal = document.getElementById('nonVerifiedDriverModal');
            
            // Set driver details
            document.getElementById('driverName').textContent = driverData.driverName || 'Unknown Driver';
            
            // Set driver plate number
            const plateElement = document.getElementById('driverPlate').querySelector('span');
            plateElement.textContent = driverData.plateNumber || '-';
            
            // Set vehicle model
            const modelElement = document.getElementById('driverModel').querySelector('span');
            modelElement.textContent = driverData.model || '-';
            
            // Set address
            const addressElement = document.getElementById('driverAddress').querySelector('span');
            addressElement.textContent = driverData.address || '-';
            
            // Set TODA registration
            const todaElement = document.getElementById('driverTodaReg').querySelector('span');
            todaElement.textContent = driverData.todaRegistration || '-';
            
            // Set photo if available
            if (driverData.photo) {
                document.getElementById('driverPhoto').src = '../assets/img/drivers/' + driverData.photo;
            } else {
                document.getElementById('driverPhoto').src = '../assets/img/profile.jpg';
            }
            
            // Handle confirm button click
            document.getElementById('confirmRideBtn').onclick = function() {
                window.location.href = 'verificationScreen.php?driverId=' + driverId;
            };
            
            // Resume scanning when modal is dismissed
            document.getElementById('cancelRideBtn').addEventListener('click', function() {
                scanning = true;
                requestAnimationFrame(tick);
            });
            
            // Show the modal
            const nonVerifiedModal = new bootstrap.Modal(modal);
            nonVerifiedModal.show();
        }

        document.addEventListener('DOMContentLoaded', startScanner);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>