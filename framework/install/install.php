<?php

// main paths
define('PATH_FW', PATH_ROOT.'framework'.DS);
define('PATH_INSTALL', PATH_FW.'install'.DS); // Core framework path
define('PATH_CORE', PATH_FW.'core'.DS); // Core framework path
define('PATH_LIB', PATH_FW.'lib'.DS); // Libaries framework path
define('PATH_MODULES', PATH_ROOT.'modules'.DS); // Modules path

// define app name and path
define('PATH_APP', PATH_ROOT.'application'.DS.APP_NAME.DS); // Modules path
define('PATH_PUBLIC_APP', dirname(__FILE__)); // Modules path

// show all errors if development mode is on
error_reporting(E_ALL);
ini_set('display_errors', '1');

// add some functions / classes
require_once PATH_INSTALL.'functions.php';

// add autoloader
spl_autoload_register('plethoraInstallAutoload');

// show content
echo \PlethoraInstall\Core::factory()->renderOutput();
