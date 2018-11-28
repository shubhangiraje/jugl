<?php

error_reporting(E_ALL & ~E_NOTICE);
ini_set('memory_limit','2G');

if ($_SERVER['PHP_AUTH_USER']!='additional' || $_SERVER['PHP_AUTH_PW']!='aHG2rSDdp7VSrmJt') {
    header('WWW-Authenticate: Basic realm="Please enter your credentials"');
    header('HTTP/1.0 401 Unauthorized');
    exit;
}
