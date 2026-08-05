<?php
class User{
    private $conn;

    
    public $username;
    public $id = null;
    public $table = 'auth';


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

        if ($email === '' || $password === '') {
            throw new Exception("Email and password are required.");
        }

        $conn = Database::getConnection();
        $query = "SELECT * FROM `Auth` WHERE `email` = :email LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            'email'    => trim($email)
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$user) {
            throw new Exception("Enter the correct email or password.");
        }
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Enter the correct email or password.");
        }

        // Session::delete($user['password']);
        return $user;
    }

    public static function _signup($fullName, $username, $email, $password, $cPassword) {
        $conn = Database::getConnection();
        if (trim($fullName) === '' || trim($username) === '' || trim($email) === '' || trim($password) === '' || trim($cPassword) === '') {
            throw new Exception("All fields are required.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address.");
        }
        if($password !== $cPassword) {
            throw new Exception("Confirm Password is not correct");
        }

        $result = self::_exists($email, $username);
        if($result !== true) {
            throw new Exception("Username or Email already exists.");
        }

        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters.");
        }

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
        
        return $conn->lastInsertId();
    }

    public static function _exists($email, $username) {
        $conn = Database::getConnection();

        $queryCheck = "SELECT id FROM `Auth` WHERE `email` = :email OR `username` = :username LIMIT 1";


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

