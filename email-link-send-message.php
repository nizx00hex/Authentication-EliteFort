<?php
include "_core/__init__.php";

$email = $email ?? 'nisath.sec@gmail.com';
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

    <!-- PAGE -->
    <main class="auth">


        <!-- LOGO -->
        <!-- <div class="logo">
            <div class="logo-symbol">
                 <img src="assets/images/elitefort.png" alt="EliteFort Logo" style="max-height: 60px; width: auto; height: auto;">
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div> -->


        <!-- STATUS ICON -->
        <div class="mail-status">
            <i class="bi bi-envelope-paper"></i>
            <div class="status-check">
                <i class="bi bi-check-lg"></i>
            </div>
        </div>

        <!-- HEADING -->
        <div class="heading">
            <h1>Check your <span>email</span></h1>
            <p>
                We've sent password reset instructions
                to the email address associated with
                your account.
            </p>
        </div>

        <!-- EMAIL -->
        <?php if (!empty($email)): ?>
            <div class="email-box">
                <div class="email-icon">
                    <i class="bi bi-envelope"></i>
                </div>
                <div class="email-content">
                    <span class="email-label">Reset link sent to</span>
                    <span class="email-address">
                        <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <!-- INFORMATION -->
        <div class="notice">
            <i class="bi bi-info-circle"></i>
            <span>
                Open the email and select the
                <strong>reset password link</strong>
                to create a new password.

                If you don't see the email,
                check your spam or junk folder.
            </span>
        </div>

        <!-- OPEN MAIL -->
        <a href="mailto:" class="primary-button">
            Open email app
            <i class="bi bi-box-arrow-up-right"></i>
        </a>

        <!-- RESEND -->
        <div class="resend">
            Didn't receive the email?
            <button type="button" class="resend-button" id="resendButton" disabled>
                Resend in 60s
            </button>
        </div>

        <!-- LOGIN -->
        <div class="back-login">
            <a href="login.php">
                <i class="bi bi-arrow-left"></i>
                Back to sign in
            </a>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <span>EliteFort</span>
            <span class="footer-dot"></span>
            <span>Authentication</span>
        </div>

    </main>

    <script>
        /* =========================================================
           RESEND COOLDOWN
        ========================================================= */
        const resendButton = document.getElementById('resendButton');
        let seconds = 60;

        const resendTimer = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(resendTimer);
                resendButton.disabled = false;
                resendButton.textContent = 'Resend link';
                return;
            }
            resendButton.textContent = `Resend in ${seconds}s`;
        }, 1000);

        /* =========================================================
           RESEND LINK
        ========================================================= */
        resendButton.addEventListener('click', () => {
            if (resendButton.disabled) {
                return;
            }
            // Example PHP route: forgot-password.php?action=resend
            window.location.href = 'forgot-password.php?action=resend';
        });
    </script>

</body>
</html>