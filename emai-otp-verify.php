<?php
// echo password_hash(000000, PASSWORD_DEFAULT);
// echo password_hash("nisath", PASSWORD_DEFAULT);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
    .message-card {
        max-width: 380px;
        padding: 18px 20px;
        border-radius: 12px;
        background: #f0fff4;
        border: 1px solid #b7ebc6;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        font-family: Arial, sans-serif;
    }

    .message-card h3 {
        margin: 0 0 6px;
        font-size: 17px;
        color: #15803d;
    }

    .message-card p {
        margin: 0;
        color: #444;
        font-size: 14px;
        line-height: 1.5;
    }
    </style>
</head>
<body>
    <form id="otpForm">
        <input
            type="email"
            name="email"
            id="email"
            placeholder="Enter your email"
            required
        >

        <button type="submit" id="sendOtpBtn">
            Send OTP
        </button>
    </form>

    <div id="message"></div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $('#otpForm').on('submit', function(e) {
        e.preventDefault();
        let email = $('#email').val();
        // Hide email form
        $('#otpForm').hide();
        // Show message card
        $('#message').html(`
            <div class="message-card">
                <h3>OTP Request Received</h3>

                <p>
                    Please check your email shortly.
                </p>
            </div>
        `);
        // Now process OTP
        $.ajax({
            url: 'send-otp.php',
            type: 'POST',
            data: {
                email: email
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#message').html(`
                        <div class="message-card">
                            <h3>OTP Sent Successfully</h3>
                            <p>
                                We sent a verification code to
                                <strong>${email}</strong>.<br>
                                You can check your email now.
                            </p>
                        </div>
                    `);
                } else {
                    // Show form again if OTP fails
                    $('#otpForm').show();

                    $('#message').html(`
                        <p style="color:red;">
                            ${response.message}
                        </p>
                    `);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                // Bring form back
                $('#otpForm').show();
                $('#message').html(`
                    <p style="color:red;">
                        Unable to send OTP.
                    </p>
                `);
            }
        });
    });
</script>
</body>
</html>