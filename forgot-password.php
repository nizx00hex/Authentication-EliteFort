<?php
$error   = $error ?? '';
$success = $success ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | EliteFort</title>

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
            transform: translate(-50%, -50%);
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
           WRAPPER
        ========================================================= */
        .auth {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 400px;
        }

        /* =========================================================
           LOGO
        ========================================================= */
        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            margin-bottom: 34px;
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
           RECOVERY SYMBOL
        ========================================================= */
        .recovery-symbol {
            width: 43px;
            height: 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border-radius: 12px;
            background: rgba(139,92,246,.07);
            border: 1px solid rgba(139,92,246,.13);
            color: var(--purple-light);
            font-size: 18px;
            box-shadow: 0 0 30px rgba(139,92,246,.06);
        }

        /* =========================================================
           HEADING
        ========================================================= */
        .heading {
            margin-bottom: 32px;
            text-align: center;
        }

        .heading h1 {
            margin-bottom: 11px;
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

        /* =========================================================
           FIELD
        ========================================================= */
        .field {
            margin-bottom: 24px;
        }

        .field-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 9px;
        }

        .field-top label {
            color: #bbb6c4;
            font-size: 10px;
            font-weight: 500;
        }

        /* =========================================================
           INPUT
        ========================================================= */
        .input-container {
            position: relative;
        }

        .input {
            width: 100%;
            height: 56px;
            padding: 0 18px 0 52px;
            color: #f6f4fb;
            background: rgba(255,255,255,.025);
            border: 1px solid var(--border);
            border-radius: 11px;
            outline: none;
            font-family: "JetBrains Mono", monospace;
            font-size: 11px;
            font-weight: 400;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            transition: background .2s, border-color .2s, box-shadow .2s;
        }

        .input::placeholder {
            color: #4c4955;
        }

        .input:hover {
            border-color: var(--border-hover);
        }

        .input:focus {
            border-color: rgba(139,92,246,.72);
            background: rgba(139,92,246,.035);
            box-shadow: 0 0 0 3px rgba(139,92,246,.07), 0 0 35px rgba(139,92,246,.045);
        }

        /* =========================================================
           INPUT ICON
        ========================================================= */
        .input-icon {
            position: absolute;
            left: 17px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            pointer-events: none;
            color: #666170;
            font-size: 17px;
            transition: color .2s;
        }

        .input-container:focus-within .input-icon {
            color: var(--purple-light);
        }

        /* =========================================================
           SUBMIT BUTTON
        ========================================================= */
        .reset-button {
            width: 100%;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 10px;
            background: #f4f2fb;
            color: #08070c;
            font-family: "JetBrains Mono", monospace;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .4px;
            cursor: pointer;
            transition: background .2s, transform .2s, box-shadow .2s;
        }

        .reset-button i {
            font-size: 12px;
        }

        .reset-button:hover {
            background: white;
            transform: translateY(-1px);
            box-shadow: 0 14px 40px rgba(255,255,255,.09);
        }

        .reset-button:active {
            transform: translateY(0);
        }

        /* =========================================================
           HELP TEXT
        ========================================================= */
        .help {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            margin-top: 18px;
            padding: 12px 13px;
            border-radius: 9px;
            border: 1px solid rgba(255,255,255,.05);
            background: rgba(255,255,255,.015);
            color: #625d69;
            font-size: 8px;
            line-height: 1.7;
        }

        .help i {
            flex-shrink: 0;
            margin-top: 1px;
            color: #7862c6;
            font-size: 11px;
        }

        /* =========================================================
           BACK TO SIGN IN
        ========================================================= */
        .back-login {
            margin-top: 28px;
            text-align: center;
        }

        .back-login a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #746e7c;
            font-size: 9px;
            font-weight: 500;
            text-decoration: none;
            transition: color .2s;
        }

        .back-login a i {
            font-size: 11px;
        }

        .back-login a:hover {
            color: var(--purple-light);
        }

        /* =========================================================
           FOOTER
        ========================================================= */
        .footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-top: 44px;
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
                padding: 50px 20px;
            }

            .heading h1 {
                font-size: 24px;
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
         FORGOT PASSWORD
    ========================================================= -->
    <main class="auth">

        <!-- LOGO -->
        <div class="logo">
            <div class="logo-symbol">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div>

        <!-- RECOVERY ICON -->
        <div class="recovery-symbol">
            <i class="bi bi-key"></i>
        </div>

        <!-- HEADING -->
        <div class="heading">
            <h1>Forgot <span>password?</span></h1>
            <p>
                Enter the email address associated
                with your account and we'll help
                you reset your password.
            </p>
        </div>

        <!-- FORM -->
        <form method="POST" id="forgotPasswordForm">

            <div class="field">
                <div class="field-top">
                    <label for="email">Email address</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" id="email" class="input" placeholder="Enter your email" autocomplete="email" required>
                </div>
            </div>

            <!-- SUBMIT -->
            <button type="submit" name="forgot_password" class="reset-button">
                Send reset instructions
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <!-- SECURITY INFO -->
        <div class="help">
            <i class="bi bi-shield-lock"></i>
            <span>
                For your security, account recovery
                instructions are only sent to the email
                address associated with your account.
            </span>
        </div>

        <!-- BACK LOGIN -->
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
           REUSABLE TOAST
        ========================================================= */
        function showToast(type, message, title = null) {
            const container = document.getElementById('toastContainer');

            if (type !== 'success' && type !== 'error') {
                type = 'error';
            }

            if (!title) {
                title = type === 'success' ? 'Request received' : 'Unable to continue';
            }

            // Remove previous notification
            const existing = container.querySelector('.toast');
            if (existing) {
                existing.remove();
            }

            // Create notification
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

            // Show
            requestAnimationFrame(() => {
                toast.classList.add('show');
            });

            // Dismiss
            function dismissToast() {
                clearTimeout(dismissTimer);
                toast.classList.remove('show');
                toast.classList.add('closing');
                setTimeout(() => {
                    toast.remove();
                }, 280);
            }

            // Manual close
            closeButton.addEventListener('click', dismissToast);

            // Auto close after 6 seconds
            function startTimer() {
                dismissTimer = setTimeout(dismissToast, 6000);
            }
            startTimer();

            // Pause while hovering
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
            <?php if (!empty($error)): ?>
                showToast('error', <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Reset request failed');
            <?php elseif (!empty($success)): ?>
                showToast('success', <?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Check your email');
            <?php endif; ?>
        });
    </script>

</body>
</html>