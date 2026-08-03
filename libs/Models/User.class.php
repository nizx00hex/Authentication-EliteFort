<?php
class User{
    private $conn;

    public $table = 'auth';
    public $username;
    public $id = null;



    public function __construct($identifier)
    {
        $this->conn = Database::getConnection();

        $query = "SELECT `id`, `username` FROM `Auth` WHERE `username` = :username OR `id` = :id OR `email` = :email LIMIT 1 ";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            'username' => trim($identifier),
            'id'       => trim($identifier),
            'email'    => trim($identifier)
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("User doesn't exist.");
        }

        $this->id = (int) $user['id'];
    }

    public static function _login($email, $password)  {

        $conn = Database::getConnection();

        //This query expect three posibilities username, email for login
        // $query = "SELECT * FROM `auth` WHERE `username` = ':user' OR `email` = ':user'";
        //This expecy only email.
        $query = "SELECT * FROM `Auth` WHERE `email` = :email LIMIT 1";

        // var_dump(get_class($connection));
        // var_dump($query);
        
        $stmt = $conn->prepare($query);

        $stmt->execute([
            'email'    => trim($email)
        ]);

        // $email = htmlspecialchars(strip_tags($email));
        // $stmt->bindParam(':email', $email);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                // print_r($userInfo);
        
        if (!$user) {
            // print_r($userInfo);
            throw new Exception("Enter the correct email or password.");
        }
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Enter the correct email or password.");
        }

        return $user;
    }

    public static function _signup($fullName, $username, $email, $password, $cPassword) {
        $conn = Database::getConnection();

        $result = self::_exists($email, $username);
        if($result === true) {
            try {
                if($password === $cPassword) {

                    $ip = $_SERVER['REMOTE_ADDR'];
                    $agent = $_SERVER['HTTP_USER_AGENT'];
                    $pass = password_hash($password, PASSWORD_DEFAULT);

                    $query = "INSERT INTO `Auth` (`fullname`, `username`, `email`, `password`, `register_date`, `register_ip`, `register_agent`)
            VALUES (:fullname, :username, :email, :password, now(), :ip, :agent);";
                
                    $stmt = $conn->prepare($query);

                    $stmt->execute([
                        'fullname'  =>  $fullName,
                        'username'  =>  $username,
                        'email'     =>  $email,
                        'password'  =>  $pass,
                        'ip'        =>  $ip,
                        'agent'     =>  $agent
                    ]);
                } else {
                    echo "Confirm Password is not correct";
                    return false;
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        } else {
            echo "Username or Email already exists.";
            return false;
        }
        
    }

    public function _addSession($username, $token, $expiry) {

    }

    public static function _exists($email, $username) {
        $conn = Database::getConnection();

        $queryCheck = "SELECT * FROM `Auth` WHERE `email` = :email OR `username` = :username LIMIT 1";


        $check = $conn->prepare($queryCheck);

        $check->execute([
            'email'     =>  $email,
            'username'  =>  $username
        ]);

        $row = $check->fetch();
        // var_dump($row);
        // exit();
        if ($row != null) {
            // throw new AuthException('An account already exists with this email address.');
            return false;
        } else {
            return true;
        }

    }

}