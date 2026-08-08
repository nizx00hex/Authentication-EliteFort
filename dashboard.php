<?php
require_once 'libs/__init__.php';

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}


$success = '';
$error = '';

$flash = Session::getFlash();

if ($flash !== null) {

    if ($flash['type'] === 'success') {
        $success = $flash['message'];
    }

    if ($flash['type'] === 'error') {
        $error = $flash['message'];
    }
}


echo $success;
echo "<br> <a href='logout.php'>Logout</a>";
?>
