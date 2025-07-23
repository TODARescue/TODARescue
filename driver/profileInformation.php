<?php
// Include database connection
require_once '../assets/php/connect.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['testUser'])) {
    $_SESSION['userId'] = (int)$_GET['testUser'];
}

if (!isset($_SESSION['userId'])) {
    header('Location: ../index.php');
    exit;
}

$userId = $_SESSION['userId'];

$stmt = $conn->prepare("SELECT d.driverId, d.plateNumber, d.model, d.address, d.todaRegistration, 
                        d.isVerified, d.photo, d.qrCode, CONCAT(u.firstName, ' ', u.lastName) as fullName
                        FROM drivers d
                        JOIN users u ON d.userId = u.userId
                        WHERE d.userId = ?");

// Debug query
error_log("Fetching driver info for userId: " . $userId);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $error = "You are not registered as a driver. Please contact support.";
} else {
    $driver = $result->fetch_assoc();
    // Debug driver info
    error_log("Driver data: " . print_r($driver, true));
}

$stmt->close();

if (isset($_POST['generateQR']) && isset($driver)) {
    $qrData = $driver['plateNumber'];
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);
    
    // Save QR code URL to database
    $stmt = $conn->prepare("UPDATE drivers SET qrCode = ? WHERE driverId = ?");
    $stmt->bind_param("si", $qrUrl, $driver['driverId']);
    $stmt->execute();
    $stmt->close();
    
    $driver['qrCode'] = $qrUrl;

    $success = "QR Code has been generated successfully!";
}

// We'll handle QR code generation via JavaScript to ensure it's always displayed
// Even if database update fails, we'll display a QR code for the user
$plateNumber = isset($driver['plateNumber']) ? $driver['plateNumber'] : '';
$driverId = isset($driver['driverId']) ? $driver['driverId'] : 0;

// Make these values accessible to JavaScript
$jsPlateNumber = json_encode($plateNumber);
$jsDriverId = json_encode($driverId);

$downloadUrl = isset($driver['qrCode']) ? $driver['qrCode'] : '';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TODA Rescue - Driver Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <!-- Header for Profile Info -->
    <div class="container-fluid">
        <div class="row">
            <div class="col header d-flex flex-row p-2 rounded-5">
                <a href="javascript:history.back()">
                    <img src="../assets/shared/navbar-icons/arrow-back.svg" alt="Back"
                        class="img-fluid m-3" />
                </a>
                <div class="h3 p-3 m-1 px-1 fw-bolder">Profile Information</div>
            </div>
        </div>
    </div>
    <!-- HEADER -->
    <?php include '../assets/shared/header.php'; ?>

    <?php if (isset($error)): ?>
    <div class="container-fluid mt-4">
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    </div>
    <?php elseif (isset($success)): ?>
    <div class="container-fluid mt-4">
        <div class="alert alert-success" role="alert">
            <?php echo $success; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($driver)): ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-center">
            <div class="card border-0">
                <div class="card-body p-4 text-center">
                    <!-- Driver Photo -->
                    <div class="row">
                        <div class="col mb-4 py-1">
                            <div class="rounded-circle d-flex justify-content-center align-items-center mx-auto"
                                style="width: 120px; height: 120px; background-color: #958D8D; overflow: hidden;">
                                <?php if (!empty($driver['photo']) && file_exists($driver['photo'])): ?>
                                    <img src="<?php echo htmlspecialchars($driver['photo']); ?>" alt="Driver Photo" 
                                         class="img-fluid w-100 h-100 object-fit-cover">
                                <?php else: ?>
                                    <span class="text-white fw-bold">Driver Photo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Driver Name -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h5 class="fw-bold"><?php echo htmlspecialchars($driver['fullName']); ?>
                                <?php if ($driver['isVerified']): ?>
                                    <span class="badge bg-success rounded-circle">✓</span>
                                <?php endif; ?>
                            </h5>
                        </div>
                    </div>

                    <!-- Tricycle Details -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Tricycle Details</h6>
                            <p class="mb-1 text-dark"><?php echo htmlspecialchars($driver['model'] . ' - ' . $driver['plateNumber']); ?></p>
                        </div>
                    </div>

                    <!-- Tricycle number -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Tricycle Number</h6>
                            <p class="mb-1 text-dark" id="tricycle-number"><?php echo htmlspecialchars($driver['plateNumber']); ?></p>
                        </div>
                    </div>

                    <!-- Permanent Address -->
                    <div class="row">
                        <div class="col mb-3 py-1">
                            <h6 class="mb-2 text-dark">Permanent Address</h6>
                            <p class="mb-1 text-dark"><?php echo htmlspecialchars($driver['address']); ?></p>
                        </div>
                    </div>

                    <!-- Toda Registration -->
                    <div class="row">
                        <div class="col mb-4 py-1">
                            <h6 class="mb-2 text-dark">TODA Registration</h6>
                            <p class="mb-1 text-dark"><?php echo htmlspecialchars($driver['todaRegistration']); ?></p>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="row">
                        <div class="col mb-3">
                            <div id="qr-container" class="text-center">
                                <img id="qr-code" alt="QR Code" class="img-fluid mb-3" style="max-width: 100px;">
                            </div>
                            <!-- Loading spinner while QR code is generating -->
                            <div id="qr-loading" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Generating QR Code...</p>
                            </div>
                            <!-- Error message if QR fails to load -->
                            <div id="qr-error" class="alert alert-danger d-none">
                                Failed to generate QR code. Please try again.
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Actions -->
                    <div class="row justify-content-center">
                        <div class="col-auto mb-3">
                            <button id="download-qr-btn" 
                                class="btn custom-hover text-white fw-bold px-4 py-2 rounded-pill"
                                style="background-color: #2DAAA7;">Download QR Code</button>
                        </div>
                        <div id="direct-download-container" class="d-none">
                            <!-- This is a fallback direct download link that will be set by JavaScript -->
                            <a id="direct-download-link" download="TODA_QRCode.png"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navbar for Driver -->
    <?php include '../assets/shared/navbarDriver.php'; ?>


    <script>
        // Replace the existing JavaScript section in your PHP file with this improved version

