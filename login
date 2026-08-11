

if (Session::get('isLoggedIn')) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Email and password are required.";
    } else {

        try {
            $user = User::_login($email, $password);

            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('isLoggedIn', true);

            header("Location: dashboard.php");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}


    public static function isVerified($email) {
        $conn = Database::getConnection();

        $user = self::_exists($email);

        if($user['is_verified'] == 1) {
            echo 'User verified!!';
        } else {
            echo 'User verified pending...';
        }
    }