<?php


spl_autoload_register(function($class) {    
    require_once 'libs/Models/' . $class . '.class.php';
});
// Session::start();
require_once __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

function loadTemplates($name) {
    // print(__DIR__ . "/../_templates/$name.php");
    include __DIR__ . "/../_templates/$name.php";
    // echo __DIR__ . "/_templates/$name.php";
    // include $_SERVER['DOCUMENT_ROOT'] . "sna-photogram-project/_templates/$name.php";
}

$dotenv = Dotenv\Dotenv::createImmutable(
    dirname(__DIR__)
);

$dotenv->safeLoad();


function _getTitle($file) {
    $_file = basename($file, ".php");
    if($_file === 'login') {
        echo "<title>Sign In | EliteFort</title>";
    } else if($_file === 'signup'){
        echo "<title>Create Account | EliteFort</title>";
    } else if ($_file === 'otp-verify') {
        echo "<title>Verify Email | EliteFort</title>";
    } else if ($_file === 'forgot-password') {
        echo "<title>Forgot Password | EliteFort</title>";
    } else if ($_file === 'reset-password') {
        echo "<title>Reset Password | EliteFort</title>";
    } else if ($_file === 'session-expired') {
        echo "<title>Session Expired | EliteFort</title>";
    } else if($_file === 'account-locked') {
        echo "<title>Account Locked | EliteFort</title>";
    } else {
        echo "<title>EliteFort</title>";
    }
}


