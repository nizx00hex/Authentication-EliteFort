<?php
if (Session::isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}


$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $cPassword = $_POST['confirm_password'] ?? '';

    try {

        $userId = Auth::_signup(
            $fullName,
            $username,
            $email,
            $password,
            $cPassword
        );

        $otp = Otp::_createForUser($userId);
        // $otpExpiry = Otp::getExpiry($userId);

        Session::set('otp', $otp);
        // Session::set('otpExpiry', $otpExpiry);

        Session::setPendingVerification(
            (int) $userId,
            $email
        );
        /*
         * Send OTP here
         */
        // Mail::sendOtp($email, $otp);
        header('Location: otp-verify.php');
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

  <!-- Alert Message -->
    <?php if (!empty($error)): ?>

        <div class="alert alert-error" id="errorAlert" role="alert">
            <div class="alert-icon">
                !
            </div>
            <div class="alert-content">
                <strong>Signup Failed</strong>
                <span>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <button type="button" class="alert-close" onclick="closeAlert('errorAlert')" aria-label="Close alert">
                &times;
            </button>
            <div class="alert-progress"></div>
        </div>
    <?php endif; ?>

<main class="signup-card" role="main" aria-label="Signup panel">
    <div class="corner-deco"></div>

    <header class="card-header">
      <h1>Create account</h1>
      <p>Enter your information to get started</p>
    </header>

    <form class="signup-form" id="signupForm" action="signup.php" method="post" novalidate >
      <div class="input-group">
        <input type="text" id="fullName" name="full_name" placeholder="Full name" autocomplete="name" minlength="2"  />
        <i class="fas fa-user input-icon"></i>
      </div>

      <div class="input-group">
        <input type="text" id="username" name="username" placeholder="Username" autocomplete="username" minlength="4"  />
        <i class="fas fa-at input-icon"></i>
      </div>

      <div class="input-group">
        <input type="email" id="email" name="email" placeholder="Email address" autocomplete="email"  />
        <i class="fas fa-envelope input-icon"></i>
      </div>

      <div class="input-group" id="passwordGroup">
        <input type="password" id="password" name="password" placeholder="Create password" autocomplete="new-password" minlength="8"  />
        <i class="fas fa-lock input-icon"></i>

        <button type="button" class="password-toggle" data-target="password" aria-label="Show password" >
          <i class="fas fa-eye"></i>
        </button>
      </div>

      <p class="password-help">
        Use at least 8 characters.
      </p>

      <div class="input-group" id="confirmPasswordGroup">
        <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm password" autocomplete="new-password" minlength="8"  />
        <i class="fas fa-shield-alt input-icon"></i>

        <button type="button" class="password-toggle" data-target="confirmPassword" aria-label="Show confirm password" >
          <i class="fas fa-eye"></i>
        </button>
      </div>

      <p id="passwordMessage" class="field-message" aria-live="polite"></p>

      <div class="form-options">
        <div class="terms-row">
          <input type="checkbox" id="terms" name="terms" value="1"  />

          <div class="terms-copy">
            <label for="terms">I agree to the </label>
            <button
              type="button"
              class="policy-link"
              data-modal-target="termsModal"
            >Terms of Service</button>
            <span> and </span>
            <button type="button" class="policy-link" data-modal-target="privacyModal" >Privacy Policy</button><span>.</span>
          </div>
        </div>
      </div>

      <button type="submit" class="signup-btn">
        <span>Create account</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <div class="divider">or sign up with</div>

    <div class="social-row">
      <a href="#" aria-label="Sign up with Google">
        <i class="fab fa-google"></i>
        Google
      </a>

      <a href="#" aria-label="Sign up with Apple">
        <i class="fab fa-apple"></i>
        Apple
      </a>
    </div>

    <footer class="card-footer">
      Already have an account? <a href="login.php">Sign in</a>
    </footer>
  </main>