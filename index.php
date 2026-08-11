<?php
require_once __DIR__ . '/libs/__init__.php';


$conn = Database::getConnection();

// $email = "nisath.hex@gmail.com";
// $password = "nisath";








// $user = new User($email);
// $user = User::_login($email, $password);

// User::_signup("Hexona Teora", "hDfii", "h@dfsmail.com, ", "pasdfssf", 'pasdfssf');

// if(User::_exists("nisath.hex@dddgmail.com","nisath")) {
//     echo "Not Exist";
// } else {
//     echo "Exist";
// }
// echo "<pre>";
// print_r($_SERVER);
// echo "</pre>";
// echo $user->id;
// $user = User::_login($email, $password);

// print_r($user);

// echo 'Welcome, MR.' . ucfirst($user ?? 'Unknown');
// function getUser($conn, $id) {
//     $query = "SELECT * FROM Users WHERE id = :id LIMIT 1";

//     $stmt = $conn->prepare($query);
//     $stmt->execute(['id' => $id]);

//     if ($user = $stmt->fetch()) {
//         return $user;
//     } else {
//         return false;
//     }
// }

// $user = getUser($conn, 1);
// echo $user['username'];

// function getAllUsers(PDO $conn): array
// {
//     $query = "SELECT * FROM Users";

//     $stmt = $conn->prepare($query);
//     if($stmt->execute()) {
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//         // return $stmt->fetchAll();   
//     } else {
//         return [];
//     }
// /*
//     PDO::FETCH_ASSOC
//         $stmt->fetchAll(PDO::FETCH_ASSOC);

//         This tells PDO to return each row using only column names:

//         [
//             'id' => 1,
//             'name' => 'TEORA'
//         ]

//         Without PDO::FETCH_ASSOC, PDO may return both numeric and named indexes:

//         [
//             0      => 1,
//             'id'   => 1,
//             1      => 'TEORA',
//             'name' => 'TEORA'
//         ]

//         So PDO::FETCH_ASSOC avoids duplicated values and is recommended.
//  */
    
// }

// $users = getAllUsers($conn);

// foreach ($users as $user) {
//     echo htmlspecialchars($user['username']) . '<br>';
//     // echo "<pre>";
//     // print_r($user);
//     // echo "</pre>";
// }











//OTP Testing

// $otp = Otp::_genarate();
// echo "<br>";
// $otpHash = Otp::_hash($otp);
// echo "<br>";
// $otpVerify = Otp::_verifyHash($otp, $otpHash);
// echo "<br>";

// $otpExpiry = Otp::_createExpiry();


// echo $otp;
// echo "<br>";

// echo $otpHash;
// echo "<br>";

// echo $otpVerify;
// echo "<br>";

// echo $otpExpiry;
// echo "<br>";

// if(Otp::_isExpired($otpExpiry)) {
//     echo "Expired";
// } else {
//     echo "Not expired";
// }

// echo "<br>";
// echo Otp::_createForUser("nisath");
//528033
// if(Otp::_verifyForUser("nisath", "528033")) {
//     echo "user verified successfully";
// } else {
//     echo "enter correct otp";
// }

// if(Otp::_activateUser("nisath")) {
//     echo "Account activated";
// } else {
//     echo "Activation failed";
// }










//New Signup Testing
// User::_signup("Hexona Teora", "hDfii", "h@dfsmail.com, ", "pasdfssf", 'pasdfssf');
// $userid = Auth::_signup("Hexona Teora", "hexona", "hexona.teora@gmail.com", "pASs1WORD", 'pASs1WORD');
// echo Otp::_createForUser($userid);

// 361177

// try {
//     Otp::_verifyForUser(8,"361177");
//     echo true;
// } catch(Exception $e) {
//     echo $e->getMessage();
// }

Auth::isVerified("nisath.hex@gmail.com");

echo "<a href='login.php'>Login</a>";
echo "<br>";
echo "<a href='signup.php'>Signup</a>";
