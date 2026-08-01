<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Matte Black · Premium Signup</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
  <link rel="stylesheet" href="assets/css/signup.css">
</head>

<body>
  <main class="signup-card" role="main" aria-label="Signup panel">
    <div class="corner-deco"></div>

    <header class="card-header">
      <h1>Create account</h1>
      <p>Enter your information to get started</p>
    </header>

    <form
      class="signup-form"
      id="signupForm"
      action="#"
      method="post"
      novalidate
    >
      <div class="input-group">
        <input
          type="text"
          id="fullName"
          name="full_name"
          placeholder="Full name"
          autocomplete="name"
          minlength="2"
          required
        />
        <i class="fas fa-user input-icon"></i>
      </div>

      <div class="input-group">
        <input
          type="text"
          id="username"
          name="username"
          placeholder="Username"
          autocomplete="username"
          minlength="3"
          required
        />
        <i class="fas fa-at input-icon"></i>
      </div>

      <div class="input-group">
        <input
          type="email"
          id="email"
          name="email"
          placeholder="Email address"
          autocomplete="email"
          required
        />
        <i class="fas fa-envelope input-icon"></i>
      </div>

      <div class="input-group" id="passwordGroup">
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Create password"
          autocomplete="new-password"
          minlength="8"
          required
        />
        <i class="fas fa-lock input-icon"></i>

        <button
          type="button"
          class="password-toggle"
          data-target="password"
          aria-label="Show password"
        >
          <i class="fas fa-eye"></i>
        </button>
      </div>

      <p class="password-help">
        Use at least 8 characters.
      </p>

      <div class="input-group" id="confirmPasswordGroup">
        <input
          type="password"
          id="confirmPassword"
          name="confirm_password"
          placeholder="Confirm password"
          autocomplete="new-password"
          minlength="8"
          required
        />
        <i class="fas fa-shield-alt input-icon"></i>

        <button
          type="button"
          class="password-toggle"
          data-target="confirmPassword"
          aria-label="Show confirm password"
        >
          <i class="fas fa-eye"></i>
        </button>
      </div>

      <p id="passwordMessage" class="field-message" aria-live="polite"></p>

      <div class="form-options">
        <div class="terms-row">
          <input
            type="checkbox"
            id="terms"
            name="terms"
            value="1"
            required
          />

          <div class="terms-copy">
            <label for="terms">I agree to the </label>
            <button
              type="button"
              class="policy-link"
              data-modal-target="termsModal"
            >Terms of Service</button>
            <span> and </span>
            <button
              type="button"
              class="policy-link"
              data-modal-target="privacyModal"
            >Privacy Policy</button><span>.</span>
          </div>
        </div>
      </div>

      <button type="submit" class="signup-btn">
        <span>Create account</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <div class="divider">or sign up with</div>

    <div class="social-row">
      <a href="#" aria-label="Sign up with Google">
        <i class="fab fa-google"></i>
        Google
      </a>

      <a href="#" aria-label="Sign up with Apple">
        <i class="fab fa-apple"></i>
        Apple
      </a>
    </div>

    <footer class="card-footer">
      Already have an account? <a href="login.php">Sign in</a>
    </footer>
  </main>


  <!-- Terms of Service popup -->
  <div
    class="policy-modal"
    id="termsModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="termsModalTitle"
    aria-hidden="true"
  >
    <section class="policy-modal-card" role="document">
      <header class="policy-modal-header">
        <h2 id="termsModalTitle">Terms of Service</h2>
        <button type="button" class="modal-close-icon" data-modal-close aria-label="Close Terms of Service">
          <i class="fas fa-times"></i>
        </button>
      </header>

      <div class="policy-modal-body">
        <p class="policy-date">Last updated: July 26, 2026</p>

        <h3>1. Acceptance</h3>
        <p>
          By creating an account or using this service, the user agrees to these
          Terms of Service. The service should not be used when these terms are
          not accepted.
        </p>

        <h3>2. Account responsibility</h3>
        <p>
          Accurate registration information must be provided. Account passwords
          and activities performed through the account remain the responsibility
          of the account holder.
        </p>

        <h3>3. Acceptable use</h3>
        <ul>
          <li>Do not use the service for unlawful, abusive or fraudulent activity.</li>
          <li>Do not attempt to access another user's account or restricted systems.</li>
          <li>Do not upload malicious code or disrupt the service.</li>
        </ul>

        <h3>4. Suspension and termination</h3>
        <p>
          Access may be limited or terminated when these terms are violated,
          security is threatened or misuse is detected.
        </p>

        <h3>5. Service availability</h3>
        <p>
          Features may be updated, changed or temporarily unavailable. Reasonable
          efforts may be made to keep the service reliable, but uninterrupted
          availability is not guaranteed.
        </p>

        <h3>6. Changes</h3>
        <p>
          These terms may be updated when the service or legal requirements
          change. Continued use after an update means the revised terms are
          accepted.
        </p>

        <h3>7. Contact</h3>
        <p>
          Replace this section with the official business name, support email and
          applicable jurisdiction before publishing.
        </p>
      </div>

      <footer class="policy-modal-footer">
        <button type="button" class="modal-done-btn" data-modal-close>
          Close
        </button>
      </footer>
    </section>
  </div>

  <!-- Privacy Policy popup -->
  <div
    class="policy-modal"
    id="privacyModal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="privacyModalTitle"
    aria-hidden="true"
  >
    <section class="policy-modal-card" role="document">
      <header class="policy-modal-header">
        <h2 id="privacyModalTitle">Privacy Policy</h2>
        <button type="button" class="modal-close-icon" data-modal-close aria-label="Close Privacy Policy">
          <i class="fas fa-times"></i>
        </button>
      </header>

      <div class="policy-modal-body">
        <p class="policy-date">Last updated: July 26, 2026</p>

        <h3>1. Information collected</h3>
        <p>
          Registration details such as name, username and email address may be
          collected. Technical information may also be recorded for security and
          service operation.
        </p>

        <h3>2. How information is used</h3>
        <ul>
          <li>To create and manage user accounts.</li>
          <li>To authenticate users and protect the service.</li>
          <li>To provide support and important account notices.</li>
          <li>To improve reliability, performance and user experience.</li>
        </ul>

        <h3>3. Sharing</h3>
        <p>
          Personal information is not sold. Information may be shared with trusted
          service providers when necessary to operate the service or when required
          by law.
        </p>

        <h3>4. Data security</h3>
        <p>
          Reasonable technical and organisational safeguards should be used to
          protect stored information. No online system can guarantee absolute
          security.
        </p>

        <h3>5. Data retention</h3>
        <p>
          Information should be retained only for as long as required to provide
          the service, meet legal duties or resolve disputes.
        </p>

        <h3>6. User choices</h3>
        <p>
          Users may request access, correction or deletion of personal information,
          subject to applicable legal and operational requirements.
        </p>

        <h3>7. Contact</h3>
        <p>
          Replace this section with the official privacy contact, business details
          and any rights required by the laws that apply to the service.
        </p>
      </div>

      <footer class="policy-modal-footer">
        <button type="button" class="modal-done-btn" data-modal-close>
          Close
        </button>
      </footer>
    </section>
  </div>

  <script>

    const policyLinks = document.querySelectorAll('[data-modal-target]');
    const policyModals = document.querySelectorAll('.policy-modal');
    let activeModal = null;
    let modalTrigger = null;

    function openPolicyModal(modal, trigger) {
      if (!modal) return;

      activeModal = modal;
      modalTrigger = trigger;
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');

      const closeButton = modal.querySelector('[data-modal-close]');
      closeButton?.focus();
    }

    function closePolicyModal(modal) {
      if (!modal) return;

      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
      activeModal = null;

      modalTrigger?.focus();
      modalTrigger = null;
    }

    policyLinks.forEach((link) => {
      link.addEventListener('click', () => {
        openPolicyModal(document.getElementById(link.dataset.modalTarget), link);
      });
    });

    policyModals.forEach((modal) => {
      modal.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => closePolicyModal(modal));
      });

      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          closePolicyModal(modal);
        }
      });
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && activeModal) {
        closePolicyModal(activeModal);
      }
    });

    const signupForm = document.getElementById('signupForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const confirmPasswordGroup = document.getElementById('confirmPasswordGroup');
    const passwordMessage = document.getElementById('passwordMessage');
    const toggleButtons = document.querySelectorAll('.password-toggle');

    toggleButtons.forEach((button) => {
      button.addEventListener('click', () => {
        const targetId = button.dataset.target;
        const input = document.getElementById(targetId);
        const icon = button.querySelector('i');
        const passwordIsHidden = input.type === 'password';

        input.type = passwordIsHidden ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !passwordIsHidden);
        icon.classList.toggle('fa-eye-slash', passwordIsHidden);

        button.setAttribute(
          'aria-label',
          passwordIsHidden ? 'Hide password' : 'Show password'
        );
      });
    });

    function validatePasswordMatch() {
      const password = passwordInput.value;
      const confirmPassword = confirmPasswordInput.value;

      confirmPasswordGroup.classList.remove('has-error');
      passwordMessage.className = 'field-message';
      passwordMessage.textContent = '';
      confirmPasswordInput.setCustomValidity('');

      if (confirmPassword.length === 0) {
        return false;
      }

      if (password !== confirmPassword) {
        confirmPasswordGroup.classList.add('has-error');
        passwordMessage.classList.add('error');
        passwordMessage.textContent = 'Passwords do not match.';
        confirmPasswordInput.setCustomValidity('Passwords do not match.');
        return false;
      }

      passwordMessage.classList.add('success');
      passwordMessage.textContent = 'Passwords match.';
      return true;
    }

    passwordInput.addEventListener('input', () => {
      if (confirmPasswordInput.value) {
        validatePasswordMatch();
      }
    });

    confirmPasswordInput.addEventListener('input', validatePasswordMatch);

    signupForm.addEventListener('submit', (event) => {
      const passwordsMatch = validatePasswordMatch();

      if (!signupForm.checkValidity() || !passwordsMatch) {
        event.preventDefault();
        signupForm.reportValidity();
      }
    });
  </script>
</body>
</html>
