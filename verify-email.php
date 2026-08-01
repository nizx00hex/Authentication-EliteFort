<?php
/*
|--------------------------------------------------------------------------
| Verify Email Page
|--------------------------------------------------------------------------
| Expected URL example:
| verify-email.php?token=SECURE_EMAIL_VERIFICATION_TOKEN
|
| Replace the demo state handling below with your database verification logic.
*/

$token = $_GET['token'] ?? '';

/*
|--------------------------------------------------------------------------
| Demo states
|--------------------------------------------------------------------------
| Supported values:
| success, expired, invalid, already_verified, pending
|
| In production, determine this value after validating the token in the
| database. The default is "pending" when no verification result exists.
*/

$verificationState = $verificationState ?? 'pending';
$userEmail         = $userEmail ?? '';
$resendSuccess     = $resendSuccess ?? '';
$resendError       = $resendError ?? '';

$states = [
    'success' => [
        'icon'        => 'fa-circle-check',
        'title'       => 'Email verified',
        'description' => 'Your email address has been verified successfully. Your account is now ready to use.',
        'class'       => 'success',
    ],
    'expired' => [
        'icon'        => 'fa-clock',
        'title'       => 'Verification link expired',
        'description' => 'This verification link has expired. Request a new verification email to continue.',
        'class'       => 'warning',
    ],
    'invalid' => [
        'icon'        => 'fa-triangle-exclamation',
        'title'       => 'Invalid verification link',
        'description' => 'This verification link is invalid or has already been used. Request a new link if needed.',
        'class'       => 'error',
    ],
    'already_verified' => [
        'icon'        => 'fa-shield-check',
        'title'       => 'Already verified',
        'description' => 'This email address has already been verified. Sign in to continue to your account.',
        'class'       => 'info',
    ],
    'pending' => [
        'icon'        => 'fa-envelope-open-text',
        'title'       => 'Verify your email',
        'description' => 'Open the verification link sent to your email address to activate your account.',
        'class'       => 'neutral',
    ],
];

