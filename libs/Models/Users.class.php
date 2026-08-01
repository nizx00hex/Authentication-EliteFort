<?php
class Users{
    private $connection;

    // public $id;
    // public $user;
    // public $table;

    public function _login($email, $password)  {

        $connection = Database::getConnection();

        //This query expect three posibilities username, email for login
        // $query = "SELECT * FROM `auth` WHERE `username` = ':user' OR `email` = ':user'";
        //This expecy only email.
        $query = "SELECT * FROM `Users` WHERE `email` = :email";

        // var_dump(get_class($connection));
        // var_dump($query);
        
        $stmt = $connection->prepare($query);

        $stmt->execute(['email'    => trim($email)]);

        // $email = htmlspecialchars(strip_tags($email));
        // $stmt->bindParam(':email', $email);
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userInfo) {
            if (password_verify($password, $userInfo['password'])) {
                // echo "works";
                return $userInfo;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function _signup($fullName, $username, $email, $password, $cPassword) {

    }

    public function _addSession($username, $token, $expiry) {

    }

}