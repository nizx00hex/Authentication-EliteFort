<?php


$error = '';
$success = '';

$userId = Session::pendingUserId();
$email = Session::pendingEmail();

// echo "<pre>";
// print_r(Session::get('pending_verification'));
// echo "</pre>";
echo Session::get('otp');
// $otpExpiryTimestamp = strtotime(Session::get('otpExpiry'));


// No pending verification
if ($userId === null || $email === null) {
    header('Location: signup.php');
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'resend') {
    try {
        $otp2 = Otp::_resend($userId);
        Session::set('otp', $otp2);

        // Mail::sendOtp($email, $otp);
        $success = 'A new OTP has been sent to your email.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}


// =============================================
// VERIFY OTP
// =============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    try {
        $otp = trim($_POST['otp_code'] ?? '');

        // Verify OTP + activate account
        Otp::_verifyForUser($userId, $otp);

        // OTP process finished
        Session::clearPendingVerification();

        // Message for login page
        Session::flash(
            'success',
            'Email verified successfully. You can now login.'
        );
        header('Location: login.php');
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>



    <?php if (!empty($error)): ?>
        <div class="alert alert-error" id="errorAlert" role="alert">
            <div class="alert-icon">!</div>

            <div class="alert-content">
                <strong>Verification Failed</strong>
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
    <div class="alert alert-success" id="successAlert" role="alert">
        <div class="alert-icon">✓</div>

        <div class="alert-content">
            <strong>OTP Sent</strong>

            <span>
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>

        <button type="button" class="alert-close" onclick="closeAlert('successAlert')" aria-label="Close alert">
            &times;
        </button>
        <div class="alert-progress"></div>
    </div>
    <?php endif; ?>



<main class="otp-card" role="main" aria-label="OTP verification panel">
    <div class="corner-deco"></div>

    <header class="card-header">
      <h1>Verify OTP</h1>
      <p>We sent a 6‑digit code to your email</p>
    </header>

    <form class="otp-form" id="otpForm" action="otp-verify.php" method="post" novalidate>

      <div class="input-group" id="otpGroup">
        <input type="text" id="otpCode" name="otp_code" placeholder="Enter 6‑digit code" autocomplete="one-time-code"  maxlength="6" />
        <i class="fas fa-shield-alt input-icon"></i>
      </div>
      <!-- write a function for check otp is letter or number  -->

      <div id="otpMessage" class="field-message" aria-live="polite"></div>

      <div class="otp-timer">
        <span id="timerDisplay" data-expiry="<?= (int)$otpExpiryTimestamp ?>">5:00</span>
        <a class="resend-link" id="resendBtn" href="otp-verify.php?action=resend">Resend code</a>
      </div>

      <button type="submit" name="verify" class="verify-btn">
        <span>Verify &amp; continue</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </form>
  </main>

  
  