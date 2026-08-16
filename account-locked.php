<?php
include "_core/__init__.php";

$error   = $error ?? '';
$success = $success ?? '';
?>

<?= loadTemplates('_head'); ?>

<body>

    <!-- =====================================================
         BACKGROUND
    ===================================================== -->
    <div class="background">
        <div class="blur blur-one"></div>
        <div class="blur blur-two"></div>
        <div class="blur blur-three"></div>
        <div class="blur blur-four"></div>
    </div>

    <!-- =====================================================
         NOTIFICATION
    ===================================================== -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- =====================================================
         ACCOUNT LOCKED
    ===================================================== -->
    <main class="auth">


        <!-- LOGO -->
        <!-- <div class="logo">
            <div class="logo-symbol">
                 <img src="assets/images/elitefort.png" alt="EliteFort Logo" style="max-height: 60px; width: auto; height: auto;">
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div> -->

        <!-- LOCK -->
        <div class="lock-symbol">
            <i class="bi bi-lock-fill"></i>
        </div>

        <!-- HEADING -->
        <div class="heading">
            <h1>Account <span>locked</span></h1>
            <p>
                Your account has been temporarily locked
                to protect it from unauthorized access.
            </p>
        </div>

        <!-- LOCK INFO -->
        <div class="lock-info">
            <div class="lock-info-icon">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="lock-info-content">
                <span class="lock-info-title">Security protection activated</span>
                <span class="lock-info-message">
                    Too many unsuccessful sign-in attempts
                    were detected. Please wait before trying
                    again.
                </span>
            </div>
        </div>

        <!-- LOCK TIMER -->
        <div class="timer-box">
            <div class="timer-left">
                <i class="bi bi-clock"></i>
                <span>Try again in</span>
            </div>
            <strong class="timer" id="lockTimer">15:00</strong>
        </div>

        <!-- LOGIN -->
        <a href="login.php" class="primary-button" id="loginButton">
            <i class="bi bi-box-arrow-in-right"></i>
            Return to sign in
        </a>

        <!-- FORGOT PASSWORD -->
        <a href="forgot-password.php" class="secondary-button">
            <i class="bi bi-key"></i>
            Reset your password
        </a>

        <!-- HELP -->
        <div class="help">
            Didn't try to sign in?
            <a href="forgot-password.php">Secure your account</a>
        </div>

        <!-- FOOTER -->
        <?= loadTemplates('_footer'); ?>


    </main>

    <script>
        /* =====================================================
           LOCK COUNTDOWN
        ===================================================== */
        const lockTimer = document.getElementById('lockTimer');

        // Example: 15 minutes. This is visual only.
        let remainingSeconds = 15 * 60;

        function updateLockTimer() {
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            lockTimer.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

            if (remainingSeconds <= 0) {
                clearInterval(lockInterval);
                lockTimer.textContent = 'Unlocked';
                lockTimer.classList.add('expired');
                showToast('success', 'You can try signing in to your account again.', 'Account unlocked');
                return;
            }
            remainingSeconds--;
        }

        updateLockTimer();
        const lockInterval = setInterval(updateLockTimer, 1000);

        /* =====================================================
           TOAST
        ===================================================== */
        function showToast(type, message, title = null) {
            const container = document.getElementById('toastContainer');
            const existing = container.querySelector('.toast');

            if (existing) {
                existing.remove();
            }

            if (type !== 'error' && type !== 'success') {
                type = 'error';
            }

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const state = type === 'success' ? 'Success' : 'Security';
            const icon = type === 'success' ? 'bi-check-lg' : 'bi-shield-exclamation';

            if (!title) {
                title = type === 'success' ? 'Completed' : 'Account locked';
            }

            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="bi ${icon}"></i>
                </div>
                <div class="toast-content">
                    <span class="toast-state">${state}</span>
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button type="button" class="toast-close">
                    <span>Dismiss</span>
                    <i class="bi bi-x-lg"></i>
                </button>
            `;

            toast.querySelector('.toast-state').textContent = state;
            toast.querySelector('.toast-title').textContent = title;
            toast.querySelector('.toast-message').textContent = message;

            container.appendChild(toast);

            const closeButton = toast.querySelector('.toast-close');
            let timer;

            requestAnimationFrame(() => {
                toast.classList.add('show');
            });

            function closeToast() {
                clearTimeout(timer);
                toast.classList.remove('show');
                toast.classList.add('closing');
                setTimeout(() => {
                    toast.remove();
                }, 280);
            }

            closeButton.addEventListener('click', closeToast);
            timer = setTimeout(closeToast, 6000);
        }

        /* =====================================================
           PHP ERROR / SUCCESS
        ===================================================== */
        document.addEventListener('DOMContentLoaded', () => {
            <?php if (!empty($error)): ?>
                showToast('error', <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Account locked');
            <?php elseif (!empty($success)): ?>
                showToast('success', <?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Account available');
            <?php endif; ?>
        });
    </script>
    <script href></script>

</body>
</html>
