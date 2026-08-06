<?php
require_once __DIR__ . '/libs/init.php';
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header('Location: login.php');
    exit;
}

echo "Welcome back, Mr." . ucfirst(Session::get('username'));
// echo "Welcome back, Mr." . ucfirst(Session::get('password'));


echo "<br>";
$userId = Session::get('user_id');

echo "Click to <a href='logout.php?logout&id={$userId}'>Logout</a>";
?>
