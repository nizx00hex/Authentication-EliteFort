<?php
require_once '_core/__init__.php';
// if(!Session::isAuthenticated()) {
//     header("Location: login.php");
//     exit;
// }


// $conn = Database::getConnection();

// if($conn) {
//     echo "hi";
// } else {
//     echo 'no';
// }
//    echo basename($_SERVER['PHP_SELF'], ".php");

Session::flash('success', 'OTP Verified. You can login now.');
echo "Session set";
echo "<br>";
echo '<a href="dashboard.php">Dashboard</a>';