<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    private const DEFAULT_PORT = '587';
    private const DEFAULT_ENCRYPTION = 'tls';
    private const DEFAULT_FROM_NAME = 'EliteFort';
    private const DEFAULT_CHARSET = 'UTF-8';

    private const EMAIL_STYLES = [
        'container' =>
            'font-family: Arial, sans-serif;
             max-width: 600px;
             margin: auto;
             padding: 30px;
             color: #111;
             line-height: 1.6;',

        'button' =>
            'display: inline-block;
             padding: 14px 24px;
             text-decoration: none;
             border-radius: 6px;
             background: #111;
             color: #fff;',

        'otp' =>
            'font-size: 32px;
             font-weight: bold;
             letter-spacing: 8px;
             margin: 25px 0;',

        'footer' =>
            'font-size: 12px;
             color: #666;
             margin-top: 30px;',

        'spacing' =>
            'margin: 30px 0;'
    ];

    // Creates and configures PHPMailer.
    private static function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = Env::env('MAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = Env::env('MAIL_USERNAME');
        $mail->Password = Env::env('MAIL_PASSWORD');
        $mail->Port = (int) Env::env('MAIL_PORT', self::DEFAULT_PORT);
        $encryption = strtolower(Env::env('MAIL_ENCRYPTION', self::DEFAULT_ENCRYPTION));
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->CharSet = self::DEFAULT_CHARSET;
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->setFrom(
            Env::env('MAIL_FROM_ADDRESS'),
            Env::env('MAIL_FROM_NAME', self::DEFAULT_FROM_NAME)
        );
        return $mail;
    }

    // Core method used by all email methods.
    private static function send(string $to, string $subject, string $body, string $altBody): bool
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            error_log('Mailer Error: Invalid recipient email.');
            return false;
        }
        try {
            $mail = self::createMailer();
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody;
            return $mail->send();
        } catch (Throwable $e) {
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }

    // Builds main email template.
    private static function buildEmailTemplate(string $content): string
    {
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>EliteFort</title>
        </head>
        <body>
            <div style="' . self::EMAIL_STYLES['container'] . '">
                ' . $content . '
            </div>
        </body>
        </html>';
    }

    // Builds common email footer.
    private static function buildFooter(): string
    {
        return '
            <hr>
            <p style="' . self::EMAIL_STYLES['footer'] . '">
                EliteFort Authentication
                <br>
                This is an automated security email.
                Please do not reply.
            </p>
        ';
    }

    // Sends OTP for email verification.
    public static function sendOtp(string $email, string $otp): bool
    {
        $otpSafe = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
        $subject = 'Verify Your Email';
        $content = '
            <h2>Email Verification</h2>
            <p>Use the following OTP to verify your EliteFort account.</p>
            <div style="' . self::EMAIL_STYLES['otp'] . '">' . $otpSafe . '</div>
            <p>This OTP will expire shortly.</p>
            <p>Never share this OTP with anyone.</p>
            <p>If you did not create an EliteFort account, you can safely ignore this email.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "EliteFort Email Verification\n\nYour OTP is: {$otp}\n\nThis OTP will expire shortly.\n\nNever share this OTP with anyone.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends password reset link.
    public static function sendPasswordReset(string $email, string $resetLink): bool
    {
        $safeLink = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');
        $subject = 'Reset Your Password';
        $content = '
            <h2>Password Reset</h2>
            <p>We received a request to reset your EliteFort account password.</p>
            <p>Click the button below to create a new password.</p>
            <p style="' . self::EMAIL_STYLES['spacing'] . '">
                <a href="' . $safeLink . '" style="' . self::EMAIL_STYLES['button'] . '">
                    Reset Password
                </a>
            </p>
            <p>If the button does not work, copy and paste the following link into your browser:</p>
            <p>' . $safeLink . '</p>
            <p>This password reset link will expire.</p>
            <p>If you did not request a password reset, you can safely ignore this email.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "EliteFort Password Reset\n\nReset your password using this link:\n\n" . $resetLink . "\n\nIf you did not request this, ignore this email.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends notification after password is changed.
    public static function sendPasswordChanged(string $email): bool
    {
        $subject = 'Your Password Was Changed';
        $content = '
            <h2>Password Changed</h2>
            <p>Your EliteFort account password was successfully changed.</p>
            <p>If you made this change, no action is required.</p>
            <p>If you did not change your password, secure your account immediately.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "Your EliteFort account password was successfully changed.\n\nIf this was not you, secure your account immediately.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends notification when a login occurs.
    public static function sendLoginAlert(string $email): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $date = date('Y-m-d H:i:s');
        $safeIp = htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');
        $safeAgent = htmlspecialchars($agent, ENT_QUOTES, 'UTF-8');
        $safeDate = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');
        $subject = 'New Login to Your Account';
        $content = '
            <h2>New Login Detected</h2>
            <p>A login was detected on your EliteFort account.</p>
            <p><strong>Time:</strong> ' . $safeDate . '</p>
            <p><strong>IP Address:</strong> ' . $safeIp . '</p>
            <p><strong>Device / Browser:</strong> ' . $safeAgent . '</p>
            <p>If this was you, no action is required.</p>
            <p>If this was not you, change your password immediately.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "New EliteFort Login Detected\n\nTime: {$date}\nIP Address: {$ip}\nDevice / Browser: {$agent}\n\nIf this wasn't you, change your password immediately.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends notification when account is locked.
    public static function sendAccountLocked(string $email, int $minutes): bool
    {
        $minutes = max(1, $minutes);
        $subject = 'Your Account Has Been Locked';
        $content = '
            <h2>Account Temporarily Locked</h2>
            <p>Your EliteFort account has been temporarily locked because of multiple failed login attempts.</p>
            <p><strong>Lock duration:</strong> ' . $minutes . ' minutes</p>
            <p>You can try signing in again after the lock period expires.</p>
            <p>If these login attempts were not made by you, consider changing your password.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "Your EliteFort account has been temporarily locked.\n\nLock duration: {$minutes} minutes.\n\nIf these attempts were not made by you, consider changing your password.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends notification when account is unlocked.
    public static function sendAccountUnlocked(string $email): bool
    {
        $subject = 'Your Account Has Been Unlocked';
        $content = '
            <h2>Account Unlocked</h2>
            <p>Your EliteFort account has been unlocked.</p>
            <p>You can now sign in normally.</p>
            <p>If you did not expect this change, secure your account.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "Your EliteFort account has been unlocked.\n\nYou can now sign in normally.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends security notification when account email address is changed.
    public static function sendEmailChanged(string $email, string $newEmail): bool
    {
        $safeNewEmail = htmlspecialchars($newEmail, ENT_QUOTES, 'UTF-8');
        $subject = 'Your Email Address Was Changed';
        $content = '
            <h2>Email Address Changed</h2>
            <p>The email address associated with your EliteFort account was changed.</p>
            <p><strong>New email:</strong> ' . $safeNewEmail . '</p>
            <p>If you made this change, no action is required.</p>
            <p>If you did not make this change, secure your account immediately.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "Your EliteFort account email was changed.\n\nNew email: {$newEmail}\n\nIf this wasn't you, secure your account immediately.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends welcome email after successful email verification.
    public static function sendWelcome(string $email, string $name): bool
    {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $subject = 'Welcome to EliteFort';
        $content = '
            <h2>Welcome to EliteFort</h2>
            <p>Hello ' . $safeName . ',</p>
            <p>Your email address has been successfully verified.</p>
            <p>Your EliteFort account is now active.</p>
            <p>You can now sign in to your account.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "Hello {$name},\n\nYour EliteFort email has been successfully verified.\n\nYour account is now active and you can sign in.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends a test email to verify mail configuration.
    public static function sendTestEmail(string $email): bool
    {
        $subject = 'EliteFort Mail Test';
        $content = '
            <h2>Mail Configuration Test</h2>
            <p>This is a test email to verify that the EliteFort mail system is working correctly.</p>
            <p>If you received this email, your mail configuration is working properly.</p>
            <p><strong>Time sent:</strong> ' . date('Y-m-d H:i:s') . '</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "EliteFort Mail Test\n\nThis is a test email to verify mail configuration.\n\nTime sent: " . date('Y-m-d H:i:s');
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends 2FA OTP for secure login.
    public static function sendTwoFactorOtp(string $email, string $otp): bool
    {
        $otpSafe = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
        $subject = 'Your 2FA Verification Code';
        $content = '
            <h2>Two-Factor Authentication</h2>
            <p>Use the following code to complete your EliteFort login.</p>
            <div style="' . self::EMAIL_STYLES['otp'] . '">' . $otpSafe . '</div>
            <p>This code will expire in 5 minutes.</p>
            <p>Never share this code with anyone.</p>
            <p>If you did not attempt to log in, secure your account immediately.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "EliteFort 2FA Code\n\nYour verification code is: {$otp}\n\nThis code will expire in 5 minutes.\n\nNever share this code with anyone.";
        return self::send($email, $subject, $body, $altBody);
    }

    // Sends notification when profile information is updated.
    public static function sendProfileUpdated(string $email, array $changes): bool
    {
        $subject = 'Your Profile Was Updated';
        $changeList = '';
        foreach ($changes as $field => $value) {
            $safeField = htmlspecialchars($field, ENT_QUOTES, 'UTF-8');
            $safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            $changeList .= '<li><strong>' . $safeField . ':</strong> ' . $safeValue . '</li>';
        }
        $content = '
            <h2>Profile Updated</h2>
            <p>Your EliteFort profile information was updated.</p>
            <p>The following changes were made:</p>
            <ul>' . $changeList . '</ul>
            <p>If you made these changes, no action is required.</p>
            <p>If you did not make these changes, secure your account immediately.</p>
            ' . self::buildFooter();
        $body = self::buildEmailTemplate($content);
        $altBody = "Your EliteFort profile was updated.\n\nChanges made:\n" . print_r($changes, true) . "\n\nIf this wasn't you, secure your account immediately.";
        return self::send($email, $subject, $body, $altBody);
    }
}