<?php
/*
|--------------------------------------------------------------------------
| RESET LINK SENT PAGE
|--------------------------------------------------------------------------
|
| Example:
|
| $email = $_SESSION['reset_email'] ?? '';
|
*/

$email = $email ?? 'nisathnisath606@gmail.com';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Link Sent | EliteFort</title>

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
            --text: #f8f7ff;
            --muted: #777281;
            --border: rgba(255,255,255,.085);
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
           PAGE WRAPPER
        ========================================================= */
        .auth {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 410px;
            text-align: center;
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
           SUCCESS ICON
        ========================================================= */
        .mail-status {
            position: relative;
            width: 74px;
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 27px;
            border-radius: 22px;
            color: #cbbaff;
            font-size: 29px;
            background: linear-gradient(145deg, rgba(139,92,246,.13), rgba(99,102,241,.055));
            border: 1px solid rgba(167,139,250,.17);
            box-shadow: 0 0 50px rgba(139,92,246,.09), inset 0 1px 0 rgba(255,255,255,.04);
        }

        .status-check {
            position: absolute;
            right: -4px;
            bottom: -4px;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #0d1712;
            border: 1px solid rgba(104,224,161,.25);
            color: var(--success);
            font-size: 12px;
            box-shadow: 0 0 18px rgba(104,224,161,.13);
        }

        /* =========================================================
           HEADING
        ========================================================= */
        .heading {
            margin-bottom: 27px;
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
            color: var(--muted);
            font-size: 10px;
            line-height: 1.8;
        }

        /* =========================================================
           EMAIL BOX
        ========================================================= */
        .email-box {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 19px;
            padding: 14px 15px;
            border-radius: 11px;
            border: 1px solid rgba(255,255,255,.07);
            background: rgba(255,255,255,.02);
            text-align: left;
        }

        .email-icon {
            flex-shrink: 0;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            color: #a88cff;
            background: rgba(139,92,246,.07);
            border: 1px solid rgba(139,92,246,.10);
            font-size: 14px;
        }

        .email-content {
            min-width: 0;
            flex: 1;
        }

        .email-label {
            display: block;
            margin-bottom: 4px;
            color: #625d69;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .email-address {
            display: block;
            overflow: hidden;
            color: #c0bac7;
            font-size: 9px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* =========================================================
           INFORMATION BOX
        ========================================================= */
        .notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 25px;
            padding: 13px 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.055);
            background: rgba(255,255,255,.015);
            color: #6b6572;
            font-size: 8px;
            line-height: 1.7;
            text-align: left;
        }

        .notice i {
            flex-shrink: 0;
            margin-top: 1px;
            color: #8066d7;
            font-size: 12px;
        }

        .notice strong {
            color: #98909f;
            font-weight: 500;
        }

        /* =========================================================
           OPEN EMAIL BUTTON
        ========================================================= */
        .primary-button {
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
            text-decoration: none;
            cursor: pointer;
            transition: .2s;
        }

        .primary-button:hover {
            background: #fff;
            transform: translateY(-1px);
            box-shadow: 0 14px 40px rgba(255,255,255,.09);
        }

        /* =========================================================
           RESEND
        ========================================================= */
        .resend {
            margin-top: 25px;
            color: #66616d;
            font-size: 9px;
        }

        .resend-button {
            margin-left: 3px;
            padding: 0;
            border: none;
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
            color: #514b59;
            cursor: not-allowed;
        }

        /* =========================================================
           BACK LOGIN
        ========================================================= */
        .back-login {
            margin-top: 20px;
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
           MOBILE
        ========================================================= */
        @media(max-width: 500px) {
            body {
                align-items: flex-start;
                padding: 48px 20px;
            }

            .heading h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <!-- BACKGROUND -->
    <div class="background">
        <div class="blur blur-one"></div>
        <div class="blur blur-two"></div>
        <div class="blur blur-three"></div>
        <div class="blur blur-four"></div>
    </div>

    <!-- PAGE -->
    <main class="auth">

        <!-- LOGO -->
        <div class="logo">
            <div class="logo-symbol">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div>

        <!-- STATUS ICON -->
        <div class="mail-status">
            <i class="bi bi-envelope-paper"></i>
            <div class="status-check">
                <i class="bi bi-check-lg"></i>
            </div>
        </div>

        <!-- HEADING -->
        <div class="heading">
            <h1>Check your <span>email</span></h1>
            <p>
                We've sent password reset instructions
                to the email address associated with
                your account.
            </p>
        </div>

        <!-- EMAIL -->
        <?php if (!empty($email)): ?>
            <div class="email-box">
                <div class="email-icon">
                    <i class="bi bi-envelope"></i>
                </div>
                <div class="email-content">
                    <span class="email-label">Reset link sent to</span>
                    <span class="email-address">
                        <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <!-- INFORMATION -->
        <div class="notice">
            <i class="bi bi-info-circle"></i>
            <span>
                Open the email and select the
                <strong>reset password link</strong>
                to create a new password.

                If you don't see the email,
                check your spam or junk folder.
            </span>
        </div>

        <!-- OPEN MAIL -->
        <a href="mailto:" class="primary-button">
            Open email app
            <i class="bi bi-box-arrow-up-right"></i>
        </a>

        <!-- RESEND -->
        <div class="resend">
            Didn't receive the email?
            <button type="button" class="resend-button" id="resendButton" disabled>
                Resend in 60s
            </button>
        </div>

        <!-- LOGIN -->
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
           RESEND COOLDOWN
        ========================================================= */
        const resendButton = document.getElementById('resendButton');
        let seconds = 60;

        const resendTimer = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(resendTimer);
                resendButton.disabled = false;
                resendButton.textContent = 'Resend link';
                return;
            }
            resendButton.textContent = `Resend in ${seconds}s`;
        }, 1000);

        /* =========================================================
           RESEND LINK
        ========================================================= */
        resendButton.addEventListener('click', () => {
            if (resendButton.disabled) {
                return;
            }
            // Example PHP route: forgot-password.php?action=resend
            window.location.href = 'forgot-password.php?action=resend';
        });
    </script>

</body>
</html>