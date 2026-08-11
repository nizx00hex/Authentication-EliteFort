<?php
/*
|--------------------------------------------------------------------------
| Reset Password Page
|--------------------------------------------------------------------------
| Expected URL example:
| reset-password.php?token=SECURE_RESET_TOKEN
|
| Validate the token on the server before showing or processing this form.
*/

$token          = $_GET['token'] ?? $_POST['token'] ?? '';
$successMessage = $successMessage ?? '';
$errorMessage   = $errorMessage ?? '';
$tokenIsValid   = $tokenIsValid ?? ($token !== '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reset Password</title>

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
            --warning: #9a6700;
            --warning-bg: #fff8e6;
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

        .reset-card {
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

        .reset-card::before {
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

            font-size: 1.6rem;

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
            max-width: 350px;
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

        .status-message.warning {
            color: var(--warning);
            background: var(--warning-bg);
            border: 1px solid rgba(154, 103, 0, 0.15);
        }

        .reset-form {
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

            padding: 0.95rem 3.25rem 0.95rem 3rem;

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

        .password-toggle {
            width: 36px;
            height: 36px;

            position: absolute;

            top: 50%;
            right: 9px;

            transform: translateY(-50%);

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;
            border-radius: 50%;

            color: #858585;
            background: transparent;

            cursor: pointer;

            transition: 0.2s ease;
        }

        .password-toggle:hover {
            color: var(--matte-black);
            background: rgba(23, 23, 23, 0.07);
        }

        .password-strength {
            margin-top: 0.65rem;
        }

        .strength-track {
            height: 5px;
            overflow: hidden;

            background: #dedede;
            border-radius: 50px;
        }

        .strength-bar {
            width: 0;
            height: 100%;

            border-radius: inherit;
            background: #9b9b9b;

            transition:
                width 0.25s ease,
                background 0.25s ease;
        }

        .strength-text {
            margin-top: 0.45rem;

            color: #777;

            font-size: 0.72rem;
            line-height: 1.4;
        }

        .password-rules {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.45rem 0.8rem;

            padding: 0.9rem 1rem;

            border-radius: 14px;
            background: rgba(23, 23, 23, 0.04);

            list-style: none;
        }

        .password-rules li {
            display: flex;
            align-items: center;
            gap: 0.45rem;

            color: #7d7d7d;

            font-size: 0.72rem;

            transition: color 0.2s ease;
        }

        .password-rules li i {
            font-size: 0.62rem;
        }

        .password-rules li.valid {
            color: var(--success);
        }

        .field-error {
            display: none;

            margin: 0.45rem 0 0 0.25rem;

            color: var(--danger);

            font-size: 0.74rem;
        }

        .field-error.visible {
            display: block;
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

        .reset-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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

            .reset-card {
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
                padding:
                    0.85rem
                    3rem
                    0.85rem
                    2.8rem;

                font-size: 0.9rem;
            }

            .input-icon {
                left: 15px;
            }

            .password-rules {
                grid-template-columns: 1fr;
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
        class="reset-card"
        role="main"
        aria-labelledby="pageTitle"
    >
        <div class="corner-deco"></div>

        <div class="card-content">

            <header class="card-header">
                <div class="icon-mark">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <h1 id="pageTitle">Reset password</h1>

                <p>
                    Create a strong new password for your account.
                    Avoid reusing a password from another website.
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

            <?php if (!$tokenIsValid): ?>

                <div
                    class="status-message warning"
                    role="alert"
                >
                    <i class="fa-solid fa-triangle-exclamation"></i>

                    <span>
                        This password-reset link is invalid or has expired.
                        Request a new reset link to continue.
                    </span>
                </div>

                <footer class="card-footer">
                    <a
                        href="forgot-password.php"
                        class="back-link"
                    >
                        <i class="fa-solid fa-arrow-left"></i>

                        <span>Request another reset link</span>
                    </a>
                </footer>

            <?php else: ?>

                <form
                    class="reset-form"
                    id="resetForm"
                    action="reset-password-process.php"
                    method="POST"
                    novalidate
                >
                    <input
                        type="hidden"
                        name="token"
                        value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <!-- Add your real CSRF token -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    >

                    <div>
                        <label
                            class="form-label"
                            for="password"
                        >
                            New password
                        </label>

                        <div class="input-group">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Create a new password"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required
                            >

                            <i class="fa-solid fa-lock input-icon"></i>

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="password"
                                aria-label="Show new password"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        <div class="password-strength">
                            <div class="strength-track">
                                <div
                                    class="strength-bar"
                                    id="strengthBar"
                                ></div>
                            </div>

                            <p
                                class="strength-text"
                                id="strengthText"
                            >
                                Use at least 8 characters with uppercase,
                                lowercase, a number and a symbol.
                            </p>
                        </div>
                    </div>

                    <ul class="password-rules">
                        <li id="ruleLength">
                            <i class="fa-solid fa-circle"></i>
                            At least 8 characters
                        </li>

                        <li id="ruleUpper">
                            <i class="fa-solid fa-circle"></i>
                            One uppercase letter
                        </li>

                        <li id="ruleLower">
                            <i class="fa-solid fa-circle"></i>
                            One lowercase letter
                        </li>

                        <li id="ruleNumber">
                            <i class="fa-solid fa-circle"></i>
                            One number
                        </li>

                        <li id="ruleSymbol">
                            <i class="fa-solid fa-circle"></i>
                            One special character
                        </li>
                    </ul>

                    <div>
                        <label
                            class="form-label"
                            for="confirmPassword"
                        >
                            Confirm new password
                        </label>

                        <div class="input-group">
                            <input
                                type="password"
                                id="confirmPassword"
                                name="confirm_password"
                                placeholder="Repeat the new password"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="72"
                                required
                            >

                            <i class="fa-solid fa-shield-halved input-icon"></i>

                            <button
                                type="button"
                                class="password-toggle"
                                data-target="confirmPassword"
                                aria-label="Show confirmed password"
                            >
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        <p
                            class="field-error"
                            id="passwordError"
                            role="alert"
                        >
                            Passwords do not match.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="reset-btn"
                    >
                        <span>Update password</span>

                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

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

    <script>
        const resetForm =
            document.getElementById("resetForm");

        const password =
            document.getElementById("password");

        const confirmPassword =
            document.getElementById("confirmPassword");

        const passwordError =
            document.getElementById("passwordError");

        const strengthBar =
            document.getElementById("strengthBar");

        const strengthText =
            document.getElementById("strengthText");

        const rules = {
            length: document.getElementById("ruleLength"),
            upper: document.getElementById("ruleUpper"),
            lower: document.getElementById("ruleLower"),
            number: document.getElementById("ruleNumber"),
            symbol: document.getElementById("ruleSymbol")
        };

        document
            .querySelectorAll(".password-toggle")
            .forEach(function (button) {
                button.addEventListener("click", function () {
                    const target =
                        document.getElementById(
                            button.dataset.target
                        );

                    const icon =
                        button.querySelector("i");

                    const isHidden =
                        target.type === "password";

                    target.type =
                        isHidden ? "text" : "password";

                    icon.classList.toggle(
                        "fa-eye",
                        !isHidden
                    );

                    icon.classList.toggle(
                        "fa-eye-slash",
                        isHidden
                    );

                    button.setAttribute(
                        "aria-label",
                        isHidden
                            ? "Hide password"
                            : "Show password"
                    );
                });
            });

        function markRule(element, isValid) {
            if (!element) {
                return;
            }

            element.classList.toggle("valid", isValid);

            const icon =
                element.querySelector("i");

            icon.className =
                isValid
                    ? "fa-solid fa-circle-check"
                    : "fa-solid fa-circle";
        }

        function evaluatePassword(value) {
            const checks = {
                length: value.length >= 8,
                upper: /[A-Z]/.test(value),
                lower: /[a-z]/.test(value),
                number: /\d/.test(value),
                symbol: /[^A-Za-z0-9]/.test(value)
            };

            Object.keys(checks).forEach(function (rule) {
                markRule(rules[rule], checks[rule]);
            });

            const score =
                Object.values(checks)
                    .filter(Boolean)
                    .length;

            const widths = [
                "0%",
                "20%",
                "40%",
                "60%",
                "80%",
                "100%"
            ];

            strengthBar.style.width =
                value.length === 0
                    ? "0%"
                    : widths[score];

            if (value.length === 0) {
                strengthBar.style.background = "#9b9b9b";

                strengthText.textContent =
                    "Use at least 8 characters with uppercase, lowercase, a number and a symbol.";

                return checks;
            }

            if (score <= 2) {
                strengthBar.style.background = "#b42318";
                strengthText.textContent = "Weak password";
            } else if (score <= 4) {
                strengthBar.style.background = "#9a6700";
                strengthText.textContent = "Medium password";
            } else {
                strengthBar.style.background = "#217a44";
                strengthText.textContent = "Strong password";
            }

            return checks;
        }

        function validateMatch() {
            const matches =
                password.value === confirmPassword.value;

            const shouldShow =
                confirmPassword.value.length > 0 &&
                !matches;

            passwordError.classList.toggle(
                "visible",
                shouldShow
            );

            confirmPassword.setCustomValidity(
                matches
                    ? ""
                    : "Passwords do not match."
            );

            return matches;
        }

        if (password) {
            password.addEventListener(
                "input",
                function () {
                    evaluatePassword(password.value);
                    validateMatch();
                }
            );
        }

        if (confirmPassword) {
            confirmPassword.addEventListener(
                "input",
                validateMatch
            );
        }

        if (resetForm) {
            resetForm.addEventListener(
                "submit",
                function (event) {
                    const checks =
                        evaluatePassword(password.value);

                    const strongEnough =
                        Object.values(checks).every(Boolean);

                    const passwordsMatch =
                        validateMatch();

                    if (
                        !strongEnough ||
                        !passwordsMatch ||
                        !resetForm.checkValidity()
                    ) {
                        event.preventDefault();
                        resetForm.reportValidity();
                    }
                }
            );
        }
    </script>

</body>
</html>
