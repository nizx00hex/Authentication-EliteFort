<?php
class Otp {
    // private const OTP_LENGTH = 6;
    private const OTP_EXPIRY_MINUTES = 5;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_COOLDOWN_SECONDS = 60;


    //genarate 6 digit OTP
    public static function _genarate(){
        return random_int(100000, 999999);
    }

    //hash the OTP for stroing in database
    public static function _hash($otp){
        return password_hash($otp, PASSWORD_DEFAULT);
    }

    //verify the hashed OTP
    public static function _verifyHash($otp, $hashOtp) {
        return password_verify($otp, $hashOtp);
    }

    //check is OTP expired?
    public static function _isExpired($expiry) {
        if(empty($expiry)) {
            return true;
        }

        $expiryTimeStamp = strtotime($expiry);

        if($expiryTimeStamp === false) {
            return true;
        }

        return $expiryTimeStamp <= time();
    }


    //create OTP expiry time
    public static function _createExpiry() {
        return date(
            'Y-m-d H:i:s',
            time() + (self::OTP_EXPIRY_MINUTES * 60)
        );
    }

    //create and store in database for a new user
    public static function _createForUser($userId) {
        $conn = Database::getConnection();

        $userId = $userId;

        $user = self::_getUser($userId);

        if(!$user) {
            throw new Exception('User account was not found.');
        }

        if((int) $user['is_verified'] === 1) {
            throw new Exception("This account is already verified.");
        }

        if(!self::_canResend($user['otp_last_sent'])) {
            throw new Exception('Please wait before requesting another OTP');
        }

        $otp = self::_genarate();
        $otpHash = self::_hash($otp);
        $otpExpiry = self::_createExpiry();

        $query = "UPDATE `Auth` SET `otp_hash` = :otp_hash, `otp_expiry` = :otp_expiry, `otp_attempts` = 0, `otp_last_sent` = NOW() WHERE `id` = :id AND `is_verified` = 0 LIMIT 1";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            'otp_hash' => $otpHash,
            'otp_expiry' => $otpExpiry,
            'id' => $userId
        ]);

        if($stmt->rowCount() !== 1) {
            throw new Exception('OTP genration failed.');
        }
        return $otp;
    }

    //verify the OTP entered by the user
    public static function _verifyForUser($userId, $otp) {
        $userId = $userId;
        $otp = trim($otp);


        // if (preg_match('/^\d{6}$/', $otp)) {
        //     throw new Exception('Please enter a valid 6-digit OTP.');
        // }

        $user = self::_getUser($userId);

        if(!$user) {
            throw new Exception('User account was not found');
        }

        if((int) $user['is_verified'] === 1) {
            throw new Exception('This account is already verified.');
        }

        if(empty($user['otp_hash'])) {
            throw new Exception('No OTP is available, Please request a new OTP.');
        }

        if(self::_isExpired($user['otp_expiry'])) {
            self::_clearForUser($userId);

            throw new Exception('OTP has expired, Please resend the OTP.');
        }

        if((int) $user['otp_attempts'] >= self::MAX_ATTEMPTS) {
            self::_clearForUser($userId);

            throw new Exception('Too many incorrect attemtps, Please resend the OTP.');
        }

        if(!self::_verifyHash($otp, $user['otp_hash'])) {
            self::_increaseAttempts($userId);

            $remainingAttempts = self::MAX_ATTEMPTS - ((int) $user['otp_attempts'] + 1);

            if($remainingAttempts <= 0) {
                self::_clearForUser($userId);

                throw new Exception('Too many incorrect attempts, Please resend the OTP.');
            }

            throw new Exception(
                "Incorrect OTP, {$remainingAttempts} attempts remaining"
            );
        }
        return self::_activateUser((int) $user['id']);
    }

    //
 
    //Activate account after successfull OTP verification.
    public static function _activateUser($userId) {
        $conn = Database::getConnection();

        $query = "UPDATE `Auth` SET `is_verified` = 1, `otp_hash` = NULL, `otp_expiry` = NULL, `otp_attempts` = 0, `otp_last_sent` = NULL WHERE `id` = :id AND `is_verified` = 0 LIMIT 1";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            'id' => $userId
        ]);

        if($stmt->rowCount() !== 1) {
            throw new Exception('Account activation failed.');
        }

        return true;
    }


    //increase incorrect OTP attempts
    private static function _increaseAttempts($username) {
        $conn = Database::getConnection();
        
        $query = "UPDATE `Auth` SET `otp_attempts` = `otp_attempts` + 1 WHERE `id` = :id LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            'id' => $userId
        ]);
    }
    


    //remove expired or invalidated OTP information
    public static function _clearForUser($userId) {
        $conn = Database::getConnection();

        $query = "UPDATE `Auth` SET `otp_hash` = NULL, `otp_expiry` = NULL, `otp_attempts` = 0 WHERE `id` = :id LIMIT 1";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            'id' => $userId
        ]);
    }

    //generate another OTP
    public static function _resend($userId) {
        return self::_createForUser($userId);
    }


    //check resend cooldown 
    private static function _canResend($lastSent) {
        if(empty($lastSent)) {
            return true;
        }

        $lastSentTimeStamp = strtotime($lastSent);

        if($lastSentTimeStamp === false) {
            return true;
        }

        return time() >= ($lastSentTimeStamp + self::RESEND_COOLDOWN_SECONDS);
    }
    //get user for OTP information
    private static function _getUser($userId) {
        $conn = Database::getConnection();

        $query = "SELECT `id`, `email`, `username`, `otp_hash`, `otp_expiry`, `otp_attempts`, `otp_last_sent`, `is_verified` FROM `Auth` WHERE `id` = :id LIMIT 1";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            'id' => $userId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}