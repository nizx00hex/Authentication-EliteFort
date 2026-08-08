<?php
if (Session::isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}



$error = '';
$success = '';


// ============================================
// GET FLASH MESSAGE
// ============================================


$flash = Session::getFlash();

if ($flash !== null) {

    if ($flash['type'] === 'success') {
        $success = $flash['message'];
    }

    if ($flash['type'] === 'error') {
        $error = $flash['message'];
    }
}

$email = Session::rememberedEmail();



// ============================================
// LOGIN
// ============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $remember = isset($_POST['remember']);
        $csrfToken = $_POST['csrf_token'] ?? null;

        if (!Session::verifyCsrf($csrfToken)) {
            throw new Exception(
                'Invalid request. Please refresh the page and try again.'
            );
        }

        $user = Auth::_login(
            $email,
            $password
        );

        Session::login($user);


        if ($remember) {
            Session::rememberEmail($email, 30);

        } else {

            Session::forgetRememberedEmail();
        }

        Session::flash('success', "Welcome back, $user[fullname] !");

        header('Location: dashboard.php');
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
  
    <?php if (!empty($success)): ?>

    <div
        class="alert alert-success"
        id="successAlert"
        role="alert"
    >
        <div class="alert-icon">✓</div>

        <div class="alert-content">
            <strong>Success</strong>

            <span>
                <?= htmlspecialchars(
                    $success,
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </span>
        </div>

        <button
            type="button"
            class="alert-close"
            onclick="closeAlert('successAlert')"
            aria-label="Close alert"
        >
            &times;
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

        <form
            class="login-form"
            action="login.php"
            method="post"
        >

            <!-- CSRF -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    Session::csrfToken(),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >


            <!-- EMAIL -->
            <div class="input-group">

                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Email address"
                    autocomplete="email"
                    value="<?= htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

                <i class="fas fa-envelope input-icon"></i>

            </div>


            <!-- PASSWORD -->
            <div class="input-group">

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password"
                    autocomplete="current-password"
                    required
                >

                <i class="fas fa-lock input-icon"></i>

            </div>


            <div class="form-options">

                <label for="remember">

                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        value="1"
                        <?= Session::rememberedEmail() !== ''
                            ? 'checked'
                            : '' ?>
                    >

                    <span>Remember me</span>

                </label>

                <a href="forgot-password.php">
                    Forgot password?
                </a>

            </div>


            <button
                type="submit"
                class="login-btn"
            >

                <span>Sign in</span>

                <i class="fas fa-arrow-right"></i>

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