<?php
include "_core/__init__.php";

if (Session::validate()) {
    header("Location: index.php");
    exit;
}

$error   = $error ?? '';
// $success = $success ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    try {
        Csrf::protect();    

        $fullName = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $cPassword = $_POST['confirm_password'] ?? '';

        $userId = Auth::signup(
            $fullName,
            $username,
            $email,
            $password,
            $cPassword
        );
        AuditLog::signupSuccess($userId);
        Otp::createForUser(
            $userId
        );
        
        Session::set(
            'pending_verification_user_id',
            $userId
        );
        header(
            'Location: otp-verify.php'
        );
        exit;

    } catch (Exception $e) {
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
    
            <?=Csrf::input() ?>
  
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
        <?= loadTemplates('_footer'); ?>


    </main>

    <!-- =========================================================
         TERMS / PRIVACY MODAL
    ========================================================= -->
        <?= loadTemplates('_term&privacy');?>
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