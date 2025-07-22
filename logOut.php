<?php
include_once './assets/php/connect.php';
session_start();

$updateStatusQuery = "UPDATE users SET isRiding = 0 WHERE userId = {$_SESSION['userId']}";
executeQuery($updateStatusQuery);
session_destroy();
header('Location: ./index.php');
exit;
