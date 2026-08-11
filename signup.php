<?php
require_once __DIR__ . '/libs/__init__.php';
?>

<?= loadTemplates("_head")?>
<body>

  <?= loadTemplates("_signupForm") ?>

  <?= loadTemplates("_toPopup") ?>

  <script>

    const policyLinks = document.querySelectorAll('[data-modal-target]');
    const policyModals = document.querySelectorAll('.policy-modal');
    let activeModal = null;
    let modalTrigger = null;
    function closeAlert(alertId) {
        const alertBox = document.getElementById(alertId);

        if (!alertBox || alertBox.classList.contains("hide")) {
            return;
        }

        alertBox.classList.add("hide");

        setTimeout(() => {
            alertBox.remove();
        }, 300);
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".alert").forEach(function (alertBox) {
            setTimeout(function () {
                closeAlert(alertBox.id);
            }, 5000);
        });
    });
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

    // function validatePasswordMatch() {
    //   const password = passwordInput.value;
    //   const confirmPassword = confirmPasswordInput.value;

    //   confirmPasswordGroup.classList.remove('has-error');
    //   passwordMessage.className = 'field-message';
    //   passwordMessage.textContent = '';
    //   confirmPasswordInput.setCustomValidity('');

    //   if (confirmPassword.length === 0) {
    //     return false;
    //   }

    //   if (password !== confirmPassword) {
    //     confirmPasswordGroup.classList.add('has-error');
    //     passwordMessage.classList.add('error');
    //     passwordMessage.textContent = 'Passwords do not match.';
    //     confirmPasswordInput.setCustomValidity('Passwords do not match.');
    //     return false;
    //   }

    //   passwordMessage.classList.add('success');
    //   passwordMessage.textContent = 'Passwords match.';
    //   return true;
    // }

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
