<?php
require_once '_core/__init__.php';
if(!Session::isAuthenticated()) {
    header("Location: login.php");
    exit;
}


// $conn = Database::getConnection();

// if($conn) {
//     echo "hi";
// } else {
//     echo 'no';
// }
//    echo basename($_SERVER['PHP_SELF'], ".php");