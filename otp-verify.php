<?php
$error   = $error ?? '';
$success = $success ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | EliteFort</title>

    <!-- JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* =========================================================
           RESET & ROOT VARIABLES
        ========================================================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --background: #05050a;
            --purple: #8b5cf6;
            --purple-light: #a78bfa;
            --blue: #6366f1;
            --text: #f8f7ff;
            --muted: #777281;
            --border: rgba(255,255,255,.085);
            --border-hover: rgba(255,255,255,.16);
            --danger: #ff647c;
            --success: #68e0a1;
        }

        html {
            color-scheme: dark;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 34px 24px;
            background: var(--background);
            color: var(--text);
            font-family: "JetBrains Mono", monospace;
            overflow-x: hidden;
        }

        /* =========================================================
           BACKGROUND
        ========================================================= */
        .background {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .blur {
            position: absolute;
            border-radius: 50%;
        }

        .blur-one {
            width: 650px;
            height: 650px;
            top: -330px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(123,79,255,.20);
            filter: blur(160px);
        }

        .blur-two {
            width: 450px;
            height: 450px;
            top: 38%;
            left: -220px;
            background: rgba(139,92,246,.11);
            filter: blur(135px);
        }

        .blur-three {
            width: 470px;
            height: 470px;
            right: -220px;
            bottom: -130px;
            background: rgba(89,79,255,.10);
            filter: blur(145px);
        }

        .blur-four {
            width: 320px;
            height: 320px;
            top: 48%;
            left: 50%;
            transform: translate(-50%,-50%);
            background: rgba(175,120,255,.07);
            filter: blur(110px);
        }

        .background::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: .13;
            mask-image: radial-gradient(circle at center, black, transparent 78%);
        }

        /* =========================================================
           AUTH
        ========================================================= */
        .auth {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 430px;
        }

        /* =========================================================
           LOGO
        ========================================================= */
        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            margin-bottom: 33px;
        }

        .logo-symbol {
            position: relative;
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            background: linear-gradient(145deg, #a17cff, #6640db);
            box-shadow: 0 15px 45px rgba(122,82,255,.30), inset 0 1px 0 rgba(255,255,255,.22);
        }

        .logo-symbol::before {
            content: "";
            position: absolute;
            inset: -5px;
            border-radius: 19px;
            border: 1px solid rgba(139,92,246,.15);
        }

        .logo-symbol i {
            color: white;
            font-size: 24px;
        }

        .brand-name {
            color: #aaa4b7;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 4px;
        }

        /* =========================================================
           HEADING
        ========================================================= */
        .heading {
            margin-bottom: 31px;
            text-align: center;
        }

        .heading h1 {
            margin-bottom: 10px;
            font-size: 27px;
            font-weight: 700;
            letter-spacing: -1.3px;
        }

        .heading h1 span {
            color: var(--purple-light);
        }

        .heading p {
            max-width: 360px;
            margin: 0 auto;
            color: var(--muted);
            font-size: 10px;
            line-height: 1.8;
        }

        .heading .email {
            color: #aaa4b7;
            font-weight: 500;
        }

        /* =========================================================
           OTP ICON
        ========================================================= */
        .verify-symbol {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border-radius: 12px;
            color: var(--purple-light);
            font-size: 18px;
            background: rgba(139,92,246,.07);
            border: 1px solid rgba(139,92,246,.12);
            box-shadow: 0 0 30px rgba(139,92,246,.06);
        }

        /* =========================================================
           OTP FORM
        ========================================================= */
        .otp-label {
            display: block;
            margin-bottom: 10px;
            color: #bbb6c4;
            font-size: 10px;
            font-weight: 500;
        }

        .otp-group {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 9px;
            margin-bottom: 18px;
        }

        .otp-input {
            width: 100%;
            height: 58px;
            padding: 0;
            border-radius: 11px;
            border: 1px solid var(--border);
            outline: none;
            color: #f8f7ff;
            background: rgba(255,255,255,.025);
            font-family: "JetBrains Mono", monospace;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            caret-color: var(--purple-light);
            transition: background .2s, border-color .2s, box-shadow .2s, transform .2s;
        }

        .otp-input:hover {
            border-color: var(--border-hover);
        }

        .otp-input:focus {
            border-color: rgba(139,92,246,.72);
            background: rgba(139,92,246,.045);
            box-shadow: 0 0 0 3px rgba(139,92,246,.07), 0 0 30px rgba(139,92,246,.045);
            transform: translateY(-1px);
        }

        .otp-input.filled {
            border-color: rgba(167,139,250,.30);
            background: rgba(139,92,246,.035);
            color: #d9ccff;
        }

        /* =========================================================
           TIMER INFO
        ========================================================= */
        .otp-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 24px;
        }

        .expiry {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #625d6b;
            font-size: 8px;
        }

        .expiry i {
            color: #7862c6;
            font-size: 11px;
        }

        .expiry strong {
            color: #9e91c3;
            font-weight: 600;
        }

        /* =========================================================
           VERIFY BUTTON
        ========================================================= */
        .verify-button {
            width: 100%;
            height: 54px;
            border: none;
            border-radius: 10px;
            background: #f4f2fb;
            color: #08070c;
            font-family: "JetBrains Mono", monospace;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .5px;
            cursor: pointer;
            transition: .2s;
        }

        .verify-button:hover {
            background: white;
            transform: translateY(-1px);
            box-shadow: 0 14px 40px rgba(255,255,255,.09);
        }

        .verify-button:active {
            transform: translateY(0);
        }

        /* =========================================================
           RESEND
        ========================================================= */
        .resend {
            margin-top: 26px;
            text-align: center;
            color: #66616d;
            font-size: 9px;
        }

        .resend-button {
            margin-left: 4px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #a88cff;
            font-family: "JetBrains Mono", monospace;
            font-size: 9px;
            font-weight: 600;
            cursor: pointer;
        }

        .resend-button:hover:not(:disabled) {
            color: #c9b9ff;
        }

        .resend-button:disabled {
            color: #524c5d;
            cursor: not-allowed;
        }

        /* =========================================================
           BACK TO LOGIN
        ========================================================= */
        .back-login {
            margin-top: 18px;
            text-align: center;
        }

        .back-login a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #5f5968;
            font-size: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: color .2s;
        }

        .back-login a:hover {
            color: #aaa4b7;
        }

        /* =========================================================
           FOOTER
        ========================================================= */
        .footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-top: 43px;
            color: #3d3944;
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .footer-dot {
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: #674bb8;
        }

        /* =========================================================
           TOP RIGHT NOTIFICATION
        ========================================================= */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 10000;
            width: calc(100% - 48px);
            max-width: 400px;
            pointer-events: none;
        }

        .toast {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 13px;
            width: 100%;
            padding: 16px;
            border-radius: 13px;
            background: linear-gradient(145deg, rgba(17,16,23,.97), rgba(8,8,12,.98));
            border: 1px solid rgba(255,255,255,.085);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow: 0 24px 70px rgba(0,0,0,.55), inset 0 1px 0 rgba(255,255,255,.025);
            opacity: 0;
            transform: translateX(42px) scale(.985);
            transition: opacity .28s ease, transform .28s cubic-bezier(.2,.8,.2,1);
            pointer-events: auto;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0) scale(1);
        }

        .toast.closing {
            opacity: 0;
            transform: translateX(32px) scale(.985);
        }

        .toast::before {
            content: "";
            position: absolute;
            top: 13px;
            bottom: 13px;
            left: 0;
            width: 3px;
            border-radius: 0 8px 8px 0;
        }

        .toast.error::before {
            background: var(--danger);
            box-shadow: 0 0 14px rgba(255,100,124,.35);
        }

        .toast.success::before {
            background: var(--success);
            box-shadow: 0 0 14px rgba(104,224,161,.30);
        }

        .toast-icon {
            flex-shrink: 0;
            width: 39px;
            height: 39px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 16px;
        }

        .toast.error .toast-icon {
            color: #ff8799;
            background: rgba(255,100,124,.08);
            border: 1px solid rgba(255,100,124,.13);
        }

        .toast.success .toast-icon {
            color: #7ee2ad;
            background: rgba(104,224,161,.08);
            border: 1px solid rgba(104,224,161,.13);
        }

        .toast-content {
            flex: 1;
            min-width: 0;
            padding-top: 1px;
        }

        .toast-state {
            display: block;
            margin-bottom: 4px;
            color: #625d69;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 1.3px;
            text-transform: uppercase;
        }

        .toast-title {
            margin-bottom: 5px;
            color: #f8f7ff;
            font-size: 11px;
            font-weight: 600;
        }

        .toast-message {
            color: #898390;
            font-size: 9px;
            line-height: 1.6;
            word-break: break-word;
        }

        .toast-close {
            flex-shrink: 0;
            height: 29px;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 0 9px;
            border-radius: 7px;
            border: 1px solid rgba(255,255,255,.07);
            background: rgba(255,255,255,.025);
            color: #77717e;
            font-family: "JetBrains Mono", monospace;
            font-size: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: .2s;
        }

        .toast-close:hover {
            color: #f8f7ff;
            background: rgba(255,255,255,.06);
            border-color: rgba(255,255,255,.12);
        }

        /* =========================================================
           MOBILE
        ========================================================= */
        @media(max-width: 500px) {
            body {
                align-items: flex-start;
                padding: 45px 18px;
            }

            .auth {
                max-width: 420px;
            }

            .heading h1 {
                font-size: 24px;
            }

            .otp-group {
                gap: 6px;
            }

            .otp-input {
                height: 53px;
                font-size: 16px;
            }

            .toast-container {
                top: 14px;
                left: 14px;
                right: 14px;
                width: auto;
                max-width: none;
            }

            .toast-close span {
                display: none;
            }

            .toast-close {
                width: 29px;
                padding: 0;
                justify-content: center;
            }
        }
    </style>
</head>

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
        <div class="logo">
            <div class="logo-symbol">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div>

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