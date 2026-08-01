<?php
class Users{
    private $connection;

    // public $id;
    // public $user;
    // public $table;

    public function __login($user, $password)  {

        $connection = Database::getConnection();

        $query = "SELECT * FROM `auth` WHERE `username` = ':user' OR `email` = ':user' OR `phone` = ':user'";
        $stmt = $this->conn->prepare($query);

        $user = htmlspecialchars(strip_tags($user));
        $stmt->bindParam(':name', $user);

        if($stmt->execute()) {
            $account = $stmt->fetch(PDO::FETCH_ASSOC);
            if($account && strcmp($user, $account['username'])) {
                if (password_verify($password, $account['password'])) {
                    // session_start();

                    // session_regenerate_id(true);
                    // $_SESSION['user_id']  = $account['id'];
                    // $_SESSION['username'] = $account['username'];

                    echo "Worked";
                } else { 
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function __signup($fullName, $username, $email, $password, $cPassword) {

    }

    public function __addSession($username, $token, $expiry) {

    }

}