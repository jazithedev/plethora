<?php

use Plethora\Core;
use Plethora\I18n;
use Plethora\Exception;
use Plethora\Router;

/**
 * Autoloader function which autoload libraries classes.
 *
 * @author   Krzysztof Trzos
 * @param    string $sClassName
 * @return   bool
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
function plethoraAutoload($sClassName)
{
    # Doctrine
    if(strpos($sClassName, 'Doctrine') !== FALSE) {
        return TRUE;
    }

    # start creating the path based on class name
    $sPathPrefix = str_replace(['_', '/', '\\'], [DS, DS, DS], $sClassName);

    if($sPathPrefix != $sClassName) {
        $sClassName = $sPathPrefix;
    }

    # application classes
    if(file_exists($sPath = PATH_APP.'classes'.DS.$sClassName.'.php')) {
        require_once $sPath;

        return TRUE;
    }

    # core
    if(file_exists($sPath = PATH_CORE.$sClassName.'.php')) {
        require_once $sPath;

        return TRUE;
    }

    # libraries
    if(file_exists($sPath = PATH_LIB.$sClassName.DS.$sClassName.'.php')) {
        require_once $sPath;

        return TRUE;
    }

    # other libs / helpers
    if(file_exists($sPath = PATH_LIB.$sClassName.'.php')) {
        require_once $sPath;

        return TRUE;
    }

    # other classes in modules
    foreach(Router::getModules() as $aModuleData) {
        $sPath = PATH_MODULES.$aModuleData['path'].DS.'classes'.DS.$sPathPrefix.'.php';
        if(file_exists($sPath)) {
            require_once $sPath;

            return TRUE;
        }
    }

    return FALSE;
}

spl_autoload_register('plethoraAutoload');

if(file_exists(PATH_ROOT.DS.'vendor/autoload.php')) {
    require_once PATH_ROOT.DS.'vendor/autoload.php';
}

/**
 * Fatal errors handler.
 *
 * @author   Krzysztof Trzos
 * @throws   Exception\Fatal
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
function fatal_errors_handler()
{
    $aError = error_get_last();

    if(
        !is_null($aError) &&
        isset($aError['message']) &&
        substr($aError['message'], 0, 18) != 'Uncaught exception'
    ) {
        $iLevel = ob_get_level();

        for($i = 1; $i < $iLevel; $i++) {
            ob_get_clean();
        }

        if(Core::getAppMode() == Core::MODE_DEVELOPMENT) {
           ddd($aError);

            return FALSE;
        } else {
            try {
                throw new Exception\Fatal($aError['message']);
            } catch(Exception $e) {
                $e->handler();
            }
        }
    }
}

/**
 * Errors handler.
 *
 * @author   Krzysztof Trzos
 * @param    integer $errno
 * @param    string  $errstr
 * @param    string  $errfile
 * @param    integer $errline
 * @param    array   $errcontext
 * @throws   Exception\Fatal
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
function error_handler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = [])
{
    $iLevel = ob_get_level();

    for($i = 1; $i < $iLevel; $i++) {
        ob_get_clean();
    }

    if(Core::getAppMode() == Core::MODE_DEVELOPMENT) {
        \Kint::trace();
        ddd($errno, $errstr, $errfile, $errline, $errcontext);
    } else {
        try {
            throw new Exception\Fatal();
        } catch(Exception $e) {
            $e->handler();
        }
    }
}

// turn on error handlers
set_error_handler('error_handler');
register_shutdown_function('fatal_errors_handler');

/**
 * Translating method.
 *
 * @author   Krzysztof Trzos
 * @access   public
 * @param    string $toTranslate
 * @param    array  $params
 * @param    array  $options
 * @return   string
 * @since    1.0.0-alpha
 * @version  1.0.0-alpha
 */
function __($toTranslate, $params = [], $options = [])
{
    $output = $toTranslate;

    if(Router::hasModule('i18n')) {
        $output = I18n\Core::get($toTranslate, $options);
    }

    foreach($params as $sParamName => $aParamValue) {
        $output = str_replace(':'.$sParamName, $aParamValue, $output);
    }

    return $output;
}
