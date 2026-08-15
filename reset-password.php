<?php
include "_core/__init__.php";

$error   = $error ?? '';
$success = $success ?? '';
?>

<?= loadTemplates('_head'); ?>


<body>

    <!-- BACKGROUND -->
    <div class="background">
        <div class="blur blur-one"></div>
        <div class="blur blur-two"></div>
        <div class="blur blur-three"></div>
        <div class="blur blur-four"></div>
    </div>

    <!-- TOAST -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- RESET PASSWORD -->
    <main class="auth">


        <!-- LOGO -->
        <!-- <div class="logo">
            <div class="logo-symbol">
                 <img src="assets/images/elitefort.png" alt="EliteFort Logo" style="max-height: 60px; width: auto; height: auto;">
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div>
 -->

        <!-- ICON -->
        <div class="reset-symbol">
            <i class="bi bi-shield-lock"></i>
        </div>

        <!-- HEADING -->
        <div class="heading">
            <h1>Reset <span>password</span></h1>
            <p>
                Create a new password for your
                EliteFort account.
            </p>
        </div>

        <!-- FORM -->
        <form method="POST" id="resetForm">

            <!-- PASSWORD -->
            <div class="field">
                <div class="field-top">
                    <label for="password">New password</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="input" placeholder="Enter new password" autocomplete="new-password" required>
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

            <!-- SECURITY INFO -->
            <div class="password-info">
                <i class="bi bi-info-circle"></i>
                <span>
                    Use a strong password that you
                    haven't used on this account before.
                </span>
            </div>

            <!-- BUTTON -->
            <button type="submit" name="reset_password" class="reset-button">
                Update password
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <!-- BACK -->
        <div class="back-login">
            <a href="login.php">
                <i class="bi bi-arrow-left"></i>
                Back to sign in
            </a>
        </div>

        <div class="footer">
            <span>EliteFort</span>
            <span class="footer-dot"></span>
            <span>Authentication</span>
        </div>

    </main>

    <script>
        /* =============================================
           PASSWORD SHOW/HIDE
        ============================================= */
        const eyeButtons = document.querySelectorAll('.eye');

        eyeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.target);
                const icon = button.querySelector('i');
                const hidden = input.type === 'password';

                input.type = hidden ? 'text' : 'password';
                icon.classList.toggle('bi-eye', !hidden);
                icon.classList.toggle('bi-eye-slash', hidden);
            });
        });

        /* =============================================
           TOAST
        ============================================= */
        function showToast(type, message, title = null) {
            const container = document.getElementById('toastContainer');
            const existing = container.querySelector('.toast');

            if (existing) {
                existing.remove();
            }

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icon = type === 'success' ? 'bi-check-lg' : 'bi-exclamation-lg';
            const state = type === 'success' ? 'Success' : 'Error';

            if (!title) {
                title = type === 'success' ? 'Completed' : 'Unable to continue';
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
                setTimeout(() => toast.remove(), 280);
            }

            closeButton.addEventListener('click', closeToast);
            timer = setTimeout(closeToast, 6000);
        }

        /* =============================================
           PASSWORD MATCH
        ============================================= */
        const resetForm = document.getElementById('resetForm');

        resetForm.addEventListener('submit', event => {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                showToast('error', 'New password and confirm password must be the same.', 'Passwords do not match');
            }
        });

        /* =============================================
           PHP ERROR / SUCCESS
        ============================================= */
        document.addEventListener('DOMContentLoaded', () => {
            <?php if (!empty($error)): ?>
                showToast('error', <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Password reset failed');
            <?php elseif (!empty($success)): ?>
                showToast('success', <?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Password updated');
            <?php endif; ?>
        });
    </script>

</body>
</html>