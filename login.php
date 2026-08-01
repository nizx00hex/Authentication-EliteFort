<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>matte black · premium login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
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
    <form class="login-form" action="#" method="post">
      <div class="input-group">
        <input type="email" id="email" placeholder="Email address" required autocomplete="email" />
        <i class="fas fa-envelope input-icon"></i>
      </div>

      <div class="input-group">
        <input type="password" id="password" placeholder="Password" required autocomplete="current-password" />
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
</body>
</html>