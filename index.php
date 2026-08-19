<?php
require_once '_core/__init__.php';


// if (!Session::validate()) {
//     header("Location: login.php");
//     exit;
// }

// print_r($_SESSION);

// $userId = $_SESSION['auth']['user_id'];
// echo $userId;

// if (!Session::validateSessionUser($userId)) {
//     Session::logout();

//     header('Location: login.php');
//     exit;
// }



// $conn = Database::getConnection();

// if($conn) {
//     echo "hi";
// } else {
//     echo 'no';
// }
//    echo basename($_SERVER['PHP_SELF'], ".php");

// Session::flash('success', 'OTP Verified. You can login now.');
// echo "Session set";
echo "<br>";
echo '<a href="pages.php">Pages</a>';
echo "<br>";

echo '<a href="logout.php">Logout</a>';


echo "<br>";
echo "<br>";
echo "<br>";


// $session = Session::sessionExists(52);
// Session::createSessionRecord(52);
// echo $session['session_id_hash'];
// echo "<br>";
// echo "<pre>";
// print_r($session);
// print_r($_SERVER);
// echo "</pre>";

// if($session['session_id_hash'] === Session::get('session_hash_id')){
//     if($session['user_agent'] === $_SERVER['HTTP_USER_AGENT']) {
//         if($session['ip_address'] === $_SERVER['REMOTE_ADDR']) {
//             echo "User agent is same!!!!";
//             return true;
//         }
//     }
// } else {
//     echo "<br>";
//     echo "{sesson hijacking}detected.";
// }
?>
<!-- <pre>
teora:$2y$12$VPJzuYIn.gHjNrQtrcbvPeCCK2C0wt/gLMeR16I7ygel8KMKXCm1y<br>naruto:$2y$12$jPaUO877i.JzpewAwIAc/u8N40lEMJ1NfOKDWhl6/idX00qWM7fsC<br>nisath:$2y$12$.J7WuUDQFAN/5NDl9ls3dOzeNilxlLuz/2mQzouvPgwoJGVflyEgC
</pre> -->
