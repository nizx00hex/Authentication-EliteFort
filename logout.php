<?php

declare(strict_types=1);

include "_core/__init__.php";


// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     header('Location: dashboard.php');
//     exit;
// }

// try {

//     Csrf::protect();
//     $userId = Session::userId();
//     try {
//         RememberMe::forget();
//         if ($userId !== null) {
//             AuditLog::rememberRevoked(
//                 $userId
//             );
//         }
//     } catch (Throwable $e) {
//         error_log(
//             'Remember logout error: ' .
//             $e->getMessage()
//         );
//     }

//     if ($userId !== null) {

//         try {
//             AuditLog::logout($userId);
//             AuditLog::sessionRevoked(
//                 $userId
//             );
//         } catch (Throwable $e) {
//             error_log(
//                 'Logout audit error: ' .
//                 $e->getMessage()
//             );
//         }
//     }

// } finally {
//     Session::logout();
// }

RememberMe::forget();
Session::logout();

header('Location: login.php');
exit;