<?php

// main paths
define('PATH_APP', PATH_ROOT.'application'.DS.APP_NAME.DS);
define('PATH_PUBLIC', PATH_ROOT.'public_html'.DS.APP_NAME.DS);
define('PATH_THEMES', PATH_ROOT.'public_html'.DS.APP_NAME.DS.'themes'.DS);
define('PATH_TEMP', PATH_PUBLIC.'temp'.DS);

define('PATH_FW', PATH_ROOT.'framework'.DS);
define('PATH_CORE', PATH_FW.'core'.DS); // Core framework path
define('PATH_LIB', PATH_FW.'lib'.DS); // Libaries framework path
define('PATH_HELPERS', PATH_LIB.'Helpers'.DS);

define('PATH_MODULES', PATH_ROOT.'modules'.DS); // Modules path
define('PATH_LOG', PATH_APP.'logs'.DS); // Logs path
define('PATH_CACHE', PATH_APP.'cache'.DS); // Cache path

define('PATH_G_VIEWS', PATH_APP.'views'.DS); // Global views path
// styles and images path
define('PATH_CSS', '/css/');
define('PATH_IMAGES', '/images/');

// show all errors if development mode is on
//if(\Plethora\Core::getAppMode() == \Plethora\Core::MODE_DEVELOPMENT) {
error_reporting(E_ALL);
ini_set('display_errors', '1');

//require_once PATH_LIB.'KintDebug/Kint.class.php';
//}
// Load global functions
require PATH_CORE.'functions.php';

// show content
if(file_exists('./install.php')) {
    require_once PATH_PUBLIC.'install.php';
} else {
    if(\Plethora\Core::getAppMode() == \Plethora\Core::MODE_DEVELOPMENT) {
        \Plethora\Core::startApp();
    } else {
        try {
            \Plethora\Core::startApp();
        } catch(\Plethora\Exception $e) {
            $e->handler();
        }
    }
}

// destruct Log instance
\Plethora\Log::destruct();
