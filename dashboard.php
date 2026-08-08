<?php
require_once __DIR__ . '/libs/__init__.php';
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header('Location: login.php');
    exit;
}

echo "Welcome back, Mr." . ucfirst(Session::get('username'));
// echo "Welcome back, Mr." . ucfirst(Session::get('password'));


echo "<br>";
$userId = Session::get('user_id');
// $pass = Session::get('password');
// $email = Session::get('email');

// echo $pass;
// echo $email;
echo "Click to <a href='logout.php?logout&id={$userId}'>Logout</a>";
?>
