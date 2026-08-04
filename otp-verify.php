<?php
require_once __DIR__ . '/libs/init.php';
?>

<?= loadTemplates("_head")?>

<body>
  <?= loadTemplates("_otpForm")?>

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