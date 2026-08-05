<?php

$success = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $user = User::_login($email, $password);
        // var_dump($user);
        // exit;

        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);
        Session::delete($user['password']);
        Session::set('isLoggedIn', true);

        header("Location: dashboard.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}


?>
  
  
  
  <!-- Alert Message -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-error" id="errorAlert" role="alert">
            <div class="alert-icon">!</div>

            <div class="alert-content">
                <strong>Login Failed</strong>
                <span>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <button
                type="button" class="alert-close" onclick="closeAlert('errorAlert')" aria-label="Close alert"> &times;
            </button>

            <div class="alert-progress"></div>
        </div>
    <?php endif; ?>
  
  
  
  <div class="login-card" role="main" aria-label="Login panel">
    <!-- decorative corner -->
    <div class="corner-deco"></div>

    <!-- header -->
    <div class="card-header">
      <div class="logo-mark">
        <i class="fas fa-shield"></i>
      </div>
      <h1>Welcome back</h1>
      <p>Sign in to your account</p>
    </div>

    <!-- form -->
    <form class="login-form" action="login.php" method="post">
      <div class="input-group">
        
        <input type="email" name="email" id="email" placeholder="Email address"  autocomplete="email" />
        <i class="fas fa-envelope input-icon"></i>
      </div>

      <div class="input-group">
        <input type="password" name="password" id="password" placeholder="Password"  autocomplete="current-password" />
        <i class="fas fa-lock input-icon"></i>
      </div>

      <div class="form-options">
        <label for="remember">
          <input type="checkbox" id="remember" checked /> <span>Remember me</span>
        </label>
        <a href="#">Forgot password?</a>
      </div>

      <button type="submit" class="login-btn">
        <span>Sign in</span> <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <!-- divider -->
    <div class="divider">or continue with</div>

    <!-- social -->
    <div class="social-row">
      <a href="#" aria-label="Google"><i class="fab fa-google"></i> Google</a>
      <a href="#" aria-label="Apple"><i class="fab fa-apple"></i> Apple</a>
    </div>
 
    <!-- footer -->
    <div class="card-footer">
      Don't have an account? <a href="signup.php">Sign up</a>
    </div>
  </div>