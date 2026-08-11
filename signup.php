<?php
$error   = $error ?? '';
$success = $success ?? 'fdsfsf';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | EliteFort</title>

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
            --border: rgba(255, 255, 255, .085);
            --border-hover: rgba(255, 255, 255, .16);
            --danger: #ff647c;
            --success: #68e0a1;
            --github-mono: ui-monospace, SFMono-Regular, "SF Mono", Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
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
            font-family: var(--github-mono);
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
            background: rgba(123, 79, 255, .20);
            filter: blur(160px);
        }

        .blur-two {
            width: 450px;
            height: 450px;
            top: 38%;
            left: -220px;
            background: rgba(139, 92, 246, .11);
            filter: blur(135px);
        }

        .blur-three {
            width: 470px;
            height: 470px;
            right: -220px;
            bottom: -130px;
            background: rgba(89, 79, 255, .10);
            filter: blur(145px);
        }

        .blur-four {
            width: 320px;
            height: 320px;
            top: 48%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(175, 120, 255, .07);
            filter: blur(110px);
        }

        .background::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, .05) 1px, transparent 1px);
            background-size: 28px 28px;
            opacity: .13;
            mask-image: radial-gradient(circle at center, black, transparent 78%);
        }

        /* =========================================================
           AUTH WRAPPER
        ========================================================= */
        .auth {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
        }

        /* =========================================================
           LOGO
        ========================================================= */
        .logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            margin-bottom: 32px;
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
            box-shadow: 0 15px 45px rgba(122, 82, 255, .30), inset 0 1px 0 rgba(255, 255, 255, .22);
        }

        .logo-symbol::before {
            content: "";
            position: absolute;
            inset: -5px;
            border-radius: 19px;
            border: 1px solid rgba(139, 92, 246, .15);
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
            margin-bottom: 32px;
            text-align: center;
        }

        .heading h1 {
            margin-bottom: 10px;
            color: var(--text);
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
            line-height: 1.7;
        }

        /* =========================================================
           FORM
        ========================================================= */
        .field {
            margin-bottom: 19px;
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
            font-weight: 600;
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
            padding: 0 50px 0 52px;
            color: #f6f4fb;
            background: rgba(255, 255, 255, .025);
            border: 1px solid var(--border);
            border-radius: 11px;
            outline: none;
            font-family: var(--github-mono);
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
            border-color: rgba(139, 92, 246, .72);
            background: rgba(139, 92, 246, .035);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, .07), 0 0 35px rgba(139, 92, 246, .045);
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
            color: #666170;
            font-size: 17px;
            pointer-events: none;
            transition: color .2s;
        }

        .input-container:focus-within .input-icon {
            color: var(--purple-light);
        }

        /* =========================================================
           PASSWORD EYE
        ========================================================= */
        .eye {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #666170;
            font-size: 16px;
            cursor: pointer;
            transition: color .2s, background .2s;
        }

        .eye:hover {
            color: var(--purple-light);
            background: rgba(139, 92, 246, .06);
        }

        /* =========================================================
           TERMS
        ========================================================= */
        .terms {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            margin-top: 5px;
            margin-bottom: 26px;
            color: #716c79;
            font-size: 9px;
            line-height: 1.8;
        }

        .terms input {
            appearance: none;
            flex-shrink: 0;
            position: relative;
            width: 16px;
            height: 16px;
            margin-top: 1px;
            border-radius: 4px;
            border: 1px solid rgba(255, 255, 255, .13);
            background: rgba(255, 255, 255, .03);
            cursor: pointer;
        }

        .terms input:hover {
            border-color: rgba(167, 139, 250, .45);
        }

        .terms input:checked {
            background: var(--purple);
            border-color: var(--purple);
        }

        .terms input:checked::after {
            content: "";
            position: absolute;
            left: 5px;
            top: 2px;
            width: 3px;
            height: 7px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .policy-link {
            padding: 0;
            border: 0;
            background: transparent;
            color: #a88cff;
            font-family: var(--github-mono);
            font-size: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .policy-link:hover {
            color: #c9b9ff;
        }

        /* =========================================================
           CREATE BUTTON
        ========================================================= */
        .create-button {
            width: 100%;
            height: 54px;
            border: none;
            border-radius: 10px;
            background: #f4f2fb;
            color: #08070c;
            font-family: var(--github-mono);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .4px;
            cursor: pointer;
            transition: background .2s, transform .2s, box-shadow .2s;
        }

        .create-button:hover {
            background: white;
            transform: translateY(-1px);
            box-shadow: 0 14px 40px rgba(255, 255, 255, .09);
        }

        .create-button:active {
            transform: translateY(0);
        }

        /* =========================================================
           LOGIN LINK
        ========================================================= */
        .login-link {
            margin-top: 28px;
            color: #66616d;
            font-size: 9px;
            text-align: center;
        }

        .login-link a {
            margin-left: 3px;
            color: #a88cff;
            font-weight: 600;
            text-decoration: none;
        }

        .login-link a:hover {
            color: #c9b9ff;
        }

        /* =========================================================
           FOOTER
        ========================================================= */
        .footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin-top: 45px;
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
            padding: 16px 15px;
            border-radius: 13px;
            background: linear-gradient(145deg, rgba(17, 16, 23, .97), rgba(8, 8, 12, .98));
            border: 1px solid rgba(255, 255, 255, .085);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow: 0 24px 70px rgba(0, 0, 0, .55), inset 0 1px 0 rgba(255, 255, 255, .025);
            opacity: 0;
            transform: translateX(42px) scale(.985);
            transition: opacity .28s ease, transform .28s cubic-bezier(.2, .8, .2, 1);
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
            left: 0;
            top: 13px;
            bottom: 13px;
            width: 3px;
            border-radius: 0 8px 8px 0;
        }

        .toast.error::before {
            background: var(--danger);
            box-shadow: 0 0 14px rgba(255, 100, 124, .35);
        }

        .toast.success::before {
            background: var(--success);
            box-shadow: 0 0 14px rgba(104, 224, 161, .30);
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
            background: rgba(255, 100, 124, .08);
            border: 1px solid rgba(255, 100, 124, .13);
        }

        .toast.success .toast-icon {
            color: #7ee2ad;
            background: rgba(104, 224, 161, .08);
            border: 1px solid rgba(104, 224, 161, .13);
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
            font-weight: 700;
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
            border: 1px solid rgba(255, 255, 255, .07);
            background: rgba(255, 255, 255, .025);
            color: #77717e;
            font-family: var(--github-mono);
            font-size: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .toast-close i {
            font-size: 10px;
        }

        .toast-close:hover {
            color: #f8f7ff;
            background: rgba(255, 255, 255, .06);
            border-color: rgba(255, 255, 255, .12);
        }

        /* =========================================================
           POLICY MODAL
        ========================================================= */
        .policy-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s, visibility .2s;
        }

        .policy-modal.show {
            opacity: 1;
            visibility: visible;
        }

        .policy-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 2, 7, .72);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .policy-box {
            position: relative;
            width: 100%;
            max-width: 440px;
            max-height: 540px;
            overflow: hidden;
            border-radius: 15px;
            background: linear-gradient(180deg, rgba(19, 16, 30, .97), rgba(8, 7, 14, .97));
            border: 1px solid rgba(255, 255, 255, .09);
            box-shadow: 0 30px 90px rgba(0, 0, 0, .60), 0 0 70px rgba(139, 92, 246, .08);
            transform: translateY(10px) scale(.98);
            transition: transform .2s;
        }

        .policy-modal.show .policy-box {
            transform: translateY(0) scale(1);
        }

        .policy-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
        }

        .policy-brand {
            margin-bottom: 6px;
            color: #6d6877;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .policy-header h3 {
            color: #f8f7ff;
            font-size: 14px;
            font-weight: 700;
        }

        .policy-close {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .07);
            background: rgba(255, 255, 255, .035);
            color: #77717f;
            cursor: pointer;
        }

        .policy-close:hover {
            color: white;
            background: rgba(255, 255, 255, .06);
        }

        .policy-tabs {
            display: flex;
            gap: 6px;
            padding: 0 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
        }

        .policy-tab {
            padding: 8px 12px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: #777281;
            font-family: var(--github-mono);
            font-size: 8px;
            font-weight: 600;
            cursor: pointer;
        }

        .policy-tab:hover {
            color: #b2acba;
        }

        .policy-tab.active {
            color: #d7caff;
            background: rgba(139, 92, 246, .10);
            box-shadow: inset 0 0 0 1px rgba(139, 92, 246, .13);
        }

        .policy-content {
            max-height: 300px;
            overflow-y: auto;
            padding: 20px;
        }

        .policy-content::-webkit-scrollbar {
            width: 5px;
        }

        .policy-content::-webkit-scrollbar-thumb {
            border-radius: 20px;
            background: rgba(167, 139, 250, .22);
        }

        .policy-page {
            display: none;
        }

        .policy-page.active {
            display: block;
        }

        .policy-page h4 {
            margin-bottom: 14px;
            color: #f8f7ff;
            font-size: 11px;
        }

        .policy-page h5 {
            margin-top: 18px;
            margin-bottom: 8px;
            color: #b6a7e9;
            font-size: 9px;
        }

        .policy-page p {
            margin-bottom: 12px;
            color: #777281;
            font-size: 8px;
            line-height: 1.8;
        }

        .policy-footer {
            display: flex;
            justify-content: flex-end;
            padding: 15px 20px 20px;
            border-top: 1px solid rgba(255, 255, 255, .06);
        }

        .policy-done {
            height: 36px;
            padding: 0 18px;
            border: 0;
            border-radius: 8px;
            background: #f4f2fb;
            color: #08070c;
            font-family: var(--github-mono);
            font-size: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        /* =========================================================
           MOBILE
        ========================================================= */
        @media(max-width: 500px) {
            body {
                align-items: flex-start;
                padding: 40px 20px;
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

            .policy-box {
                max-height: 82vh;
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
         NOTIFICATION AREA
    ========================================================= -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- =========================================================
         SIGNUP
    ========================================================= -->
    <main class="auth">

        <!-- LOGO -->
        <div class="logo">
            <div class="logo-symbol">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div>

        <!-- HEADER -->
        <div class="heading">
            <h1>Create <span>account</span></h1>
            <p>Enter your details to create your account</p>
        </div>

        <!-- FORM -->
        <form method="POST" id="signupForm">

            <!-- FULL NAME -->
            <div class="field">
                <div class="field-top">
                    <label for="full_name">Full name</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-person-vcard input-icon"></i>
                    <input type="text" name="full_name" id="full_name" class="input" placeholder="Enter your full name" autocomplete="name" required>
                </div>
            </div>

            <!-- USERNAME -->
            <div class="field">
                <div class="field-top">
                    <label for="username">Username</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" name="username" id="username" class="input" placeholder="Choose a username" autocomplete="username" required>
                </div>
            </div>

            <!-- EMAIL -->
            <div class="field">
                <div class="field-top">
                    <label for="email">Email address</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" id="email" class="input" placeholder="Enter your email" autocomplete="email" required>
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="field">
                <div class="field-top">
                    <label for="password">Password</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="input" placeholder="Create a password" autocomplete="new-password" required>
                    <button type="button" class="eye" data-target="password" aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="field">
                <div class="field-top">
                    <label for="confirm_password">Confirm password</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-shield-lock input-icon"></i>
                    <input type="password" name="confirm_password" id="confirm_password" class="input" placeholder="Enter password again" autocomplete="new-password" required>
                    <button type="button" class="eye" data-target="confirm_password" aria-label="Show password">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <!-- TERMS -->
            <div class="terms">
                <input type="checkbox" name="terms" id="termsCheckbox" value="1" required>
                <span>
                    I agree to the
                    <button type="button" class="policy-link" data-policy="terms">Terms</button>
                    and
                    <button type="button" class="policy-link" data-policy="privacy">Privacy Policy</button>
                </span>
            </div>

            <!-- SUBMIT -->
            <button type="submit" name="signup" class="create-button">Create account</button>
        </form>

        <!-- LOGIN -->
        <div class="login-link">
            Already have an account?
            <a href="login.php">Sign in</a>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <span>EliteFort</span>
            <span class="footer-dot"></span>
            <span>Authentication</span>
        </div>

    </main>

    <!-- =========================================================
         TERMS / PRIVACY MODAL
    ========================================================= -->
    <div class="policy-modal" id="policyModal">
        <div class="policy-backdrop" id="policyBackdrop"></div>

        <div class="policy-box">
            <!-- HEADER -->
            <div class="policy-header">
                <div>
                    <div class="policy-brand">ELITEFORT</div>
                    <h3 id="policyTitle">Terms</h3>
                </div>
                <button type="button" class="policy-close" id="policyClose" aria-label="Close policy">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- TABS -->
            <div class="policy-tabs">
                <button type="button" class="policy-tab active" data-tab="terms">Terms</button>
                <button type="button" class="policy-tab" data-tab="privacy">Privacy</button>
            </div>

            <!-- CONTENT -->
            <div class="policy-content" id="policyContent">
                <!-- TERMS -->
                <div class="policy-page active" id="termsContent">
                    <h4>Terms of Service</h4>
                    <p>By creating an EliteFort account, you agree to these terms and to use the service responsibly.</p>
                    <h5>Account Responsibility</h5>
                    <p>You are responsible for maintaining the confidentiality of your account credentials and activity performed using your account.</p>
                    <h5>Acceptable Use</h5>
                    <p>EliteFort accounts must not be used for unlawful, fraudulent, abusive, disruptive or unauthorized activities.</p>
                    <h5>Account Security</h5>
                    <p>Use a strong password and take reasonable precautions to prevent unauthorized access to your account.</p>
                    <h5>Account Suspension</h5>
                    <p>EliteFort may restrict or suspend accounts where misuse, security threats or violations are identified.</p>
                    <h5>Changes</h5>
                    <p>These terms may be updated as EliteFort services and policies evolve.</p>
                </div>

                <!-- PRIVACY -->
                <div class="policy-page" id="privacyContent">
                    <h4>Privacy Policy</h4>
                    <p>This policy explains how information associated with your EliteFort account may be collected and used.</p>
                    <h5>Information Collected</h5>
                    <p>EliteFort may collect information such as your name, username, email address and technical authentication information.</p>
                    <h5>How Information Is Used</h5>
                    <p>Information may be used to create and manage accounts, authenticate users and protect EliteFort services.</p>
                    <h5>Authentication Data</h5>
                    <p>Passwords should be protected using secure password hashing rather than being stored as plain text.</p>
                    <h5>Security Information</h5>
                    <p>Technical information such as IP addresses, browser information and security events may be processed to protect user accounts.</p>
                    <h5>Policy Updates</h5>
                    <p>This Privacy Policy may be updated as EliteFort services and security practices change.</p>
                </div>
            </div>

            <!-- DONE -->
            <div class="policy-footer">
                <button type="button" class="policy-done" id="policyDone">Done</button>
            </div>
        </div>
    </div>

    <script>
        /* =========================================================
           PASSWORD SHOW / HIDE
        ========================================================= */
        const eyeButtons = document.querySelectorAll('.eye');

        eyeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.target);
                const icon = button.querySelector('i');
                const hidden = input.type === 'password';

                input.type = hidden ? 'text' : 'password';
                icon.classList.toggle('bi-eye', !hidden);
                icon.classList.toggle('bi-eye-slash', hidden);
                button.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
            });
        });

        /* =========================================================
           REUSABLE NOTIFICATION
        ========================================================= */
        function showToast(type, message, title = null) {
            const container = document.getElementById('toastContainer');

            if (type !== 'error' && type !== 'success') {
                type = 'error';
            }

            if (!title) {
                title = type === 'success' ? 'Completed' : 'Unable to continue';
            }

            // Remove existing notification
            const oldToast = container.querySelector('.toast');
            if (oldToast) {
                oldToast.remove();
            }

            // Build notification
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

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.add('show');
            });

            // Close
            function dismissToast() {
                clearTimeout(dismissTimer);
                toast.classList.remove('show');
                toast.classList.add('closing');
                setTimeout(() => {
                    toast.remove();
                }, 280);
            }

            // Manual dismiss
            closeButton.addEventListener('click', dismissToast);

            // Auto close
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
           PASSWORD MATCH
        ========================================================= */
        const signupForm = document.getElementById('signupForm');

        signupForm.addEventListener('submit', event => {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                showToast('error', 'Please enter the same password in both password fields.', 'Passwords do not match');
            }
        });

        /* =========================================================
           PHP ERROR / SUCCESS
        ========================================================= */
        document.addEventListener('DOMContentLoaded', () => {
            <?php if (!empty($error)): ?>
                showToast('error', <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Registration failed');
            <?php elseif (!empty($success)): ?>
                showToast('success', <?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Account created');
            <?php endif; ?>
        });

        /* =========================================================
           TERMS / PRIVACY MODAL
        ========================================================= */
        const modal = document.getElementById('policyModal');
        const modalTitle = document.getElementById('policyTitle');
        const policyContent = document.getElementById('policyContent');
        const closeButton = document.getElementById('policyClose');
        const doneButton = document.getElementById('policyDone');
        const backdrop = document.getElementById('policyBackdrop');
        const policyLinks = document.querySelectorAll('.policy-link');
        const tabs = document.querySelectorAll('.policy-tab');
        const termsContent = document.getElementById('termsContent');
        const privacyContent = document.getElementById('privacyContent');

        function switchPolicy(type) {
            tabs.forEach(tab => {
                tab.classList.toggle('active', tab.dataset.tab === type);
            });

            termsContent.classList.toggle('active', type === 'terms');
            privacyContent.classList.toggle('active', type === 'privacy');

            modalTitle.textContent = type === 'terms' ? 'Terms' : 'Privacy Policy';
            policyContent.scrollTop = 0;
        }

        function openPolicy(type) {
            switchPolicy(type);
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closePolicy() {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        policyLinks.forEach(link => {
            link.addEventListener('click', () => {
                openPolicy(link.dataset.policy);
            });
        });

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                switchPolicy(tab.dataset.tab);
            });
        });

        closeButton.addEventListener('click', closePolicy);
        doneButton.addEventListener('click', closePolicy);
        backdrop.addEventListener('click', closePolicy);

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closePolicy();
            }
        });
    </script>

</body>
</html>