<?php
/*
|--------------------------------------------------------------------------
| Forgot Password Page
|--------------------------------------------------------------------------
| Set $successMessage or $errorMessage from your controller/backend.
| The form currently submits to forgot-password-process.php.
*/

$successMessage = $successMessage ?? '';
$errorMessage   = $errorMessage ?? '';
$oldEmail       = $oldEmail ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Forgot Password</title>

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
            --danger: #b42318;
            --danger-bg: #fff1f0;
            --success: #217a44;
            --success-bg: #edf9f1;
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

            color: var(--matte-black);

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

        .forgot-card {
            width: 100%;
            max-width: 440px;

            position: relative;
            z-index: 2;
            overflow: hidden;

            padding: 3.2rem 2.8rem 2.8rem;

            background: rgba(250, 250, 250, 0.93);

            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 32px;

            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);

            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.07),
                0 8px 32px rgba(0, 0, 0, 0.035),
                inset 0 1px 0 rgba(255, 255, 255, 0.75);
        }

        .forgot-card::before {
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

        .card-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .icon-mark {
            width: 68px;
            height: 68px;

            margin: 0 auto 1.2rem;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            color: var(--white);
            background:
                linear-gradient(
                    135deg,
                    var(--matte-black),
                    var(--dark-gray)
                );

            font-size: 1.65rem;

            box-shadow:
                0 10px 25px rgba(23, 23, 23, 0.22);
        }

        .card-header h1 {
            margin-bottom: 0.55rem;

            color: var(--matte-black);

            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .card-header p {
            max-width: 340px;
            margin: 0 auto;

            color: var(--medium-gray);

            font-size: 0.92rem;
            line-height: 1.65;
        }

        .status-message {
            margin-bottom: 1.25rem;
            padding: 0.9rem 1rem;

            display: flex;
            align-items: flex-start;
            gap: 0.7rem;

            border-radius: 14px;

            font-size: 0.83rem;
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

        .forgot-form {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
        }

        .form-label {
            display: block;
            margin: 0 0 0.55rem 0.25rem;

            color: var(--charcoal);

            font-size: 0.8rem;
            font-weight: 650;
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

            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 500;

            transition: 0.25s ease;
        }

        .input-group input::placeholder {
            color: #929292;
            font-weight: 400;
        }

        .input-group input:hover {
            border-color: rgba(23, 23, 23, 0.3);
        }

        .input-group input:focus {
            background: var(--white);
            border-color: var(--matte-black);

            box-shadow:
                0 0 0 6px rgba(23, 23, 23, 0.08),
                inset 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        .input-group:focus-within .input-icon {
            color: var(--matte-black);
        }

        .reset-btn {
            width: 100%;

            padding: 1rem;

            position: relative;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;

            border: none;
            border-radius: 60px;

            color: var(--white);
            background:
                linear-gradient(
                    135deg,
                    var(--matte-black),
                    var(--charcoal)
                );

            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 650;
            letter-spacing: 0.2px;

            cursor: pointer;

            box-shadow:
                0 7px 24px rgba(23, 23, 23, 0.2);

            transition: 0.25s ease;
        }

        .reset-btn::before {
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

        .reset-btn:hover {
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

        .reset-btn:active {
            transform: scale(0.98);
        }

        .reset-btn i {
            font-size: 0.85rem;
            transition: transform 0.25s ease;
        }

        .reset-btn:hover i {
            transform: translateX(4px);
        }

        .help-box {
            margin-top: 1.35rem;
            padding: 0.9rem 1rem;

            display: flex;
            align-items: flex-start;
            gap: 0.7rem;

            color: #686868;
            background: rgba(23, 23, 23, 0.045);

            border: 1px solid rgba(23, 23, 23, 0.07);
            border-radius: 14px;

            font-size: 0.78rem;
            line-height: 1.5;
        }

        .help-box i {
            margin-top: 0.12rem;
            color: var(--charcoal);
        }

        .card-footer {
            margin-top: 1.75rem;
            padding-top: 1.55rem;

            border-top: 1px solid rgba(60, 60, 60, 0.11);

            text-align: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;

            color: var(--charcoal);

            font-size: 0.85rem;
            font-weight: 650;
            text-decoration: none;

            transition: 0.2s ease;
        }

        .back-link:hover {
            color: var(--matte-black);
            transform: translateX(-3px);
        }

        .back-link i {
            font-size: 0.75rem;
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            .forgot-card {
                padding: 2.3rem 1.4rem 2rem;
                border-radius: 24px;
            }

            .corner-deco {
                right: 30px;
                width: 65px;
            }

            .icon-mark {
                width: 58px;
                height: 58px;

                font-size: 1.35rem;
            }

            .card-header h1 {
                font-size: 1.5rem;
            }

            .card-header p {
                font-size: 0.86rem;
            }

            .input-group input {
                padding: 0.85rem 0.9rem 0.85rem 2.8rem;
                font-size: 0.9rem;
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
        class="forgot-card"
        role="main"
        aria-labelledby="pageTitle"
    >
        <div class="corner-deco"></div>

        <div class="card-content">

            <header class="card-header">
                <div class="icon-mark">
                    <i class="fa-solid fa-key"></i>
                </div>

                <h1 id="pageTitle">Forgot password?</h1>

                <p>
                    Enter the email address connected to your account.
                    A secure password-reset link will be sent to that address.
                </p>
            </header>

            <?php if ($successMessage !== ''): ?>
                <div
                    class="status-message success"
                    role="status"
                    aria-live="polite"
                >
                    <i class="fa-solid fa-circle-check"></i>

                    <span>
                        <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage !== ''): ?>
                <div
                    class="status-message error"
                    role="alert"
                >
                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span>
                        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            <?php endif; ?>

            <form
                class="forgot-form"
                action="forgot-password-process.php"
                method="POST"
            >
                <div>
                    <label
                        class="form-label"
                        for="email"
                    >
                        Email address
                    </label>

                    <div class="input-group">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Enter your email address"
                            autocomplete="email"
                            inputmode="email"
                            maxlength="254"
                            required
                        >

                        <i class="fa-solid fa-envelope input-icon"></i>
                    </div>
                </div>

                <button
                    type="submit"
                    class="reset-btn"
                >
                    <span>Send reset link</span>

                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="help-box">
                <i class="fa-solid fa-circle-info"></i>

                <span>
                    For security, the same message should be shown whether
                    the email exists or not. Reset links should also expire
                    after a short period.
                </span>
            </div>

            <footer class="card-footer">
                <a
                    href="login.php"
                    class="back-link"
                >
                    <i class="fa-solid fa-arrow-left"></i>

                    <span>Back to sign in</span>
                </a>
            </footer>

        </div>
    </main>

</body>
</html>
