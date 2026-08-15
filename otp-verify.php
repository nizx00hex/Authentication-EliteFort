<?php
include "_core/__init__.php";

$error   = $error ?? '';
$success = $success ?? '';
?>

<?= loadTemplates('_head'); ?>


<body>

    <!-- =========================================================
         BACKGROUND
    ========================================================= -->
    <div class="background">
        <div class="blur blur-one"></div>
        <div class="blur blur-two"></div>
        <div class="blur blur-three"></div>
        <div class="blur blur-four"></div>
    </div>

    <!-- =========================================================
         NOTIFICATION
    ========================================================= -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- =========================================================
         OTP VERIFICATION
    ========================================================= -->
    <main class="auth">


        <!-- LOGO -->
        <!-- <div class="logo">
            <div class="logo-symbol">
                 <img src="assets/images/elitefort.png" alt="EliteFort Logo" style="max-height: 60px; width: auto; height: auto;">
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div> -->

        <!-- SMALL VERIFY ICON -->
        <div class="verify-symbol">
            <i class="bi bi-envelope-check"></i>
        </div>

        <!-- HEADING -->
        <div class="heading">
            <h1>Verify <span>email</span></h1>
            <p>
                We sent a 6-digit verification code to
                <span class="email">your email address</span>
            </p>
        </div>

        <!-- VERIFY FORM -->
        <form method="POST" id="otpForm">

            <label class="otp-label">Verification code</label>

            <!-- This is the field PHP receives: $_POST['otp'] -->
            <input type="hidden" name="otp" id="otpValue">

            <div class="otp-group" id="otpGroup">
                <input type="text" class="otp-input" inputmode="numeric" autocomplete="one-time-code" maxlength="1" pattern="[0-9]" aria-label="OTP digit 1">
                <input type="text" class="otp-input" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="OTP digit 2">
                <input type="text" class="otp-input" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="OTP digit 3">
                <input type="text" class="otp-input" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="OTP digit 4">
                <input type="text" class="otp-input" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="OTP digit 5">
                <input type="text" class="otp-input" inputmode="numeric" maxlength="1" pattern="[0-9]" aria-label="OTP digit 6">
            </div>

            <!-- TIMER -->
            <div class="otp-meta">
                <div class="expiry">
                    <i class="bi bi-clock"></i>
                    <span>
                        Code expires in
                        <strong id="expiryTimer">05:00</strong>
                    </span>
                </div>
            </div>

            <!-- VERIFY -->
            <button type="submit" name="verify" class="verify-button">Verify account</button>
        </form>

        <!-- RESEND -->
        <div class="resend">
            Didn't receive the code?
            <button type="button" class="resend-button" id="resendButton" disabled>
                Resend in 60s
            </button>
        </div>

        <!-- BACK -->
        <div class="back-login">
            <a href="login.php">
                <i class="bi bi-arrow-left"></i>
                Back to sign in
            </a>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <span>EliteFort</span>
            <span class="footer-dot"></span>
            <span>Authentication</span>
        </div>

    </main>

    <script>
        /* =========================================================
           OTP INPUTS
        ========================================================= */
        const otpInputs = Array.from(document.querySelectorAll('.otp-input'));
        const otpValue = document.getElementById('otpValue');
        const otpForm = document.getElementById('otpForm');

        function updateOtpValue() {
            const value = otpInputs.map(input => input.value).join('');
            otpValue.value = value;
            otpInputs.forEach(input => {
                input.classList.toggle('filled', input.value !== '');
            });
        }

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', event => {
                // Numbers only
                input.value = input.value.replace(/\D/g, '');
                if (input.value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                updateOtpValue();
            });

            // Backspace moves backward
            input.addEventListener('keydown', event => {
                if (event.key === 'Backspace' && input.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                }
                if (event.key === 'ArrowLeft' && index > 0) {
                    event.preventDefault();
                    otpInputs[index - 1].focus();
                }
                if (event.key === 'ArrowRight' && index < otpInputs.length - 1) {
                    event.preventDefault();
                    otpInputs[index + 1].focus();
                }
            });
        });

        /* =========================================================
           OTP PASTE
        ========================================================= */
        document.getElementById('otpGroup').addEventListener('paste', event => {
            event.preventDefault();
            const pasted = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            pasted.split('').forEach((number, index) => {
                if (otpInputs[index]) {
                    otpInputs[index].value = number;
                }
            });
            updateOtpValue();
            const focusIndex = Math.min(pasted.length, 5);
            otpInputs[focusIndex].focus();
        });

        /* =========================================================
           VERIFY FORM
        ========================================================= */
        otpForm.addEventListener('submit', event => {
            updateOtpValue();
            if (otpValue.value.length !== 6) {
                event.preventDefault();
                showToast('error', 'Enter the complete 6-digit verification code.', 'Invalid verification code');
                const firstEmpty = otpInputs.find(input => input.value === '');
                if (firstEmpty) {
                    firstEmpty.focus();
                }
            }
        });

        /* =========================================================
           EXPIRY TIMER - 5 MINUTES
        ========================================================= */
        const expiryTimer = document.getElementById('expiryTimer');
        let expirySeconds = 5 * 60;

        function updateExpiryTimer() {
            const minutes = Math.floor(expirySeconds / 60);
            const seconds = expirySeconds % 60;
            expiryTimer.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
            if (expirySeconds <= 0) {
                clearInterval(expiryInterval);
                expiryTimer.textContent = 'Expired';
                expiryTimer.style.color = '#ff8799';
                showToast('error', 'Your verification code has expired. Request a new code.', 'Code expired');
                return;
            }
            expirySeconds--;
        }

        updateExpiryTimer();
        const expiryInterval = setInterval(updateExpiryTimer, 1000);

        /* =========================================================
           RESEND COOLDOWN
        ========================================================= */
        const resendButton = document.getElementById('resendButton');
        let resendSeconds = 60;

        const resendInterval = setInterval(() => {
            resendSeconds--;
            if (resendSeconds <= 0) {
                clearInterval(resendInterval);
                resendButton.disabled = false;
                resendButton.textContent = 'Resend code';
                return;
            }
            resendButton.textContent = `Resend in ${resendSeconds}s`;
        }, 1000);

        resendButton.addEventListener('click', () => {
            // Point this to your PHP resend action
            window.location.href = 'otp-verify.php?action=resend';
        });

        /* =========================================================
           TOAST
        ========================================================= */
        function showToast(type, message, title = null) {
            const container = document.getElementById('toastContainer');

            if (type !== 'success' && type !== 'error') {
                type = 'error';
            }

            if (!title) {
                title = type === 'success' ? 'Completed' : 'Unable to continue';
            }

            const existing = container.querySelector('.toast');
            if (existing) {
                existing.remove();
            }

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const iconClass = type === 'success' ? 'bi-check-lg' : 'bi-exclamation-lg';
            const state = type === 'success' ? 'Success' : 'Error';

            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="bi ${iconClass}"></i>
                </div>
                <div class="toast-content">
                    <span class="toast-state">${state}</span>
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button type="button" class="toast-close" aria-label="Dismiss notification">
                    <span>Dismiss</span>
                    <i class="bi bi-x-lg"></i>
                </button>
            `;

            container.appendChild(toast);

            const closeButton = toast.querySelector('.toast-close');
            let dismissTimer;

            requestAnimationFrame(() => {
                toast.classList.add('show');
            });

            function dismissToast() {
                clearTimeout(dismissTimer);
                toast.classList.remove('show');
                toast.classList.add('closing');
                setTimeout(() => {
                    toast.remove();
                }, 280);
            }

            closeButton.addEventListener('click', dismissToast);

            function startTimer() {
                dismissTimer = setTimeout(dismissToast, 6000);
            }
            startTimer();

            toast.addEventListener('mouseenter', () => {
                clearTimeout(dismissTimer);
            });
            toast.addEventListener('mouseleave', () => {
                clearTimeout(dismissTimer);
                startTimer();
            });
        }

        /* =========================================================
           PHP ERROR / SUCCESS
        ========================================================= */
        document.addEventListener('DOMContentLoaded', () => {
            otpInputs[0].focus();

            <?php if (!empty($error)): ?>
                showToast('error', <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Verification failed');
            <?php elseif (!empty($success)): ?>
                showToast('success', <?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'OTP sent');
            <?php endif; ?>
        });
    </script>

</body>
</html>