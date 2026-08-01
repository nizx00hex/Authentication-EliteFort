<?php

require_once __DIR__ . '/libs/init.php';

$conn = Database::getConnection();

$email = "nisath.hex@gmail.com";
$password = "nisath";



$user = new Users();
$user = $user->_login($email, $password);

echo 'Welcome, MR.' . ucfirst($user['username'] ?? 'Unknown');
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