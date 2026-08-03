<?php
require_once __DIR__ . '/libs/init.php';
if (!isset($_SESSION['isloggedin']) || $_SESSION['isloggedin'] !== true) {
    header('Location: login.php');
    exit;
}
?>
hello