<?php
require_once __DIR__ . '/libs/__init__.php';

Session::logout();

header('Location: login.php');
exit;