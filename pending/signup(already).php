<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Matte Black · Premium Signup</title>

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
  />

  <style>
    :root {
      --matte-black: #171717;
      --deep-black: #0f0f0f;
      --charcoal: #2b2b2b;
      --dark-gray: #444444;
      --medium-gray: #666666;
      --soft-gray: #8b8b8b;
      --border-gray: rgba(60, 60, 60, 0.15);
      --page-background: #e9e9e9;
      --card-background: rgba(250, 250, 250, 0.92);
      --white: #ffffff;
      --error: #b42318;
      --success: #16794d;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      position: relative;
      overflow-x: hidden;
      background: var(--page-background);
      color: var(--matte-black);
      font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    body::before {
      content: '';
      position: fixed;
      top: -30%;
      right: -20%;
      width: 600px;
      height: 600px;
      background: radial-gradient(
        circle,
        rgba(23, 23, 23, 0.11) 0%,
        transparent 70%
      );
      pointer-events: none;
      z-index: 0;
    }

    body::after {
      content: '';
      position: fixed;
      bottom: -30%;
      left: -20%;
      width: 500px;
      height: 500px;
      background: radial-gradient(
        circle,
        rgba(43, 43, 43, 0.09) 0%,
        transparent 70%
      );
      pointer-events: none;
      z-index: 0;
    }

    .signup-card {
      position: relative;
      z-index: 2;
      width: 100%;
      max-width: 460px;
      padding: 2.8rem 2.8rem 2.6rem;
      overflow: hidden;
      background: var(--card-background);
      border: 1px solid rgba(255, 255, 255, 0.58);
      border-radius: 32px;
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      box-shadow:
        0 20px 60px rgba(0, 0, 0, 0.05),
        0 8px 32px rgba(0, 0, 0, 0.025),
        inset 0 1px 0 rgba(255, 255, 255, 0.7);
    }

    .signup-card::before {
      content: '';
      position: absolute;
      top: -85px;
      right: -85px;
      width: 170px;
      height: 170px;
      border-radius: 50%;
      background: rgba(23, 23, 23, 0.035);
      pointer-events: none;
    }

    .corner-deco {
      position: absolute;
      top: -1px;
      right: 42px;
      width: 82px;
      height: 4px;
      border-radius: 0 0 4px 4px;
      opacity: 0.5;
      background: linear-gradient(
        90deg,
        var(--matte-black),
        var(--dark-gray),
        var(--matte-black)
      );
    }

    .card-header {
      position: relative;
      z-index: 1;
      margin-bottom: 2rem;
      text-align: center;
    }

    .card-header h1 {
      margin-bottom: 0.35rem;
      color: var(--matte-black);
      font-size: 1.85rem;
      font-weight: 650;
      letter-spacing: -0.03em;
    }

    .card-header p {
      color: var(--medium-gray);
      font-size: 0.94rem;
      line-height: 1.55;
    }

    .signup-form {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .input-group {
      position: relative;
    }

    .input-icon {
      position: absolute;
      left: 16px;
      top: 50%;
      z-index: 2;
      transform: translateY(-50%);
      color: var(--soft-gray);
      font-size: 0.9rem;
      pointer-events: none;
      transition: color 0.22s ease;
    }

    .input-group input {
      width: 100%;
      padding: 0.9rem 3rem 0.9rem 3rem;
      color: var(--matte-black);
      background: rgba(250, 250, 250, 0.68);
      border: 1.5px solid var(--border-gray);
      border-radius: 60px;
      outline: none;
      font-family: inherit;
      font-size: 0.93rem;
      font-weight: 450;
      backdrop-filter: blur(4px);
      transition:
        border-color 0.22s ease,
        background 0.22s ease,
        box-shadow 0.22s ease;
    }

    .input-group input::placeholder {
      color: var(--soft-gray);
      font-weight: 400;
    }

    .input-group input:focus {
      border-color: var(--matte-black);
      background: rgba(255, 255, 255, 0.96);
      box-shadow:
        0 0 0 6px rgba(23, 23, 23, 0.075),
        inset 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .input-group:focus-within .input-icon {
      color: var(--matte-black);
    }

    .password-toggle {
      position: absolute;
      top: 50%;
      right: 10px;
      z-index: 3;
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      transform: translateY(-50%);
      color: var(--soft-gray);
      background: transparent;
      border: none;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .password-toggle:hover,
    .password-toggle:focus-visible {
      color: var(--matte-black);
      background: rgba(23, 23, 23, 0.07);
      outline: none;
    }

    .password-help {
      margin: -0.35rem 0 0 0.6rem;
      color: var(--soft-gray);
      font-size: 0.72rem;
      line-height: 1.4;
    }

    .field-message {
      display: none;
      margin: -0.4rem 0 0 0.6rem;
      font-size: 0.74rem;
      line-height: 1.4;
    }

    .field-message.error {
      display: block;
      color: var(--error);
    }

    .field-message.success {
      display: block;
      color: var(--success);
    }

    .input-group.has-error input {
      border-color: var(--error);
      box-shadow: 0 0 0 5px rgba(180, 35, 24, 0.08);
    }

    .form-options {
      margin: 0.2rem 0;
      color: var(--medium-gray);
      font-size: 0.82rem;
      line-height: 1.45;
    }

    .form-options label {
      display: flex;
      align-items: flex-start;
      gap: 0.6rem;
      cursor: pointer;
    }

    .form-options input[type='checkbox'] {
      appearance: none;
      flex-shrink: 0;
      width: 18px;
      height: 18px;
      margin-top: 1px;
      position: relative;
      background: rgba(250, 250, 250, 0.68);
      border: 2px solid #a8a8a8;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.16s ease;
    }

    .form-options input[type='checkbox']:checked {
      background: var(--matte-black);
      border-color: var(--matte-black);
      box-shadow: 0 2px 8px rgba(23, 23, 23, 0.18);
    }

    .form-options input[type='checkbox']:checked::after {
      content: '✓';
      position: absolute;
      top: -3px;
      left: 2px;
      color: var(--white);
      font-size: 13px;
      font-weight: 700;
    }

    .form-options a {
      color: var(--matte-black);
      font-weight: 600;
      text-decoration: none;
      border-bottom: 1px solid rgba(23, 23, 23, 0.25);
      transition: border-color 0.15s ease;
    }

    .form-options a:hover {
      border-bottom-color: var(--matte-black);
    }

    .signup-btn {
      position: relative;
      overflow: hidden;
      width: 100%;
      margin-top: 0.2rem;
      padding: 1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.7rem;
      color: var(--white);
      background: linear-gradient(135deg, var(--matte-black), var(--charcoal));
      border: none;
      border-radius: 60px;
      box-shadow: 0 4px 20px rgba(23, 23, 23, 0.2);
      font-family: inherit;
      font-size: 0.95rem;
      font-weight: 650;
      letter-spacing: 0.3px;
      cursor: pointer;
      transition: all 0.25s ease;
    }

    .signup-btn::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: inherit;
      background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.12),
        transparent 55%
      );
      pointer-events: none;
    }

    .signup-btn:hover {
      transform: translateY(-2px);
      background: linear-gradient(135deg, var(--charcoal), var(--deep-black));
      box-shadow: 0 8px 32px rgba(23, 23, 23, 0.24);
    }

    .signup-btn:active {
      transform: scale(0.97);
    }

    .signup-btn i {
      font-size: 0.85rem;
      transition: transform 0.25s ease;
    }

    .signup-btn:hover i {
      transform: translateX(4px);
    }

    .divider {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      gap: 1.1rem;
      margin: 1.35rem 0 1rem;
      color: var(--soft-gray);
      font-size: 0.68rem;
      text-transform: uppercase;
      letter-spacing: 0.8px;
    }

    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: rgba(60, 60, 60, 0.18);
    }

    .social-row {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: center;
      gap: 0.8rem;
    }

    .social-row a {
      flex: 1;
      max-width: 150px;
      padding: 0.72rem 0.55rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      color: #222222;
      background: rgba(250, 250, 250, 0.74);
      border: 1.5px solid rgba(60, 60, 60, 0.14);
      border-radius: 40px;
      font-size: 0.8rem;
      font-weight: 550;
      text-decoration: none;
      backdrop-filter: blur(4px);
      transition: all 0.2s ease;
    }

    .social-row a:hover {
      color: var(--white);
      background: var(--matte-black);
      border-color: var(--matte-black);
      transform: translateY(-2px);
      box-shadow: 0 5px 16px rgba(23, 23, 23, 0.14);
    }

    .social-row a i {
      font-size: 1rem;
    }

    .card-footer {
      position: relative;
      z-index: 1;
      margin-top: 1.6rem;
      padding-top: 1.5rem;
      color: var(--medium-gray);
      border-top: 1px solid rgba(60, 60, 60, 0.14);
      text-align: center;
      font-size: 0.84rem;
    }

    .card-footer a {
      color: var(--matte-black);
      font-weight: 650;
      text-decoration: none;
      border-bottom: 1.5px solid transparent;
      transition: border-color 0.15s ease;
    }

    .card-footer a:hover {
      border-bottom-color: rgba(23, 23, 23, 0.35);
    }

    @media (max-width: 480px) {
      body {
        padding: 1rem;
        align-items: flex-start;
      }

      .signup-card {
        margin: 1rem 0;
        padding: 2rem 1.35rem 2rem;
        border-radius: 24px;
      }

      .card-header {
        margin-bottom: 1.6rem;
      }

      .card-header h1 {
        font-size: 1.55rem;
      }

      .input-group input {
        padding: 0.82rem 2.8rem 0.82rem 2.75rem;
        font-size: 0.88rem;
      }

      .input-icon {
        left: 14px;
      }

      .social-row a {
        max-width: none;
        font-size: 0.74rem;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      *,
      *::before,
      *::after {
        scroll-behavior: auto !important;
        transition: none !important;
      }
    }
  </style>
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
        <label for="terms">
          <input
            type="checkbox"
            id="terms"
            name="terms"
            value="1"
            required
          />

          <span>
            I agree to the <a href="#">Terms of Service</a> and
            <a href="#">Privacy Policy</a>.
          </span>
        </label>
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

  <script>
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
