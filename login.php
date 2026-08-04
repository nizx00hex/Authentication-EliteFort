<?php
require_once __DIR__ . '/libs/init.php';

$success = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Email and password are required.";
    } else {

        try {
            $user = User::_login($email, $password);
            // var_dump($user);
            // exit;

            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('isLoggedIn', true);

            header("Location: dashboard.php");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>matte black · premium login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/login.css">
<style>
    .alert {
        position: fixed;
        top: 25px;
        right: 25px;
        z-index: 9999;

        display: flex;
        align-items: center;
        gap: 14px;

        width: min(420px, calc(100% - 40px));
        padding: 16px 48px 16px 16px;

        font-family: Arial, sans-serif;

        border-radius: 14px;
        backdrop-filter: blur(16px);
        overflow: hidden;

        box-shadow:
            0 20px 45px rgba(0, 0, 0, 0.35),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);

        animation: alertShow 0.4s ease forwards;
    }

    .alert.hide {
        animation: alertHide 0.3s ease forwards;
    }

    .alert-error {
        color: #ffdce3;
        background: rgba(44, 10, 19, 0.95);
        border: 1px solid rgba(255, 70, 104, 0.45);
    }

    .alert-success {
        color: #dcffe9;
        background: rgba(7, 40, 26, 0.95);
        border: 1px solid rgba(48, 220, 133, 0.45);
    }

    .alert-icon {
        flex-shrink: 0;

        display: grid;
        place-items: center;

        width: 44px;
        height: 44px;

        color: #ffffff;
        font-size: 23px;
        font-weight: 800;

        border-radius: 50%;
    }

    .alert-error .alert-icon {
        background: linear-gradient(135deg, #ff3b68, #ff7895);
        box-shadow: 0 8px 24px rgba(255, 59, 104, 0.35);
    }

    .alert-success .alert-icon {
        background: linear-gradient(135deg, #1fc978, #61e9a7);
        box-shadow: 0 8px 24px rgba(31, 201, 120, 0.35);
    }

    .alert-content {
        display: flex;
        flex-direction: column;
        gap: 4px;

        min-width: 0;
        line-height: 1.4;
    }

    .alert-content strong {
        color: #ffffff;
        font-size: 16px;
    }

    .alert-content span {
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .alert-error .alert-content span {
        color: #ffb8c5;
    }

    .alert-success .alert-content span {
        color: #adf5ca;
    }

    .alert-close {
        position: absolute;
        top: 50%;
        right: 13px;

        display: grid;
        place-items: center;

        width: 30px;
        height: 30px;

        color: inherit;
        font-size: 24px;
        line-height: 1;

        background: transparent;
        border: none;
        border-radius: 50%;

        cursor: pointer;
        transform: translateY(-50%);
        transition: 0.2s ease;
    }

    .alert-close:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-50%) rotate(90deg);
    }

    .alert-progress {
        position: absolute;
        bottom: 0;
        left: 0;

        width: 100%;
        height: 3px;

        animation: alertTimer 5s linear forwards;
    }

    .alert-error .alert-progress {
        background: linear-gradient(90deg, #ff3b68, #ff8ca5);
    }

    .alert-success .alert-progress {
        background: linear-gradient(90deg, #20ca79, #73efb5);
    }

    @keyframes alertShow {
        from {
            opacity: 0;
            transform: translateX(40px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes alertHide {
        from {
            opacity: 1;
            transform: translateX(0) scale(1);
        }

        to {
            opacity: 0;
            transform: translateX(40px) scale(0.95);
        }
    }

    @keyframes alertTimer {
        from {
            width: 100%;
        }

        to {
            width: 0;
        }
    }

    @media (max-width: 600px) {
        .alert {
            top: 15px;
            right: 15px;
            left: 15px;

            width: auto;
        }
    }
</style>

</head>
<body>     
    <?php if (!empty($error)): ?>
        <div class="alert alert-error" id="errorAlert" role="alert">
            <div class="alert-icon">!</div>

            <div class="alert-content">
                <strong>Login Failed</strong>
                <span>
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>

            <button
                type="button"
                class="alert-close"
                onclick="closeAlert('errorAlert')"
                aria-label="Close alert"
            >
                &times;
            </button>

            <div class="alert-progress"></div>
        </div>
    <?php endif; ?>

  
  <div class="login-card" role="main" aria-label="Login panel">





    <!-- decorative corner -->
    <div class="corner-deco"></div>

    <!-- header -->
    <div class="card-header">
      <!-- <div class="logo-mark">
        <i class="fas fa-snowflake"></i>
      </div> -->
      <h1>Welcome back</h1>
      <p>Sign in to your account</p>
    </div>

    <!-- form -->
    <form class="login-form" action="login.php" method="post">
      <div class="input-group">
        
        <input type="email" name="email" id="email" placeholder="Email address"  autocomplete="email" />
        <i class="fas fa-envelope input-icon"></i>
      </div>

      <div class="input-group">
        <input type="password" name="password" id="password" placeholder="Password"  autocomplete="current-password" />
        <i class="fas fa-lock input-icon"></i>
      </div>

      <div class="form-options">
        <label for="remember">
          <input type="checkbox" id="remember" checked /> <span>Remember me</span>
        </label>
        <a href="#">Forgot password?</a>
      </div>

      <button type="submit" class="login-btn">
        <span>Sign in</span> <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <!-- divider -->
    <div class="divider">or continue with</div>

    <!-- social -->
    <div class="social-row">
      <a href="#" aria-label="Google"><i class="fab fa-google"></i> Google</a>
      <a href="#" aria-label="Apple"><i class="fab fa-apple"></i> Apple</a>
    </div>
 
    <!-- footer -->
    <div class="card-footer">
      Don't have an account? <a href="signup.php">Sign up</a>
    </div>
  </div>
    <script>
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
  </script>
</body>

</html>