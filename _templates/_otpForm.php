  <main class="otp-card" role="main" aria-label="OTP verification panel">
    <div class="corner-deco"></div>

    <header class="card-header">
      <h1>Verify OTP</h1>
      <p>We sent a 6‑digit code to your email</p>
    </header>

    <form
      class="otp-form"
      id="otpForm"
      action="#"
      method="post"
      novalidate
    >
      <div class="input-group" id="otpGroup">
        <input type="text" id="otpCode" name="otp_code" placeholder="Enter 6‑digit code" autocomplete="one-time-code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required/>
        <i class="fas fa-shield-alt input-icon"></i>
      </div>

      <div id="otpMessage" class="field-message" aria-live="polite"></div>
<!-- 
      <div class="otp-timer">
        <span id="timerDisplay">2:00</span>
        <button type="button" class="resend-link" id="resendBtn">Resend code</button>
      </div> -->

      <button type="submit" class="verify-btn">
        <span>Verify &amp; continue</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    </form>

    <div class="divider">or continue with</div>
<!-- 
    <div class="social-row">
      <a href="#" aria-label="Continue with Google">
        <i class="fab fa-google"></i>
        Google
      </a>

      <a href="#" aria-label="Continue with Apple">
        <i class="fab fa-apple"></i>
        Apple
      </a>
    </div> -->

    <!-- <footer class="card-footer">
      Didn't receive the code? <a href="#" id="emailChangeLink">Change email</a>
    </footer> -->
  </main>