<?php

class ValidationException extends Exception
{
}

class EmailAlreadyExistsException extends Exception
{
}

class UsernameAlreadyExistsException extends Exception
{
}

class InvalidCredentialsException extends Exception
{
}

class OtpInvalidException extends Exception
{
}

class OtpExpiredException extends Exception
{
}

class OtpAttemptsExceededException extends Exception
{
}

class OtpNotFoundException extends Exception
{
}

class MailDeliveryException extends Exception
{
}

class OtpResendTooSoonException extends Exception
{
    private int $remainingSeconds;

    public function __construct(int $remainingSeconds)
    {
        $this->remainingSeconds = $remainingSeconds;

        parent::__construct(
            "Please wait {$remainingSeconds} seconds before requesting another OTP."
        );
    }

    public function getRemainingSeconds(): int
    {
        return $this->remainingSeconds;
    }
}

class AccountNotVerifiedException extends Exception
{
    private int $userId;
    private string $email;
    private string $fullName;

    public function __construct(
        int $userId,
        string $email,
        string $fullName
    ) {
        $this->userId = $userId;
        $this->email = $email;
        $this->fullName = $fullName;

        parent::__construct("Please verify your email before logging in.");
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }
}