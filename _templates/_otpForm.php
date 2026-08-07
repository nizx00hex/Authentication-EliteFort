
<?php

$success = '';
$error = '';


$opt = Session::get('otp');
echo $opt;
$userid = Session::get('user_id');


if($userid == '') {
  header('Location: signup.php');
  exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? 'verify';

    // $fullname = trim($_POST['full_name'] ?? '');
    // $username = trim($_POST['username'] ?? '');
    // $email = trim($_POST['email'] ?? '');
    // $password = $_POST['password'] ?? '';
    // $cPassword = $_POST['confirm_password'] ?? '';

    // echo $email;
    try {
        if($action === 'resend'){
            $newOtp = Otp::_resend($userid);
        }

        $success = "A new OTP was sent. It expires in 5 minutes.";


        if($action === 'verify'){
            $enteredOtp = trim($_POST['otp']);

            Otp::_verifyForUser($userid, $opt);
        }

        Session::set('flash_success', 'Account verified successfully. Please log in.');


        header('Location: login.php');
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


<main class="otp-card" role="main" aria-label="OTP verification panel">
    <div class="corner-deco"></div>

    <header class="card-header">
      <h1>Verify OTP</h1>
      <p>We sent a 6‑digit code to your email</p>
    </header>

    <form
      class="otp-form"
      id="otpForm"
      action="#"
      method="post"
      novalidate
    >
      <div class="input-group" id="otpGroup">
        <input type="text" id="otpCode" name="otp_code" placeholder="Enter 6‑digit code" autocomplete="one-time-code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required/>
        <i class="fas fa-shield-alt input-icon"></i>
      </div>

      <div id="otpMessage" class="field-message" aria-live="polite"></div>

      <div class="otp-timer">
        <span id="timerDisplay">5:00</span>
        <button type="submit" class="resend-link" id="resendBtn"><a href="otp-verify.php?resend"></a>Resend code</button>
      </div>

      <button type="submit" class="verify-btn">
        <span>Verify &amp; continue</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <!-- <div class="divider">or continue with</div> -->
<!-- 
    <div class="social-row">
      <a href="#" aria-label="Continue with Google">
        <i class="fab fa-google"></i>
        Google
      </a>

      <a href="#" aria-label="Continue with Apple">
        <i class="fab fa-apple"></i>
        Apple
      </a>
    </div> -->

    <!-- <footer class="card-footer">
      Didn't receive the code? <a href="#" id="emailChangeLink">Change email</a>
    </footer> -->
  </main>
  