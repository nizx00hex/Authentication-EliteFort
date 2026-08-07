<?php

$success = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $cPassword = $_POST['confirm_password'] ?? '';

    echo $email;
    try {
        $userid = Auth::_signup(
          $fullname, 
          $username, 
          $email, 
          $password,
          $cPassword
        );

        $otp = Otp::_createForUser($userid);

        Session::set('user_id', $userid);
        Session::set('otp', $otp);


        header('Location: otp-verify.php?action=verify');
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
        <style>

    .alert {
        position: fixed;
        top: 25px;
        right: 25px;
        z-index: 9999;

        display: flex;
        align-items: center;
        gap: 14px;

        width: min(420px, calc(100% - 40px));
        padding: 16px 48px 16px 16px;

        font-family: Arial, sans-serif;

        border-radius: 14px;
        backdrop-filter: blur(16px);
        overflow: hidden;

        box-shadow:
            0 20px 45px rgba(0, 0, 0, 0.35),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);

        animation: alertShow 0.4s ease forwards;
    }

    .alert.hide {
        animation: alertHide 0.3s ease forwards;
    }

    .alert-error {
        color: #ffdce3;
        background: rgba(44, 10, 19, 0.95);
        border: 1px solid rgba(255, 70, 104, 0.45);
    }

    .alert-success {
        color: #dcffe9;
        background: rgba(7, 40, 26, 0.95);
        border: 1px solid rgba(48, 220, 133, 0.45);
    }

    .alert-icon {
        flex-shrink: 0;

        display: grid;
        place-items: center;

        width: 44px;
        height: 44px;

        color: #ffffff;
        font-size: 23px;
        font-weight: 800;

        border-radius: 50%;
    }

    .alert-error .alert-icon {
        background: linear-gradient(135deg, #ff3b68, #ff7895);
        box-shadow: 0 8px 24px rgba(255, 59, 104, 0.35);
    }

    .alert-success .alert-icon {
        background: linear-gradient(135deg, #1fc978, #61e9a7);
        box-shadow: 0 8px 24px rgba(31, 201, 120, 0.35);
    }

    .alert-content {
        display: flex;
        flex-direction: column;
        gap: 4px;

        min-width: 0;
        line-height: 1.4;
    }

    .alert-content strong {
        color: #ffffff;
        font-size: 16px;
    }

    .alert-content span {
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .alert-error .alert-content span {
        color: #ffb8c5;
    }

    .alert-success .alert-content span {
        color: #adf5ca;
    }

    .alert-close {
        position: absolute;
        top: 50%;
        right: 13px;

        display: grid;
        place-items: center;

        width: 30px;
        height: 30px;

        color: inherit;
        font-size: 24px;
        line-height: 1;

        background: transparent;
        border: none;
        border-radius: 50%;

        cursor: pointer;
        transform: translateY(-50%);
        transition: 0.2s ease;
    }

    .alert-close:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-50%) rotate(90deg);
    }

    .alert-progress {
        position: absolute;
        bottom: 0;
        left: 0;

        width: 100%;
        height: 3px;

        animation: alertTimer 5s linear forwards;
    }

    .alert-error .alert-progress {
        background: linear-gradient(90deg, #ff3b68, #ff8ca5);
    }

    .alert-success .alert-progress {
        background: linear-gradient(90deg, #20ca79, #73efb5);
    }

    @keyframes alertShow {
        from {
            opacity: 0;
            transform: translateX(40px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes alertHide {
        from {
            opacity: 1;
            transform: translateX(0) scale(1);
        }

        to {
            opacity: 0;
            transform: translateX(40px) scale(0.95);
        }
    }

    @keyframes alertTimer {
        from {
            width: 100%;
        }

        to {
            width: 0;
        }
    }

    @media (max-width: 600px) {
        .alert {
            top: 15px;
            right: 15px;
            left: 15px;

            width: auto;
        }
    }
        </style>
    <?php endif; ?>

<main class="signup-card" role="main" aria-label="Signup panel">
    <div class="corner-deco"></div>

    <header class="card-header">
      <h1>Create account</h1>
      <p>Enter your information to get started</p>
    </header>

    <form class="signup-form" id="signupForm" action="#" method="post" novalidate >
      <div class="input-group">
        <input type="text" id="fullName" name="full_name" placeholder="Full name" autocomplete="name" minlength="2"  />
        <i class="fas fa-user input-icon"></i>
      </div>

      <div class="input-group">
        <input type="text" id="username" name="username" placeholder="Username" autocomplete="username" minlength="3"  />
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