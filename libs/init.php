<?php
session_start();
// require_once 'c/Database.php';

spl_autoload_register(function($class) {    
    require_once 'Models/' . $class . '.class.php';
});