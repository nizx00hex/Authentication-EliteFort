<?php
require_once '_core/__init__.php';

if(!Session::isAuthenticated()) {
    header("Location: login.php");
    exit;
}




$flash = Session::getFlash();

if ($flash !== null) {

    if ($flash['type'] === 'success') {
        $success = $flash['message'];
    }

    if ($flash['type'] === 'error') {
        $error = $flash['message'];
    }
}

// echo $success;

echo "|-----------------------------------|";
echo "<br>";
echo "<a href='login.php'>| Login</a>";
echo "<br>";
echo "<a href='signup.php'>| Signup</a>";
echo "<br>";
echo "<a href='otp-verify.php'>| Otp-verify</a>";
echo "<br>";
echo "<a href='account-locked.php'>| Account-locked</a>";
echo "<br>";
echo "<a href='email-link-send-message.php'>| Email-link-send-message</a>";
echo "<br>";
echo "<a href='forgot-password.php'>| Forgot-password</a>";
echo "<br>";
echo "<a href='reset-password.php'>| Reset-password</a>";
echo "<br>";
echo "<a href='session-expired.php'>| Session-expired</a>";
echo "<br>";
echo "|-----------------------------------|";
