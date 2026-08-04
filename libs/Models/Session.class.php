<?php
class Session {
    public static function start() {
        session_start();
    }
    public static function unset() {
        session_unset();
    }
    public static function destroy() {
        session_destroy();
        /**
         * if user session is active, set it to inactive
         */
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function delete($key) {
        unset($_SESSION[$key]);
    }

    public static function isset($key) {
        return isset($_SESSION[$key]);
    }

    public static function get($key, $default = false) {
        
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }
}