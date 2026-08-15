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
         NOTIFICATION AREA
    ========================================================= -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- =========================================================
         SIGNUP
    ========================================================= -->
    <main class="auth">


        <!-- LOGO -->
        <div class="logo">
            <div class="logo-symbol">
                 <img src="assets/images/elitefort.png" alt="EliteFort Logo" style="max-height: 60px; width: auto; height: auto;">
            </div>
            <div class="brand-name">ELITEFORT</div>
        </div>


        <!-- HEADER -->
        <div class="heading">
            <h1>Create <span>account</span></h1>
            <p>Enter your details to create your account</p>
        </div>

        <!-- FORM -->
        <form method="POST" id="signupForm">

            <!-- FULL NAME -->
            <div class="field">
                <div class="field-top">
                    <label for="full_name">Full name</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-person-vcard input-icon"></i>
                    <input type="text" name="full_name" id="full_name" class="input" placeholder="Enter your full name" autocomplete="name" required>
                </div>
            </div>

            <!-- USERNAME -->
            <div class="field">
                <div class="field-top">
                    <label for="username">Username</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" name="username" id="username" class="input" placeholder="Choose a username" autocomplete="username" required>
                </div>
            </div>

            <!-- EMAIL -->
            <div class="field">
                <div class="field-top">
                    <label for="email">Email address</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email" id="email" class="input" placeholder="Enter your email" autocomplete="email" required>
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="field">
                <div class="field-top">
                    <label for="password">Password</label>
                </div>
                <div class="input-container">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="input" placeholder="Create a password" autocomplete="new-password" required>
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

            <!-- TERMS -->
            <div class="terms">
                <input type="checkbox" name="terms" id="termsCheckbox" value="1" required>
                <span>
                    I agree to the
                    <button type="button" class="policy-link" data-policy="terms">Terms</button>
                    and
                    <button type="button" class="policy-link" data-policy="privacy">Privacy Policy</button>
                </span>
            </div>

            <!-- SUBMIT -->
            <button type="submit" name="signup" class="create-button">Create account</button>
        </form>

        <!-- LOGIN -->
        <div class="login-link">
            Already have an account?
            <a href="login.php">Sign in</a>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <span>EliteFort</span>
            <span class="footer-dot"></span>
            <span>Authentication</span>
        </div>

    </main>

    <!-- =========================================================
         TERMS / PRIVACY MODAL
    ========================================================= -->
    <div class="policy-modal" id="policyModal">
        <div class="policy-backdrop" id="policyBackdrop"></div>

        <div class="policy-box">
            <!-- HEADER -->
            <div class="policy-header">
                <div>
                    <div class="policy-brand">ELITEFORT</div>
                    <h3 id="policyTitle">Terms</h3>
                </div>
                <button type="button" class="policy-close" id="policyClose" aria-label="Close policy">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- TABS -->
            <div class="policy-tabs">
                <button type="button" class="policy-tab active" data-tab="terms">Terms</button>
                <button type="button" class="policy-tab" data-tab="privacy">Privacy</button>
            </div>

            <!-- CONTENT -->
            <div class="policy-content" id="policyContent">
                <!-- TERMS -->
                <div class="policy-page active" id="termsContent">
                    <h4>Terms of Service</h4>
                    <p>By creating an EliteFort account, you agree to these terms and to use the service responsibly.</p>
                    <h5>Account Responsibility</h5>
                    <p>You are responsible for maintaining the confidentiality of your account credentials and activity performed using your account.</p>
                    <h5>Acceptable Use</h5>
                    <p>EliteFort accounts must not be used for unlawful, fraudulent, abusive, disruptive or unauthorized activities.</p>
                    <h5>Account Security</h5>
                    <p>Use a strong password and take reasonable precautions to prevent unauthorized access to your account.</p>
                    <h5>Account Suspension</h5>
                    <p>EliteFort may restrict or suspend accounts where misuse, security threats or violations are identified.</p>
                    <h5>Changes</h5>
                    <p>These terms may be updated as EliteFort services and policies evolve.</p>
                </div>

                <!-- PRIVACY -->
                <div class="policy-page" id="privacyContent">
                    <h4>Privacy Policy</h4>
                    <p>This policy explains how information associated with your EliteFort account may be collected and used.</p>
                    <h5>Information Collected</h5>
                    <p>EliteFort may collect information such as your name, username, email address and technical authentication information.</p>
                    <h5>How Information Is Used</h5>
                    <p>Information may be used to create and manage accounts, authenticate users and protect EliteFort services.</p>
                    <h5>Authentication Data</h5>
                    <p>Passwords should be protected using secure password hashing rather than being stored as plain text.</p>
                    <h5>Security Information</h5>
                    <p>Technical information such as IP addresses, browser information and security events may be processed to protect user accounts.</p>
                    <h5>Policy Updates</h5>
                    <p>This Privacy Policy may be updated as EliteFort services and security practices change.</p>
                </div>
            </div>

            <!-- DONE -->
            <div class="policy-footer">
                <button type="button" class="policy-done" id="policyDone">Done</button>
            </div>
        </div>
    </div>

    <script>
        /* =========================================================
           PASSWORD SHOW / HIDE
        ========================================================= */
        const eyeButtons = document.querySelectorAll('.eye');

        eyeButtons.forEach(button => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.target);
                const icon = button.querySelector('i');
                const hidden = input.type === 'password';

                input.type = hidden ? 'text' : 'password';
                icon.classList.toggle('bi-eye', !hidden);
                icon.classList.toggle('bi-eye-slash', hidden);
                button.setAttribute('aria-label', hidden ? 'Hide password' : 'Show password');
            });
        });

        /* =========================================================
           REUSABLE NOTIFICATION
        ========================================================= */
        function showToast(type, message, title = null) {
            const container = document.getElementById('toastContainer');

            if (type !== 'error' && type !== 'success') {
                type = 'error';
            }

            if (!title) {
                title = type === 'success' ? 'Completed' : 'Unable to continue';
            }

            // Remove existing notification
            const oldToast = container.querySelector('.toast');
            if (oldToast) {
                oldToast.remove();
            }

            // Build notification
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

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.add('show');
            });

            // Close
            function dismissToast() {
                clearTimeout(dismissTimer);
                toast.classList.remove('show');
                toast.classList.add('closing');
                setTimeout(() => {
                    toast.remove();
                }, 280);
            }

            // Manual dismiss
            closeButton.addEventListener('click', dismissToast);

            // Auto close
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
           PASSWORD MATCH
        ========================================================= */
        const signupForm = document.getElementById('signupForm');

        signupForm.addEventListener('submit', event => {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                showToast('error', 'Please enter the same password in both password fields.', 'Passwords do not match');
            }
        });

        /* =========================================================
           PHP ERROR / SUCCESS
        ========================================================= */
        document.addEventListener('DOMContentLoaded', () => {
            <?php if (!empty($error)): ?>
                showToast('error', <?= json_encode($error, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Registration failed');
            <?php elseif (!empty($success)): ?>
                showToast('success', <?= json_encode($success, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'Account created');
            <?php endif; ?>
        });

        /* =========================================================
           TERMS / PRIVACY MODAL
        ========================================================= */
        const modal = document.getElementById('policyModal');
        const modalTitle = document.getElementById('policyTitle');
        const policyContent = document.getElementById('policyContent');
        const closeButton = document.getElementById('policyClose');
        const doneButton = document.getElementById('policyDone');
        const backdrop = document.getElementById('policyBackdrop');
        const policyLinks = document.querySelectorAll('.policy-link');
        const tabs = document.querySelectorAll('.policy-tab');
        const termsContent = document.getElementById('termsContent');
        const privacyContent = document.getElementById('privacyContent');

        function switchPolicy(type) {
            tabs.forEach(tab => {
                tab.classList.toggle('active', tab.dataset.tab === type);
            });

            termsContent.classList.toggle('active', type === 'terms');
            privacyContent.classList.toggle('active', type === 'privacy');

            modalTitle.textContent = type === 'terms' ? 'Terms' : 'Privacy Policy';
            policyContent.scrollTop = 0;
        }

        function openPolicy(type) {
            switchPolicy(type);
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closePolicy() {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        policyLinks.forEach(link => {
            link.addEventListener('click', () => {
                openPolicy(link.dataset.policy);
            });
        });

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                switchPolicy(tab.dataset.tab);
            });
        });

        closeButton.addEventListener('click', closePolicy);
        doneButton.addEventListener('click', closePolicy);
        backdrop.addEventListener('click', closePolicy);

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                closePolicy();
            }
        });
    </script>

</body>
</html>