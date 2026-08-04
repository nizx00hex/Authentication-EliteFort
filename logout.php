<?php
require_once __DIR__ . '/libs/init.php';

if(isset($_GET['logout'])) {
    Session::destroy();
    // die("Session Destroyed, Login again Click <a href='login.php'> Here</a><br>");
    header('location: login.php');
    exit;
} 