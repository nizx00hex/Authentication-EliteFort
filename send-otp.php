<?php

header('Content-Type: application/json');

require_once '_core/__init__.php';

$email = trim($_POST['email'] ?? '');

if (empty($email)) {

    echo json_encode([
        'success' => false,
        'message' => 'Email is required.'
    ]);

    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo json_encode([
        'success' => false,
        'message' => 'Enter a valid email.'
    ]);

    exit;
}


// Process OTP
$result = Mailer::sendOtp($email, 123123);


// Only say success AFTER mail was sent
if ($result) {

    echo json_encode([
        'success' => true,
        'message' => 'Verification code sent successfully.'
    ]);

    exit;
}


echo json_encode([
    'success' => false,
    'message' => 'Unable to send verification code.'
]);