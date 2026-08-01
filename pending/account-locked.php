<?php
declare(strict_types=1);

session_start();

/*
|--------------------------------------------------------------------------
| Account lock information
|--------------------------------------------------------------------------
| Set this after too many failed login attempts:
|
| $_SESSION['account_lock_until'] = time() + 900;
| $_SESSION['locked_email'] = $email;
|
| Default development lock duration: 15 minutes.
*/

$lockUntil = (int) (
    $_SESSION['account_lock_until']
    ?? ($_GET['until'] ?? (time() + 900))
);

$lockedEmail = trim((string) (
    $_SESSION['locked_email']
    ?? ''
));

$remainingSeconds = max(0, $lockUntil - time());

if ($remainingSeconds <= 0) {
    unset(
        $_SESSION['account_lock_until'],
        $_SESSION['locked_email']
    );
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="The account is temporarily locked.">
    <title>Account Temporarily Locked</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --matte-black: #171717;
            --black-soft: #202020;
            --charcoal: #2b2b2b;
            --dark-gray: #464646;
            --medium-gray: #747474;
            --soft-gray: #a4a4a4;
            --background: #e9e9e9;
            --surface: #fafafa;
            --white: #ffffff;
            --success: #267647;
            --success-bg: #edf8f1;
            --warning: #946200;
            --warning-bg: #fff7df;
            --danger: #a93226;
            --danger-bg: #fff1f0;
            --transition: 0.25s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            color-scheme: light;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: var(--matte-black);
            background:
                radial-gradient(circle at 12% 14%, rgba(23, 23, 23, 0.08), transparent 24rem),
                radial-gradient(circle at 88% 82%, rgba(23, 23, 23, 0.06), transparent 22rem),
                var(--background);
            font-family: Inter, "Segoe UI", system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
        }

        body::before {
            width: 300px;
            height: 300px;
            top: -185px;
            right: -125px;
            border: 44px solid rgba(23, 23, 23, 0.04);
        }

        body::after {
            width: 220px;
            height: 220px;
            bottom: -145px;
            left: -105px;
            background: rgba(23, 23, 23, 0.035);
        }

        a,
        button {
            font: inherit;
            -webkit-tap-highlight-color: transparent;
        }

        .error-card {
            width: 100%;
            max-width: 1050px;
            min-height: 590px;
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(360px, 0.95fr);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 32px;
            background: rgba(250, 250, 250, 0.9);
            box-shadow: 0 28px 80px rgba(23, 23, 23, 0.14);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .content-side {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            padding: clamp(2rem, 6vw, 4.8rem);
        }

        .content {
            width: 100%;
            max-width: 530px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 3rem;
            color: var(--matte-black);
            text-decoration: none;
        }

        .brand-mark {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            color: var(--white);
            background: linear-gradient(135deg, var(--matte-black), var(--dark-gray));
            box-shadow: 0 10px 22px rgba(23, 23, 23, 0.2);
        }

        .brand-copy {
            display: flex;
            flex-direction: column;
            gap: 0.08rem;
        }

        .brand-name {
            font-size: 0.95rem;
            font-weight: 800;
        }

        .brand-label {
            color: var(--medium-gray);
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.9px;
            text-transform: uppercase;
        }

        .error-label {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            margin-bottom: 1rem;
            padding: 0.45rem 0.72rem;
            border: 1px solid rgba(23, 23, 23, 0.08);
            border-radius: 999px;
            color: var(--charcoal);
            background: rgba(23, 23, 23, 0.04);
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .error-title {
            margin-bottom: 1rem;
            font-size: clamp(2.1rem, 5vw, 4.2rem);
            line-height: 1;
            letter-spacing: -2.4px;
        }

        .error-description {
            max-width: 480px;
            color: var(--medium-gray);
            font-size: 0.86rem;
            line-height: 1.75;
        }

        .status-box {
            margin-top: 1.25rem;
            padding: 0.9rem 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            border: 1px solid rgba(23, 23, 23, 0.08);
            border-radius: 14px;
            color: #565656;
            background: rgba(23, 23, 23, 0.035);
            font-size: 0.7rem;
            line-height: 1.55;
        }

        .status-box i {
            margin-top: 0.12rem;
            color: var(--charcoal);
        }

        .countdown {
            margin-top: 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.75rem 0.9rem;
            border-radius: 13px;
            color: var(--white);
            background: linear-gradient(135deg, var(--matte-black), var(--charcoal));
            box-shadow: 0 10px 22px rgba(23, 23, 23, 0.14);
        }

        .countdown strong {
            font-size: 0.85rem;
        }

        .countdown span {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.67rem;
        }

        .actions {
            margin-top: 1.7rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .button {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            padding: 0.78rem 1.08rem;
            border-radius: 13px;
            font-size: 0.76rem;
            font-weight: 750;
            text-decoration: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .button-primary {
            border: 1px solid var(--matte-black);
            color: var(--white);
            background: linear-gradient(135deg, var(--matte-black), var(--charcoal));
            box-shadow: 0 10px 22px rgba(23, 23, 23, 0.18);
        }

        .button-secondary {
            border: 1px solid rgba(23, 23, 23, 0.12);
            color: var(--charcoal);
            background: var(--white);
        }

        .button:hover {
            transform: translateY(-2px);
        }

        .button:active {
            transform: scale(0.98);
        }

        .help-text {
            margin-top: 1.45rem;
            color: var(--soft-gray);
            font-size: 0.67rem;
            line-height: 1.55;
        }

        .help-text a {
            color: var(--charcoal);
            font-weight: 700;
            text-decoration: none;
        }

        .visual-side {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: var(--white);
            background: linear-gradient(145deg, var(--matte-black) 0%, #252525 55%, #363636 100%);
        }

        .visual-side::before {
            content: "";
            width: 390px;
            height: 390px;
            position: absolute;
            top: -220px;
            right: -150px;
            border: 56px solid rgba(255, 255, 255, 0.035);
            border-radius: 50%;
        }

        .visual-side::after {
            content: "";
            width: 220px;
            height: 220px;
            position: absolute;
            bottom: -140px;
            left: -100px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
        }

        .visual-wrap {
            width: min(100%, 360px);
            aspect-ratio: 1;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .outer-ring,
        .middle-ring {
            position: absolute;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .outer-ring {
            width: 100%;
            height: 100%;
        }

        .middle-ring {
            width: 72%;
            height: 72%;
        }

        .orbit-dot {
            width: 11px;
            height: 11px;
            position: absolute;
            top: 11%;
            right: 16%;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 0 0 8px rgba(255, 255, 255, 0.05);
        }

        .visual-icon {
            width: 118px;
            height: 118px;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 12px solid rgba(255, 255, 255, 0.92);
            border-radius: 36px;
            color: var(--white);
            font-size: 2.4rem;
            transform: rotate(-8deg);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.22);
        }

        .visual-icon i {
            transform: rotate(8deg);
        }

        .error-number {
            position: relative;
            z-index: 2;
            color: var(--white);
            font-size: clamp(5rem, 11vw, 8.4rem);
            font-weight: 850;
            line-height: 1;
            letter-spacing: -8px;
        }

        .visual-caption {
            position: absolute;
            right: 2rem;
            bottom: 2rem;
            left: 2rem;
            z-index: 2;
            color: rgba(255, 255, 255, 0.42);
            font-size: 0.66rem;
            text-align: center;
            letter-spacing: 0.4px;
        }

        @media (max-width: 850px) {
            .error-card {
                max-width: 620px;
                grid-template-columns: 1fr;
            }

            .visual-side {
                min-height: 320px;
                grid-row: 1;
                border-radius: 31px 31px 0 0;
            }

            .content-side {
                padding: 2.4rem;
            }

            .brand {
                margin-bottom: 2rem;
            }

            .visual-wrap {
                width: 250px;
            }
        }

        @media (max-width: 520px) {
            body {
                padding: 0.8rem;
            }

            .error-card {
                border-radius: 24px;
            }

            .visual-side {
                min-height: 260px;
                border-radius: 23px 23px 0 0;
            }

            .content-side {
                padding: 1.7rem 1.25rem 2rem;
            }

            .actions {
                align-items: stretch;
                flex-direction: column;
            }

            .button {
                width: 100%;
            }

            .error-title {
                letter-spacing: -1.4px;
            }

            .error-number {
                letter-spacing: -5px;
            }

            .visual-caption {
                bottom: 1.2rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }
</style>
</head>
<body>
    <main class="error-card">
        <section class="content-side">
            <div class="content">
                <a href="index.php" class="brand" aria-label="Go to homepage">
                    <div class="brand-mark">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="brand-copy">
                        <span class="brand-name">SecurePortal</span>
                        <span class="brand-label">Account centre</span>
                    </div>
                </a>

                <span class="error-label">
                    <i class="fa-solid fa-user-lock"></i>
                    Security protection
                </span>

                <h1 class="error-title">Account temporarily locked.</h1>

                <p class="error-description">
                    Too many unsuccessful sign-in attempts were detected.
                    Access has been temporarily restricted to protect the account.
                    <?php if ($lockedEmail !== ''): ?>
                        This applies to
                        <strong><?= e($lockedEmail) ?></strong>.
                    <?php endif; ?>
                </p>

                <div class="countdown" id="countdownBox">
                    <i class="fa-solid fa-clock"></i>
                    <div>
                        <strong id="countdownValue">
                            <?= (int) ceil($remainingSeconds / 60) ?> minutes
                        </strong>
                        <br>
                        <span id="countdownLabel">
                            Remaining before another sign-in attempt
                        </span>
                    </div>
                </div>

                <div class="status-box">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>
                        Password reset can be used when the password may have been
                        forgotten. Do not repeatedly retry the same password.
                    </span>
                </div>

                <div class="actions">
                    <a
                        href="<?= $remainingSeconds > 0 ? 'forgot-password.php' : 'login.php' ?>"
                        class="button button-primary"
                        id="primaryAction"
                    >
                        <i class="fa-solid <?= $remainingSeconds > 0 ? 'fa-key' : 'fa-right-to-bracket' ?>"></i>
                        <span id="primaryActionText">
                            <?= $remainingSeconds > 0 ? 'Reset password' : 'Return to login' ?>
                        </span>
                    </a>

                    <a href="index.php" class="button button-secondary">
                        <i class="fa-solid fa-house"></i>
                        Go to homepage
                    </a>
                </div>

                <p class="help-text">
                    The lock remains after refreshing this page.
                    <a href="contact.php">Contact support</a>
                    when access is urgently required.
                </p>
            </div>
        </section>

        <section class="visual-side" aria-label="Locked account illustration">
            <div class="visual-wrap">
                <div class="outer-ring"></div>
                <div class="middle-ring"></div>
                <div class="orbit-dot"></div>
                <div class="visual-icon">
                    <i class="fa-solid fa-user-lock"></i>
                </div>
            </div>

            <p class="visual-caption">
                Temporary protection was activated after repeated sign-in failures.
            </p>
        </section>
    </main>

    <script>
        let remainingSeconds = <?= $remainingSeconds ?>;

        const countdownValue =
            document.getElementById("countdownValue");

        const countdownLabel =
            document.getElementById("countdownLabel");

        const primaryAction =
            document.getElementById("primaryAction");

        const primaryActionText =
            document.getElementById("primaryActionText");

        function formatTime(seconds) {
            const minutes =
                Math.floor(seconds / 60);

            const remaining =
                seconds % 60;

            return String(minutes).padStart(2, "0")
                + ":"
                + String(remaining).padStart(2, "0");
        }

        function updateCountdown() {
            if (remainingSeconds <= 0) {
                countdownValue.textContent =
                    "Lock expired";

                countdownLabel.textContent =
                    "The account can now be accessed again.";

                primaryAction.href =
                    "login.php";

                primaryActionText.textContent =
                    "Return to login";

                return;
            }

            countdownValue.textContent =
                formatTime(remainingSeconds);

            remainingSeconds -= 1;

            window.setTimeout(
                updateCountdown,
                1000
            );
        }

        updateCountdown();
    </script>
</body>
</html>
