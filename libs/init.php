<?php
spl_autoload_register(function($class) {    
    require_once 'Models/' . $class . '.class.php';
});
Session::start();