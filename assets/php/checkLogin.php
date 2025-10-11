<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$userId = $_SESSION['userId'] ?? null;

if (!$userId) {
    header("Location: ../index.php");
    exit();
}
