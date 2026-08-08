<?php
class Auth{
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
        
        $conn = Database::getConnection();

        $email = trim($email);



        if ($email === '' || $password === '') {
            // Audit::log(null, 'FIELD_REQUIRE', 'INFO', 'FAILED', $email, 'Enter the email and password');
            throw new Exception('
            Email and password are required.
            ');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('
            Enter a valid email address.
            ');
        }
        if (strlen($email) > 128) {
            throw new InvalidArgumentException(
                'Email address is too long.'
            );
        }



        if (strlen($password) > 255) {
            throw new InvalidArgumentException(
                'Invalid password.'
            );
        }

        $query = "SELECT `id`, `fullname`, `username`, `email`, `password`, `is_verified` FROM `Auth` WHERE `email` = :email LIMIT 1";
       
        $stmt = $conn->prepare($query);
        
        $stmt->execute([
            'email'    => $email
        ]);
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Audit::log(null, 'USER_NOT_FOUND', 'INFO', 'FAILED', $email, 'Incorrect email.');
            throw new Exception('
                Enter the correct email or password.
            ');
        }

        // if (!self::isVerified($email)) {
        //     throw new Exception("Please verify your account first.");
        // }

        if (!password_verify($password, $user['password'])) {
            Audit::log(null, 'LOGIN_FAILED', 'WARNING', 'FAILED', $email, 'Incorrect password');
            throw new Exception('
                Enter the correct email or password.
            s');
        }

        Audit::log($user['id'], 'LOGIN_SUCCESS', 'INFO', 'SUCCESS', $email);
        unset($user['password']);
        return $user;
    }

    public static function _signup($fullName, $username, $email, $password, $cPassword) {
        $conn = Database::getConnection();
        //Field Required
        if (trim($fullName) === '' || trim($username) === '' || trim($email) === '' || trim($password) === '' || trim($cPassword) === '') {
            throw new Exception("All fields are required.");
        }
        // Full name validation
        if (strlen($fullName) < 3 || strlen($fullName) > 128) {
            throw new InvalidArgumentException(
                'Full name must be between 3 and 128 characters.'
            );
        }
        // Username validation
        if (strlen($username) < 4 || strlen($username) > 30) {
            throw new InvalidArgumentException(
                'Username must be between 4 and 30 characters.'
            );
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw new InvalidArgumentException(
                'Username can contain only letters, numbers and underscores.'
            );
        }
        //Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Audit::log(null, 'VALID_EMAIL_REQUIRED', 'INFO', 'FAILED', $email, 'Invalid email');
            throw new Exception("Invalid email address.");
        }
        if (strlen($email) > 128) {
            throw new InvalidArgumentException(
                'Email address is too long.'
            );
        }
        // Password validation
        if (strlen($password) < 8) {
            throw new InvalidArgumentException(
                'Password must contain at least 8 characters.'
            );
        }
        if (!preg_match('/[A-Z]/', $password)) {
            throw new InvalidArgumentException(
                'Password must contain an uppercase letter.'
            );
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new InvalidArgumentException(
                'Password must contain a lowercase letter.'
            );
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new InvalidArgumentException(
                'Password must contain a number.'
            );
        }

        if($password !== $cPassword) {
            throw new Exception("Confirm Password is not correct");
        }

        // Check duplicate username or email
        if (self::_exists($email, $username)) {
            throw new Exception(
                'Username or email already exists.'
            );
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO `Auth` (`fullname`, `username`, `email`, `password`, `register_date`, `register_ip`, `register_agent`)
VALUES (:fullname, :username, :email, :password, now(), :ip, :agent);";

       try {
            $stmt = $conn->prepare($query);

            $stmt->execute([
                'fullname' => $fullName,
                'username' => $username,
                'email'    => $email,
                'password' => $passwordHash,
                'ip'       => $ip,
                'agent'    => substr($agent, 0, 255)
            ]);

            $userId = $conn->lastInsertId();

            Audit::log($userId, 'SIGNUP_SUCCESS', 'INFO', 'SUCCESS', $username);
            return $userId;

        } catch (PDOException $e) {
            // Log the real database error privately
            error_log($e->getMessage());

            throw new Exception(
                'Signup failed. Please try again.'
            );
        } 
    }
    //verify the account 
    public static function isVerified($email) {
        $conn = Database::getConnection();

        $query = "SELECT `is_verified` FROM `Auth` WHERE `email` = :email LIMIT 1";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            'email'    => $email,
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }

        return (int) $user['is_verified'] === 1;
    }
    //is account exist?
    private static function _exists($email, $username) {
        $conn = Database::getConnection();

        $query = "SELECT `id` FROM `Auth` WHERE `email` = :email OR `username` = :username LIMIT 1";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            'email'    => $email,
            'username' => $username
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}





/* Login Usage
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $user = User::_login($email, $password);

        Session::start();
        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::set('logged_in', true);

        header('Location: index.php');
        exit;
    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
*/


/*Sign up usage
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userId = User::_signup(
            $_POST['fullname'] ?? '',
            $_POST['username'] ?? '',
            $_POST['email'] ?? '',
            $_POST['password'] ?? '',
            $_POST['confirm_password'] ?? ''
        );

        header('Location: verify-otp.php?user=' . $userId);
        exit;

    } catch (InvalidArgumentException $e) {
        $error = $e->getMessage();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
 */