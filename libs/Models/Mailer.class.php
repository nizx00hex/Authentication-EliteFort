<?php
require_once 'EnvGetter.class.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    /* =========================================================
       PRIVATE CONSTANTS & PROPERTIES
    ========================================================= */

    /**
     * Default configuration values
     */
    private const DEFAULT_PORT = '587';
    private const DEFAULT_ENCRYPTION = 'tls';
    private const DEFAULT_FROM_NAME = 'EliteFort';
    private const DEFAULT_CHARSET = 'UTF-8';

    /**
     * Email template styles
     */
    private const EMAIL_STYLES = [
        'container' => 'font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 30px;',
        'button' => 'display: inline-block; padding: 14px 24px; text-decoration: none; border-radius: 6px; background: #111; color: #fff;',
        'otp' => 'font-size: 32px; font-weight: bold; letter-spacing: 8px; margin: 25px 0;',
        'footer' => 'font-size: 12px;',
        'spacing' => 'margin: 30px 0;'
    ];

    // /* =========================================================
    //    ENVIRONMENT HELPER
    // ========================================================= */

    // /**
    //  * Get environment variable with optional default
    //  */
    // private static function env(string $key, ?string $default = null): string
    // {
    //     $value = getenv($key);

    //     if ($value === false || $value === '') {
    //         if ($default !== null) {
    //             return $default;
    //         }
    //         throw new RuntimeException("Missing environment variable: " . $key);
    //     }

    //     return $value;
    // }

    /* =========================================================
       MAILER CREATION
    ========================================================= */

    /**
     * Create and configure PHPMailer instance
     */
    private static function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);

        // Use SMTP
        $mail->isSMTP();

        // SMTP Server
        $mail->Host = Env::env('MAIL_HOST');

        // Enable SMTP authentication
        $mail->SMTPAuth = true;

        // SMTP Credentials
        $mail->Username = Env::env('MAIL_USERNAME');
        $mail->Password = Env::env('MAIL_PASSWORD');

        // SMTP Port
        $mail->Port = (int) Env::env('MAIL_PORT', self::DEFAULT_PORT);

        // Encryption (tls = STARTTLS, ssl = SMTPS)
        $encryption = strtolower(Env::env('MAIL_ENCRYPTION', self::DEFAULT_ENCRYPTION));
        
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        // Character encoding
        $mail->CharSet = self::DEFAULT_CHARSET;

        // Sender
        $mail->setFrom(
            Env::env('MAIL_FROM_ADDRESS'),
            Env::env('MAIL_FROM_NAME', self::DEFAULT_FROM_NAME)
        );

        // Disable SMTP debugging
        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        return $mail;
    }

    /* =========================================================
       MAIN SEND METHOD
    ========================================================= */

    /**
     * Core method to send emails
     */
    private static function send(string $to, string $subject, string $body, string $altBody): bool
    {
        // Validate recipient email
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        try {
            $mail = self::createMailer();

            // Recipient
            $mail->addAddress($to);

            // HTML email
            $mail->isHTML(true);

            // Email content
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody;

            // Send
            return $mail->send();
        } catch (Throwable $e) {
            // Log internally. Never show SMTP errors/passwords to the user.
            error_log('Mailer Error: ' . $e->getMessage());
            return false;
        }
    }

    /* =========================================================
       EMAIL TEMPLATE HELPERS
    ========================================================= */

    /**
     * Build email template wrapper
     */
    private static function buildEmailTemplate(string $content): string
    {
        return '<div style="' . self::EMAIL_STYLES['container'] . '">' . $content . '</div>';
    }

    /**
     * Build email footer
     */
    private static function buildFooter(): string
    {
        return '<hr><p style="' . self::EMAIL_STYLES['footer'] . '">EliteFort Authentication</p>';
    }

    /* =========================================================
       SEND VERIFICATION OTP
    ========================================================= */

    /**
     * Send OTP for email verification
     */
    public static function sendOtp(string $email, string $otp): bool
    {
        $otpSafe = htmlspecialchars($otp, ENT_QUOTES, 'UTF-8');
        $subject = 'Verify Your Email';

        $content = '
            <h2>Email Verification</h2>
            <p>Use the following OTP to verify your account.</p>
            <div style="' . self::EMAIL_STYLES['otp'] . '">' . $otpSafe . '</div>
            <p>This OTP will expire shortly.</p>
            <p>If you did not request this, you can ignore this email.</p>
            ' . self::buildFooter();

        $body = self::buildEmailTemplate($content);
        $altBody = "Your EliteFort verification OTP is: " . $otp . "\n\nThis OTP will expire shortly.";

        return self::send($email, $subject, $body, $altBody);
    }

    /* =========================================================
       SEND PASSWORD RESET LINK
    ========================================================= */

    /**
     * Send password reset link
     */
    public static function sendPasswordReset(string $email, string $resetLink): bool
    {
        $safeLink = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');
        $subject = 'Reset Your Password';

        $content = '
            <h2>Password Reset</h2>
            <p>We received a request to reset your password.</p>
            <p>Click the button below to create a new password.</p>
            <p style="' . self::EMAIL_STYLES['spacing'] . '">
                <a href="' . $safeLink . '" style="' . self::EMAIL_STYLES['button'] . '">
                    Reset Password
                </a>
            </p>
            <p>If the button does not work, copy this link:</p>
            <p>' . $safeLink . '</p>
            <p>This reset link will expire.</p>
            <p>If you did not request a password reset, you can ignore this email.</p>
            ' . self::buildFooter();

        $body = self::buildEmailTemplate($content);
        $altBody = "Reset your EliteFort password:\n\n" . $resetLink . "\n\nIf you did not request this, ignore this email.";

        return self::send($email, $subject, $body, $altBody);
    }

    /* =========================================================
       SEND PASSWORD CHANGED ALERT
    ========================================================= */

    /**
     * Send notification when password is changed
     */
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

    /* =========================================================
       SEND LOGIN SECURITY ALERT
    ========================================================= */

    /**
     * Send notification when a new login is detected
     */
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
            <p>A new login was detected on your EliteFort account.</p>
            <p><strong>Time:</strong> ' . $safeDate . '</p>
            <p><strong>IP Address:</strong> ' . $safeIp . '</p>
            <p><strong>Device / Browser:</strong> ' . $safeAgent . '</p>
            <p>If this was you, no action is required.</p>
            <p>If this was not you, change your password immediately.</p>
            ' . self::buildFooter();

        $body = self::buildEmailTemplate($content);
        $altBody = "New login detected.\n\nTime: {$date}\nIP: {$ip}\nDevice: {$agent}\n\nIf this wasn't you, change your password immediately.";

        return self::send($email, $subject, $body, $altBody);
    }
}