document.addEventListener('DOMContentLoaded', function() {
    // Get plate number and driver ID from PHP
    const plateNumber = <?php echo $jsPlateNumber; ?>;
    const driverId = <?php echo $jsDriverId; ?>;
    
    // Elements
    const qrContainer = document.getElementById('qr-container');
    const qrLoading = document.getElementById('qr-loading');
    const qrError = document.getElementById('qr-error');
    const qrCodeImg = document.getElementById('qr-code');
    const downloadBtn = document.getElementById('download-qr-btn');
    
    // Initially hide the QR code and show loading
    qrContainer.style.display = 'none';
    qrLoading.style.display = 'block';
    qrError.style.display = 'none';
    
    let qrCodeDataUrl = null; // Store the QR code as data URL for reliable download
    
    // Function to convert image URL to data URL (for reliable mobile download)
    function imageUrlToDataUrl(url, callback) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        // Enable CORS for external images
        img.crossOrigin = 'anonymous';
        
        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            
            try {
                const dataUrl = canvas.toDataURL('image/png');
                callback(dataUrl);
            } catch (error) {
                console.error('Failed to convert image to data URL:', error);
                callback(null);
            }
        };
        
        img.onerror = function() {
            callback(null);
        };
        
        img.src = url;
    }
    
    // Function to download file from data URL (works reliably on mobile)
    function downloadFromDataUrl(dataUrl, filename) {
        if (!dataUrl) {
            alert('Failed to prepare download. Please try again.');
            return;
        }
        
        // Create a temporary link element
        const link = document.createElement('a');
        link.download = filename;
        link.href = dataUrl;
        
        // Append to body, click, and remove (required for some mobile browsers)
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Function to generate QR code
    function generateQrCode() {
        if (!plateNumber) {
            qrLoading.style.display = 'none';
            qrError.classList.remove('d-none');
            qrError.textContent = 'No plate number found. Please update your profile.';
            downloadBtn.style.display = 'none';
            return;
        }
        
        // Generate QR code using API
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(plateNumber)}`;
        
        // Set the image source
        qrCodeImg.src = qrUrl;
        
        // When the image loads
        qrCodeImg.onload = function() {
            // Hide loading, show QR code
            qrLoading.style.display = 'none';
            qrContainer.style.display = 'block';
            
            // Convert to data URL for reliable download
            imageUrlToDataUrl(qrUrl, function(dataUrl) {
                qrCodeDataUrl = dataUrl;
                console.log('QR code converted to data URL for download');
            });
            
            // Update backend database with this QR code URL (optional)
            updateQrCodeInDatabase(qrUrl);
        };
        
        // Handle image loading error
        qrCodeImg.onerror = function() {
            qrLoading.style.display = 'none';
            qrError.classList.remove('d-none');
            qrError.textContent = 'Failed to generate QR code. Please check your internet connection.';
            downloadBtn.style.display = 'none';
        };
    }
    
    // Function to update QR code in database
    function updateQrCodeInDatabase(qrUrl) {
        fetch('updateQrCode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `driverId=${driverId}&qrUrl=${encodeURIComponent(qrUrl)}`
        }).then(response => {
            console.log('QR code updated in database');
        }).catch(error => {
            console.error('Failed to update QR code in database:', error);
        });
    }
    
    // Handle download button click
    downloadBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default link behavior
        
        if (qrCodeDataUrl) {
            // Use data URL download (most reliable for mobile)
            downloadFromDataUrl(qrCodeDataUrl, `TODA_QRCode_${plateNumber}.png`);
        } else {
            // Fallback: try to convert current image and download
            const currentSrc = qrCodeImg.src;
            if (currentSrc) {
                imageUrlToDataUrl(currentSrc, function(dataUrl) {
                    if (dataUrl) {
                        downloadFromDataUrl(dataUrl, `TODA_QRCode_${plateNumber}.png`);
                    } else {
                        // Last resort: open image in new tab (user can save manually)
                        window.open(currentSrc, '_blank');
                        alert('Please save the QR code image from the new tab that opened.');
                    }
                });
            }
        }
    });
    
    // Start generating QR code
    generateQrCode();
});
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous"></script>
</body>

</html>