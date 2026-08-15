
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

    <!-- SESSION EXPIRED -->
    <main class="auth">


        <!-- LOGO
        <div class="logo">
            <div class="logo-symbol">
                 <img src="assets/images/elitefort.png" alt="EliteFort Logo" style="max-height: 60px; width: auto; height: auto;">
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div> -->

        <!-- SESSION ICON -->
        <div class="session-symbol">
            <i class="bi bi-clock-history"></i>
        </div>

        <!-- HEADING -->
        <div class="heading">
            <h1>Session <span>expired</span></h1>
            <p>
                Your sign-in session has expired.
                Please sign in again to continue
                accessing your EliteFort account.
            </p>
        </div>

        <!-- INFORMATION -->
        <div class="session-info">
            <div class="session-info-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <div class="session-info-content">
                <span class="session-info-title">You were signed out securely</span>
                <span class="session-info-message">
                    Your session ended because it was
                    inactive for too long or reached its
                    allowed lifetime.
                </span>
            </div>
        </div>

        <!-- LOGIN -->
        <a href="login.php" class="primary-button">
            <i class="bi bi-box-arrow-in-right"></i>
            Sign in again
        </a>

        <!-- HOME -->
        <a href="index.php" class="secondary-button">
            <i class="bi bi-house"></i>
            Return to home
        </a>

        <!-- SECURITY NOTE -->
        <div class="security-note">
            <i class="bi bi-info-circle"></i>
            <span>
                Session expiration helps protect your
                account if you leave your browser
                unattended.
            </span>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <span>EliteFort</span>
            <span class="footer-dot"></span>
            <span>Authentication</span>
        </div>

    </main>

    <script>
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

            const state = type === 'success' ? 'Success' : 'Session';
            const icon = type === 'success' ? 'bi-check-lg' : 'bi-clock-history';

            if (!title) {
                title = type === 'success' ? 'Success' : 'Session expired';
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
           PHP MESSAGES
        ===================================================== */
        document.addEventListener('DOMContentLoaded', () => {
            <?php if (!empty($error)): ?>
                showToast('error', <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Session expired');
            <?php elseif (!empty($success)): ?>
                showToast('success', <?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Session updated');
            <?php endif; ?>
        });
    </script>

</body>
</html>