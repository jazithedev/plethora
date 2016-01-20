<?php

date_default_timezone_set('Europe/Warsaw');
header('Content-Type: text/html; charset=utf-8');

// directory separator
define('DS', DIRECTORY_SEPARATOR);

// Define root path
$sRealPath = str_replace(['\\', '/'], [DS, DS], realpath(__DIR__));

define('PATH_ROOT', $sRealPath.DS.'..'.DS.'..'.DS);

// Define application name
$aExplDir = explode(DS, dirname(__FILE__));

define('APP_NAME', array_pop($aExplDir));

// Load framework installation process
if(file_exists('./install.php')) {
    require_once './install.php';
} // If application is installed, load bootstrap file
else {
    require_once PATH_ROOT.'framework'.DS.'core'.DS.'bootstrap.php';
}
