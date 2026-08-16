<?php
require_once '_core/__init__.php';

// $success = Session::getFlash();
// print_r($success);


$flash = Session::getFlash();

if ($flash !== null) {

    if ($flash['type'] === 'success') {
        $success = $flash['message'];
    }

    if ($flash['type'] === 'error') {
        $error = $flash['message'];
    }
}

echo $success;
