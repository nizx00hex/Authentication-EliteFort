<?php

require_once 'libs/__init__.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$username = Session::getAuth('fullname'); 

echo 'Welcome back, ' . htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

echo "<br><a href='logout.php'>Logout</a>";