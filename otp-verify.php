<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Matte Black · OTP Verification</title>

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
  />

  <style>
    :root {
      --matte-black: #171717;
      --deep-black: #0f0f0f;
      --charcoal: #2b2b2b;
      --dark-gray: #444444;
      --medium-gray: #666666;
      --soft-gray: #8b8b8b;
      --border-gray: rgba(60, 60, 60, 0.15);
      --page-background: #e9e9e9;
      --card-background: rgba(250, 250, 250, 0.92);
      --white: #ffffff;
      --error: #b42318;
      --success: #16794d;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      position: relative;
      overflow-x: hidden;
      background: var(--page-background);
      color: var(--matte-black);
      font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    body::before {
      content: '';
      position: fixed;
      top: -30%;
      right: -20%;
      width: 600px;
      height: 600px;
      background: radial-gradient(
        circle,
        rgba(23, 23, 23, 0.11) 0%,
        transparent 70%
      );
      pointer-events: none;
      z-index: 0;
    }

    body::after {
      content: '';
      position: fixed;
      bottom: -30%;
      left: -20%;
      width: 500px;
      height: 500px;
      background: radial-gradient(
        circle,
        rgba(43, 43, 43, 0.09) 0%,
        transparent 70%
      );
      pointer-events: none;
      z-index: 0;
    }

    .otp-card {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 460px;
      padding: 2.8rem 2.8rem 2.6rem;
      overflow: hidden;
      background: var(--card-background);
      border: 1px solid rgba(255, 255, 255, 0.58);
      border-radius: 32px;
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      box-shadow:
        0 20px 60px rgba(0, 0, 0, 0.05),
        0 8px 32px rgba(0, 0, 0, 0.025),
        inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    .otp-card::before {
      content: '';
      position: absolute;
      top: -85px;
      right: -85px;
      width: 170px;
      height: 170px;
      border-radius: 50%;
      background: rgba(23, 23, 23, 0.035);
      pointer-events: none;
    }

    .corner-deco {
      position: absolute;
      top: -1px;
      right: 42px;
      width: 82px;
      height: 4px;
      border-radius: 0 0 4px 4px;
      opacity: 0.5;
      background: linear-gradient(
        90deg,
        var(--matte-black),
        var(--dark-gray),
        var(--matte-black)
      );
    }

    .card-header {
      position: relative;
      z-index: 1;
      margin-bottom: 2rem;
      text-align: center;
    }

    .card-header h1 {
      margin-bottom: 0.35rem;
      color: var(--matte-black);
      font-size: 1.85rem;
      font-weight: 650;
      letter-spacing: -0.03em;
    }

    .card-header p {
      color: var(--medium-gray);
      font-size: 0.94rem;
      line-height: 1.55;
    }

    .otp-form {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }

    .input-group {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      z-index: 2;
      transform: translateY(-50%);
      color: var(--soft-gray);
      font-size: 0.9rem;
      pointer-events: none;
      transition: color 0.22s ease;
    }

    .input-group input {
      width: 100%;
      padding: 0.9rem 3rem 0.9rem 3rem;
      color: var(--matte-black);
      background: rgba(250, 250, 250, 0.68);
      border: 1.5px solid var(--border-gray);
      border-radius: 60px;
      outline: none;
      font-family: inherit;
      font-size: 0.93rem;
      font-weight: 450;
      backdrop-filter: blur(4px);
      transition:
        border-color 0.22s ease,
        background 0.22s ease,
        box-shadow 0.22s ease;
    }

    .input-group input::placeholder {
      color: var(--soft-gray);
      font-weight: 400;
    }

    .input-group input:focus {
      border-color: var(--matte-black);
      background: rgba(255, 255, 255, 0.96);
      box-shadow:
        0 0 0 6px rgba(23, 23, 23, 0.075),
        inset 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .input-group:focus-within .input-icon {
      color: var(--matte-black);
    }

    .otp-timer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 0.2rem;
      padding: 0 0.2rem;
      font-size: 0.82rem;
      color: var(--medium-gray);
    }

    .otp-timer .resend-link {
      color: var(--matte-black);
      font-weight: 600;
      background: transparent;
      border: none;
      cursor: pointer;
      border-bottom: 1.5px solid rgba(23, 23, 23, 0.25);
      transition: border-color 0.15s ease;
      padding: 0;
      font-family: inherit;
      font-size: 0.82rem;
    }

    .otp-timer .resend-link:hover {
      border-bottom-color: var(--matte-black);
    }

    .otp-timer .resend-link:disabled {
      opacity: 0.4;
      cursor: not-allowed;
      border-bottom-color: transparent;
    }

    .field-message {
      display: none;
      margin: -0.4rem 0 0 0.6rem;
      font-size: 0.74rem;
      line-height: 1.4;
    }

    .field-message.error {
      display: block;
      color: var(--error);
    }

    .field-message.success {
      display: block;
      color: var(--success);
    }

    .input-group.has-error input {
      border-color: var(--error);
      box-shadow: 0 0 0 5px rgba(180, 35, 24, 0.08);
    }

    .verify-btn {
      position: relative;
      overflow: hidden;
      width: 100%;
      margin-top: 0.4rem;
      padding: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.7rem;
      color: var(--white);
      background: linear-gradient(135deg, var(--matte-black), var(--charcoal));
      border: none;
      border-radius: 60px;
      box-shadow: 0 4px 20px rgba(23, 23, 23, 0.2);
      font-family: inherit;
      font-size: 0.95rem;
      font-weight: 650;
      letter-spacing: 0.3px;
      cursor: pointer;
      transition: all 0.25s ease;
    }

    .verify-btn::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.12),
        transparent 55%
      );
      pointer-events: none;
    }

    .verify-btn:hover {
      transform: translateY(-2px);
      background: linear-gradient(135deg, var(--charcoal), var(--deep-black));
      box-shadow: 0 8px 32px rgba(23, 23, 23, 0.24);
    }

    .verify-btn:active {
      transform: scale(0.97);
    }

    .verify-btn i {
      font-size: 0.85rem;
      transition: transform 0.25s ease;
    }

    .verify-btn:hover i {
      transform: translateX(4px);
    }

    .divider {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      gap: 1.1rem;
      margin: 1.35rem 0 1rem;
      color: var(--soft-gray);
      font-size: 0.68rem;
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(60, 60, 60, 0.18);
    }

    .social-row {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: center;
      gap: 0.8rem;
    }

    .social-row a {
      flex: 1;
      max-width: 150px;
      padding: 0.72rem 0.55rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      color: #222222;
      background: rgba(250, 250, 250, 0.74);
      border: 1.5px solid rgba(60, 60, 60, 0.14);
      border-radius: 40px;
      font-size: 0.8rem;
      font-weight: 550;
      text-decoration: none;
      backdrop-filter: blur(4px);
      transition: all 0.2s ease;
    }

    .social-row a:hover {
      color: var(--white);
      background: var(--matte-black);
      border-color: var(--matte-black);
      transform: translateY(-2px);
      box-shadow: 0 5px 16px rgba(23, 23, 23, 0.14);
    }

    .social-row a i {
      font-size: 1rem;
    }

    .card-footer {
      position: relative;
      z-index: 1;
      margin-top: 1.6rem;
      padding-top: 1.5rem;
      color: var(--medium-gray);
      border-top: 1px solid rgba(60, 60, 60, 0.14);
      text-align: center;
      font-size: 0.84rem;
    }

    .card-footer a {
      color: var(--matte-black);
      font-weight: 650;
      text-decoration: none;
      border-bottom: 1.5px solid transparent;
      transition: border-color 0.15s ease;
    }

    .card-footer a:hover {
      border-bottom-color: rgba(23, 23, 23, 0.35);
    }

    @media (max-width: 480px) {
      body {
        padding: 1rem;
        align-items: flex-start;
      }

      .otp-card {
        margin: 1rem 0;
        padding: 2rem 1.35rem 2rem;
        border-radius: 24px;
      }

      .card-header {
        margin-bottom: 1.6rem;
      }

      .card-header h1 {
        font-size: 1.55rem;
      }

      .input-group input {
        padding: 0.82rem 2.8rem 0.82rem 2.75rem;
        font-size: 0.88rem;
      }

      .input-icon {
        left: 14px;
      }

      .social-row a {
        max-width: none;
        font-size: 0.74rem;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      *,
      *::before,
      *::after {
        scroll-behavior: auto !important;
        transition: none !important;
      }
    }
  </style>
</head>

<body>
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
        <input
          type="text"
          id="otpCode"
          name="otp_code"
          placeholder="Enter 6‑digit code"
          autocomplete="one-time-code"
          inputmode="numeric"
          pattern="[0-9]{6}"
          maxlength="6"
          required
        />
        <i class="fas fa-shield-alt input-icon"></i>
      </div>

      <div id="otpMessage" class="field-message" aria-live="polite"></div>

      <div class="otp-timer">
        <span id="timerDisplay">2:00</span>
        <button type="button" class="resend-link" id="resendBtn">Resend code</button>
      </div>

      <button type="submit" class="verify-btn">
        <span>Verify &amp; continue</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <div class="divider">or continue with</div>

    <div class="social-row">
      <a href="#" aria-label="Continue with Google">
        <i class="fab fa-google"></i>
        Google
      </a>

      <a href="#" aria-label="Continue with Apple">
        <i class="fab fa-apple"></i>
        Apple
      </a>
    </div>

    <footer class="card-footer">
      Didn't receive the code? <a href="#" id="emailChangeLink">Change email</a>
    </footer>
  </main>

  <script>
    (function() {
      const otpForm = document.getElementById('otpForm');
      const otpInput = document.getElementById('otpCode');
      const otpGroup = document.getElementById('otpGroup');
      const otpMessage = document.getElementById('otpMessage');
      const resendBtn = document.getElementById('resendBtn');
      const timerDisplay = document.getElementById('timerDisplay');
      const emailChangeLink = document.getElementById('emailChangeLink');

      let timerInterval = null;
      let timeSeconds = 120; // 2 minutes
      let isResendCooldown = false;

      // ----- utility: format mm:ss -----
      function formatTime(sec) {
        const mins = Math.floor(sec / 60);
        const remainder = sec % 60;
        return `${mins}:${remainder.toString().padStart(2, '0')}`;
      }

      // ----- update timer UI and button state -----
      function updateTimerDisplay() {
        timerDisplay.textContent = formatTime(timeSeconds);
        if (timeSeconds <= 0) {
          clearInterval(timerInterval);
          timerInterval = null;
          resendBtn.disabled = false;
          isResendCooldown = false;
          timerDisplay.textContent = '0:00';
        } else {
          resendBtn.disabled = true;
          isResendCooldown = true;
        }
      }

      // ----- start countdown from given seconds -----
      function startTimer(seconds) {
        if (timerInterval) {
          clearInterval(timerInterval);
          timerInterval = null;
        }
        timeSeconds = seconds;
        updateTimerDisplay();

        timerInterval = setInterval(() => {
          timeSeconds -= 1;
          if (timeSeconds < 0) timeSeconds = 0;
          updateTimerDisplay();
          if (timeSeconds === 0) {
            clearInterval(timerInterval);
            timerInterval = null;
          }
        }, 1000);
      }

      // ----- reset cooldown and restart timer (simulate resend) -----
      function handleResend() {
        if (isResendCooldown) return;

        // Simulate OTP resend: show message, restart timer
        otpMessage.className = 'field-message success';
        otpMessage.textContent = '✨ New OTP sent to your email.';
        otpGroup.classList.remove('has-error');

        // Reset OTP field and clear any error style
        otpInput.value = '';
        otpInput.focus();

        // Restart timer
        startTimer(120);
      }

      // ----- validate OTP (6 digits) -----
      function validateOtp() {
        const value = otpInput.value.trim();
        const isValid = /^[0-9]{6}$/.test(value);

        otpGroup.classList.remove('has-error');
        otpMessage.className = 'field-message';

        if (!isValid && value.length > 0) {
          otpGroup.classList.add('has-error');
          otpMessage.classList.add('error');
          otpMessage.textContent = 'Please enter a valid 6‑digit numeric code.';
          return false;
        } else if (isValid) {
          // optional success feedback (but we don't persist)
          otpMessage.classList.add('success');
          otpMessage.textContent = '✓ Code format looks good.';
          // remove success after short delay? keep it subtle
        } else {
          // empty field – clear message
          otpMessage.textContent = '';
          otpMessage.className = 'field-message';
        }
        return isValid;
      }

      // ----- real‑time validation on input -----
      otpInput.addEventListener('input', function(e) {
        // allow only digits
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
        validateOtp();
      });

      // ----- form submit -----
      otpForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const isValid = validateOtp();

        if (!isValid) {
          // If empty or invalid, show error
          if (otpInput.value.trim().length === 0) {
            otpGroup.classList.add('has-error');
            otpMessage.className = 'field-message error';
            otpMessage.textContent = 'Please enter the 6‑digit code.';
          }
          return;
        }

        // Simulate verification success
        otpMessage.className = 'field-message success';
        otpMessage.textContent = '✅ OTP verified successfully! Redirecting…';
        otpGroup.classList.remove('has-error');

        // Disable button briefly to prevent double submit
        const btn = this.querySelector('.verify-btn');
        btn.disabled = true;
        btn.innerHTML = '<span>Verifying…</span><i class="fas fa-spinner fa-pulse"></i>';

        // Simulate async verification
        setTimeout(() => {
          btn.disabled = false;
          btn.innerHTML = '<span>Verify &amp; continue</span><i class="fas fa-arrow-right"></i>';
          // In a real app, you would redirect or proceed.
          alert('OTP verified (demo). Proceed to dashboard.');
        }, 1200);
      });

      // ----- resend button -----
      resendBtn.addEventListener('click', handleResend);

      // ----- "Change email" link (demo) -----
      emailChangeLink.addEventListener('click', function(e) {
        e.preventDefault();
        alert('In a real app, you would be redirected to change the email address.\n(Demo action)');
      });

      // ----- start timer on page load -----
      startTimer(120);

      // ----- (optional) clear timer on page unload -----
      window.addEventListener('beforeunload', function() {
        if (timerInterval) {
          clearInterval(timerInterval);
          timerInterval = null;
        }
      });

    })();
  </script>
</body>
</html>