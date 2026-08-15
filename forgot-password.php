<?php
include "_core/__init__.php";

$error   = $error ?? '';
$success = $success ?? '';
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
         NOTIFICATION
    ========================================================= -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- =========================================================
         FORGOT PASSWORD
    ========================================================= -->
    <main class="auth">

        <!-- LOGO -->
        <!-- <div class="logo">
            <div class="logo-symbol">
                 <img src="assets/images/elitefort.png" alt="EliteFort Logo" style="max-height: 60px; width: auto; height: auto;">
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div> -->


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
        <?= loadTemplates('_footer'); ?>


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