$currentState = $states[$verificationState] ?? $states['invalid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Email Verification</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>
        :root {
            --matte-black: #171717;
            --charcoal: #2b2b2b;
            --dark-gray: #444444;
            --medium-gray: #747474;
            --light-gray: #e9e9e9;
            --card: #fafafa;
            --white: #ffffff;

            --success: #217a44;
            --success-bg: #edf9f1;

            --warning: #9a6700;
            --warning-bg: #fff8e6;

            --danger: #b42318;
            --danger-bg: #fff1f0;

            --info: #3f4f5f;
            --info-bg: #f1f3f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        button,
        input {
            font: inherit;
        }

        body {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 1.5rem;

            position: relative;
            overflow-x: hidden;

            color: var(--matte-black);

            background:
                radial-gradient(
                    circle at top right,
                    rgba(23, 23, 23, 0.10),
                    transparent 36%
                ),
                radial-gradient(
                    circle at bottom left,
                    rgba(43, 43, 43, 0.08),
                    transparent 38%
                ),
                var(--light-gray);

            font-family:
                Inter,
                "Segoe UI",
                system-ui,
                -apple-system,
                sans-serif;
        }

        body::before,
        body::after {
            content: "";

            position: fixed;
            z-index: 0;

            border-radius: 50%;
            pointer-events: none;
        }

        body::before {
            width: 360px;
            height: 360px;

            top: -190px;
            right: -120px;

            border: 45px solid rgba(23, 23, 23, 0.035);
        }

        body::after {
            width: 280px;
            height: 280px;

            bottom: -170px;
            left: -100px;

            background: rgba(23, 23, 23, 0.035);
        }

        .verify-card {
            width: 100%;
            max-width: 460px;

            position: relative;
            z-index: 2;
            overflow: hidden;

            padding: 3.1rem 2.8rem 2.8rem;

            background: rgba(250, 250, 250, 0.94);

            border: 1px solid rgba(255, 255, 255, 0.78);
            border-radius: 32px;

            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);

            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.07),
                0 8px 32px rgba(0, 0, 0, 0.035),
                inset 0 1px 0 rgba(255, 255, 255, 0.75);
        }

        .verify-card::before {
            content: "";

            position: absolute;
            inset: 0 0 auto;
            height: 42%;

            background:
                linear-gradient(
                    180deg,
                    rgba(255, 255, 255, 0.5),
                    transparent
                );

            pointer-events: none;
        }

        .corner-deco {
            width: 80px;
            height: 4px;

            position: absolute;

            top: -1px;
            right: 40px;

            border-radius: 0 0 4px 4px;

            background:
                linear-gradient(
                    90deg,
                    var(--matte-black),
                    var(--dark-gray),
                    var(--matte-black)
                );

            opacity: 0.5;
        }

        .card-content {
            position: relative;
            z-index: 1;
        }

        .state-header {
            margin-bottom: 1.8rem;
            text-align: center;
        }

        .state-icon {
            width: 72px;
            height: 72px;

            margin: 0 auto 1.25rem;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            font-size: 1.75rem;

            box-shadow:
                0 10px 25px rgba(23, 23, 23, 0.14);
        }

        .state-icon.success {
            color: var(--success);
            background: var(--success-bg);
            border: 1px solid rgba(33, 122, 68, 0.18);
        }

        .state-icon.warning {
            color: var(--warning);
            background: var(--warning-bg);
            border: 1px solid rgba(154, 103, 0, 0.18);
        }

        .state-icon.error {
            color: var(--danger);
            background: var(--danger-bg);
            border: 1px solid rgba(180, 35, 24, 0.18);
        }

        .state-icon.info {
            color: var(--info);
            background: var(--info-bg);
            border: 1px solid rgba(63, 79, 95, 0.15);
        }

        .state-icon.neutral {
            color: var(--white);
            background:
                linear-gradient(
                    135deg,
                    var(--matte-black),
                    var(--dark-gray)
                );

            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .state-header h1 {
            margin-bottom: 0.55rem;

            color: var(--matte-black);

            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .state-header p {
            max-width: 360px;
            margin: 0 auto;

            color: var(--medium-gray);

            font-size: 0.92rem;
            line-height: 1.65;
        }

        .email-box {
            margin-bottom: 1.3rem;
            padding: 0.9rem 1rem;

            display: flex;
            align-items: center;
            gap: 0.75rem;

            color: var(--charcoal);
            background: rgba(23, 23, 23, 0.045);

            border: 1px solid rgba(23, 23, 23, 0.08);
            border-radius: 14px;

            font-size: 0.82rem;
            line-height: 1.45;

            overflow-wrap: anywhere;
        }

        .email-box i {
            color: var(--matte-black);
        }

        .status-message {
            margin-bottom: 1.2rem;
            padding: 0.9rem 1rem;

            display: flex;
            align-items: flex-start;
            gap: 0.7rem;

            border-radius: 14px;

            font-size: 0.82rem;
            line-height: 1.5;
        }

        .status-message i {
            margin-top: 0.15rem;
        }

        .status-message.success {
            color: var(--success);
            background: var(--success-bg);
            border: 1px solid rgba(33, 122, 68, 0.15);
        }

        .status-message.error {
            color: var(--danger);
            background: var(--danger-bg);
            border: 1px solid rgba(180, 35, 24, 0.15);
        }

        .action-stack {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .primary-button,
        .secondary-button {
            width: 100%;

            padding: 1rem;

            position: relative;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;

            border-radius: 60px;

            font-size: 0.93rem;
            font-weight: 650;
            letter-spacing: 0.2px;
            text-decoration: none;

            cursor: pointer;

            transition: 0.25s ease;
        }

        .primary-button {
            border: none;

            color: var(--white);
            background:
                linear-gradient(
                    135deg,
                    var(--matte-black),
                    var(--charcoal)
                );

            box-shadow:
                0 7px 24px rgba(23, 23, 23, 0.2);
        }

        .primary-button::before {
            content: "";

            position: absolute;
            inset: 0;

            background:
                linear-gradient(
                    135deg,
                    rgba(255, 255, 255, 0.13),
                    transparent 58%
                );

            pointer-events: none;
        }

        .primary-button:hover {
            transform: translateY(-2px);

            background:
                linear-gradient(
                    135deg,
                    #2a2a2a,
                    var(--matte-black)
                );

            box-shadow:
                0 11px 30px rgba(23, 23, 23, 0.26);
        }

        .secondary-button {
            color: var(--charcoal);
            background: transparent;

            border: 1.5px solid rgba(23, 23, 23, 0.18);
        }

        .secondary-button:hover {
            color: var(--matte-black);
            background: rgba(23, 23, 23, 0.055);
            border-color: rgba(23, 23, 23, 0.32);

            transform: translateY(-2px);
        }

        .primary-button:active,
        .secondary-button:active {
            transform: scale(0.98);
        }

        .resend-form {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;

            top: 50%;
            left: 17px;

            transform: translateY(-50%);

            color: #8b8b8b;

            font-size: 0.9rem;
            pointer-events: none;

            transition: 0.25s ease;
        }

        .input-group input {
            width: 100%;

            padding: 0.95rem 1rem 0.95rem 3rem;

            color: var(--matte-black);
            background: rgba(250, 250, 250, 0.72);

            border: 1.5px solid rgba(60, 60, 60, 0.16);
            border-radius: 60px;

            outline: none;

            font-size: 0.93rem;
            font-weight: 500;

            transition: 0.25s ease;
        }

        .input-group input::placeholder {
            color: #929292;
        }

        .input-group input:focus {
            background: var(--white);
            border-color: var(--matte-black);

            box-shadow:
                0 0 0 6px rgba(23, 23, 23, 0.08);
        }

        .input-group:focus-within .input-icon {
            color: var(--matte-black);
        }

        .note-box {
            margin-top: 1.3rem;
            padding: 0.9rem 1rem;

            display: flex;
            align-items: flex-start;
            gap: 0.7rem;

            color: #686868;
            background: rgba(23, 23, 23, 0.04);

            border: 1px solid rgba(23, 23, 23, 0.07);
            border-radius: 14px;

            font-size: 0.77rem;
            line-height: 1.5;
        }

        .note-box i {
            margin-top: 0.12rem;
            color: var(--charcoal);
        }

        .card-footer {
            margin-top: 1.7rem;
            padding-top: 1.5rem;

            border-top: 1px solid rgba(60, 60, 60, 0.11);

            text-align: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;

            color: var(--charcoal);

            font-size: 0.84rem;
            font-weight: 650;
            text-decoration: none;

            transition: 0.2s ease;
        }

        .back-link:hover {
            color: var(--matte-black);
            transform: translateX(-3px);
        }

        .back-link i {
            font-size: 0.74rem;
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            .verify-card {
                padding: 2.3rem 1.4rem 2rem;
                border-radius: 24px;
            }

            .corner-deco {
                right: 30px;
                width: 65px;
            }

            .state-icon {
                width: 60px;
                height: 60px;

                font-size: 1.4rem;
            }

            .state-header h1 {
                font-size: 1.5rem;
            }

            .state-header p {
                font-size: 0.86rem;
            }

            .input-group input {
                padding: 0.85rem 0.9rem 0.85rem 2.8rem;
                font-size: 0.89rem;
            }

            .input-icon {
                left: 15px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                transition: none !important;
            }
        }
    </style>
</head>

<body>

    <main
        class="verify-card"
        role="main"
        aria-labelledby="pageTitle"
    >
        <div class="corner-deco"></div>

        <div class="card-content">

            <header class="state-header">
                <div
                    class="state-icon <?= htmlspecialchars($currentState['class'], ENT_QUOTES, 'UTF-8') ?>"
                >
                    <i
                        class="fa-solid <?= htmlspecialchars($currentState['icon'], ENT_QUOTES, 'UTF-8') ?>"
                    ></i>
                </div>

                <h1 id="pageTitle">
                    <?= htmlspecialchars($currentState['title'], ENT_QUOTES, 'UTF-8') ?>
                </h1>

                <p>
                    <?= htmlspecialchars($currentState['description'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </header>

            <?php if ($userEmail !== ''): ?>
                <div class="email-box">
                    <i class="fa-solid fa-envelope"></i>

                    <span>
                        <?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($resendSuccess !== ''): ?>
                <div
                    class="status-message success"
                    role="status"
                    aria-live="polite"
                >
                    <i class="fa-solid fa-circle-check"></i>

                    <span>
                        <?= htmlspecialchars($resendSuccess, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($resendError !== ''): ?>
                <div
                    class="status-message error"
                    role="alert"
                >
                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>
                        <?= htmlspecialchars($resendError, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if (
                $verificationState === 'success' ||
                $verificationState === 'already_verified'
            ): ?>

                <div class="action-stack">
                    <a
                        href="login.php"
                        class="primary-button"
                    >
                        <span>Continue to sign in</span>

                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            <?php else: ?>

                <form
                    class="resend-form"
                    action="resend-verification-process.php"
                    method="POST"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <div class="input-group">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Enter your email address"
                            autocomplete="email"
                            inputmode="email"
                            maxlength="254"
                            required
                        >

                        <i class="fa-solid fa-envelope input-icon"></i>
                    </div>

                    <button
                        type="submit"
                        class="primary-button"
                    >
                        <span>Resend verification email</span>

                        <i class="fa-solid fa-paper-plane"></i>
                    </button>

                    <a
                        href="login.php"
                        class="secondary-button"
                    >
                        <i class="fa-solid fa-arrow-left"></i>

                        <span>Back to sign in</span>
                    </a>
                </form>

            <?php endif; ?>

            <div class="note-box">
                <i class="fa-solid fa-circle-info"></i>

                <span>
                    Check the spam or junk folder when the verification email
                    is not visible. Verification links should expire and be
                    stored as secure hashes in the database.
                </span>
            </div>

            <?php if (
                $verificationState === 'success' ||
                $verificationState === 'already_verified'
            ): ?>
                <footer class="card-footer">
                    <a
                        href="login.php"
                        class="back-link"
                    >
                        <i class="fa-solid fa-arrow-left"></i>

                        <span>Back to sign in</span>
                    </a>
                </footer>
            <?php endif; ?>

        </div>
    </main>

</body>
</html>
