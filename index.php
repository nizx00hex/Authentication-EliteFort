<?php

require_once __DIR__ . '/libs/init.php';

$conn = Database::getConnection();

$email = "nisath.hex@gmail.com";
$password = "nisath";





// $user = new User($email);
// $user = User::_login($email, $password);

User::_signup("Hexona Teora", "hDfii", "h@dfsmail.com, ", "pasdfssf", 'pasdfssf');

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