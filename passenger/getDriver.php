<?php

header('Content-Type: application/json');

require_once '../assets/php/connect.php';

ini_set('display_errors', 0);
error_reporting(0);

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $qrData = isset($_POST['qrData']) ? sanitize_input($_POST['qrData']) : '';
    
    if (empty($qrData)) {
        echo json_encode(['success' => false, 'message' => 'No QR data provided']);
        exit();
    }
    
    try {
        $qrData = trim($qrData);
        
        $stmt = $conn->prepare("SELECT d.driverId, d.plateNumber, d.model, d.address, d.todaRegistration, 
                               d.isVerified, d.photo, u.firstName, u.lastName, u.userId
                               FROM drivers d
                               JOIN users u ON d.userId = u.userId
                               WHERE d.plateNumber = ?");
        
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
            exit();
        }
        
        $stmt->bind_param("s", $qrData);
        $success = $stmt->execute();
        
        if (!$success) {
            echo json_encode(['success' => false, 'message' => 'Database query failed']);
            exit();
        }
        
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $driver = $result->fetch_assoc();
            
            // Check if driver is verified
            if (!$driver['isVerified']) {
                echo json_encode(['success' => false, 'message' => 'Driver is not verified']);
                exit();
            }
            
            echo json_encode([
                'success' => true,
                'driverId' => $driver['driverId'],
                'message' => 'Driver found successfully'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No driver found with the provided QR code']);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error occurred']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

if (isset($conn) && $conn) {
    $conn->close();
}
?>