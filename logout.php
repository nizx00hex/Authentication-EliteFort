<?php
require_once __DIR__ . '/libs/init.php';

if(isset($_GET['logout'])) {
    $userId = $_GET['id'];
    Audit::log($userId, 'LOGOUT_SUCCESS', 'INFO', 'SUCCESS');

    Session::destroy();
    // die("Session Destroyed, Login again Click <a href='login.php'> Here</a><br>");
    header('location: login.php');
    exit;
} 