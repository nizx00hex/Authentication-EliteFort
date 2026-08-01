<?php
require_once 'libs/init.php';


$conn = Database::getConnection();

$query = "SELECT * FROM Users;";

// if($conn) {
  

//     $stmt = $conn->prepare($query);
//     $stmt->execute();
//     $account = $stmt->fetch(PDO::FETCH_ASSOC);
// }

if ($stmt = $conn->prepare($query)) {

    /* execute statement */
    $stmt->execute();

    /* fetch values */
    while ($row = $stmt->fetch()) {
        // printf ("%s (%s)\n", $name, $code);
        echo $row['username'];
    }

    /* close statement */
    $stmt->close();
}
