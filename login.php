<?php

declare(strict_types=1);

include "_core/__init__.php";

$error = null;
$success = null;


if (Session::validate()) {
    header("Location: index.php");
    exit;
}

if (!Session::isAuthenticated() && RememberMe::exists()) {
    try {

        $rememberedUser = RememberMe::authenticate();

        if ($rememberedUser !== null) {
            AuditLog::rememberUsed((int) $rememberedUser['id']);
            header("Location: index.php");
            exit;
        }

    } catch (Throwable $e) {
        $error = error_log( 'Remember Me authentication failed: ' . $e->getMessage());
    }
}


$flash = Session::getFlash();

if ($flash !== null) {

    if ($flash['type'] === 'success') {
        $success = $flash['message'];
    }

    if ($flash['type'] === 'error') {
        $error = $flash['message'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        Csrf::protect();

        $identifier = trim($_POST['user'] ?? '');

        $password = $_POST['password'] ?? '';

        $user = Auth::login($identifier, $password);

        Session::login($user);

        $userId = (int) $user['id'];

        AuditLog::loginSuccess($userId);
        AuditLog::sessionCreated($userId);

        if (!empty($_POST['remember'])) {
            RememberMe::create($userId);
            AuditLog::rememberCreated($userId);
        }

        Csrf::regenerate();
        
        header("Location: index.php");
        exit;

    } catch (Throwable $e) {

        $error = $e->getMessage();
    }
}
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
         NOTIFICATION AREA
    ========================================================= -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- =========================================================
         LOGIN
    ========================================================= -->
    <main class="auth">

        <!-- LOGO -->
        <div class="logo">
            <div class="logo-symbol">
                 <img src="assets/images/elitefort.png" alt="EliteFort Logo" style="max-height: 60px; width: auto; height: auto;">
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div>

        <!-- HEADING -->
        <div class="heading">
            <h1>Sign <span>in</span></h1>
            <p>Enter your credentials to access your account</p>
        </div>

        <!-- LOGIN FORM -->
        <form method="POST" id="loginForm">

            <?=Csrf::input() ?>

            <!-- EMAIL / USERNAME -->
            <div class="field">
                <div class="field-top">
                    <label for="user">Email or username</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" name="user" id="user" class="input" placeholder="Enter your email or username" autocomplete="username" required>
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="field">
                <div class="field-top">
                    <label for="password">Password</label>
                    <a href="forgot-password.php" class="forgot">Forgot password?</a>
                </div>
                <div class="input-container">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="input" placeholder="Enter your password" autocomplete="current-password" required>
                    <button type="button" class="eye" id="passwordToggle" aria-label="Show password">
                        <i class="bi bi-eye" id="passwordEye"></i>
                    </button>
                </div>
            </div>

            <!-- REMEMBER -->
            <div class="remember-row">
                <label class="remember">
                    <input type="checkbox" name="remember" value="1">
                    <span>Remember me</span>
                </label>
            </div>

            <!-- LOGIN BUTTON -->
            <button type="submit" name="login" class="login-button">Continue</button>
        </form>

        <!-- SIGN UP -->
        <div class="signup-link">
            New to EliteFort?
            <a href="signup.php">Create an account</a>
        </div>

        <!-- FOOTER -->
        <?= loadTemplates('_footer'); ?>

    </main>

    <script>
        /* =========================================================
           PASSWORD VISIBILITY
        ========================================================= */
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordEye = document.getElementById('passwordEye');

        passwordToggle.addEventListener('click', () => {
            const hidden = passwordInput.type === 'password';
            passwordInput.type = hidden ? 'text' : 'password';
            passwordEye.classList.toggle('bi-eye', !hidden);
            passwordEye.classList.toggle('bi-eye-slash', hidden);
            passwordToggle.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
        });

        /* =========================================================
           REUSABLE TOAST
        ========================================================= */
        function showToast(type, message, title = null) {
            const container = document.getElementById('toastContainer');

            if (type !== 'success' && type !== 'error') {
                type = 'error';
            }

            if (!title) {
                title = type === 'success' ? 'Completed' : 'Unable to continue';
            }

            // Remove previous notification
            const existing = container.querySelector('.toast');
            if (existing) {
                existing.remove();
            }

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const state = type === 'success' ? 'Success' : 'Error';
            const iconClass = type === 'success' ? 'bi-check-lg' : 'bi-exclamation-lg';

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

            // Open
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

            closeButton.addEventListener('click', dismissToast);

            // Auto dismiss after 6 seconds
            function startTimer() {
                dismissTimer = setTimeout(dismissToast, 6000);
            }
            startTimer();

            // Pause while user reads it
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
                showToast('error', <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Sign in failed');
            <?php elseif (!empty($success)): ?>
                showToast('success', <?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Success');
            <?php endif; ?>
        });
    </script>

</body>
</html>