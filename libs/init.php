<?php
spl_autoload_register(function($class) {    
    require_once 'Models/' . $class . '.class.php';
});
Session::start();




function loadTemplates($name) {
    // print(__DIR__ . "/../_templates/$name.php");
    include __DIR__ . "/../_templates/$name.php";
    // echo __DIR__ . "/_templates/$name.php";
    // include $_SERVER['DOCUMENT_ROOT'] . "sna-photogram-project/_templates/$name.php";
}
