<?php

/**
 * Autoloader function which autoload all classes needed to install this
 * framework.
 *
 * @package     framework
 * @subpackage  install
 * @author      Krzysztof Trzos <krzysztof.trzos@gieromaniak.pl>
 * @param       string $sClassName
 * @return      bool
 * @since       1.0.0-alpha
 * @version     1.0.0-alpha
 */
function plethoraInstallAutoload($sClassName) {
    # Doctrine
    if(strpos($sClassName, 'Doctrine') !== FALSE) {
        return TRUE;
    }

    # start creating the path based on class name
    $sPathPrefix = str_replace(['_', '/', '\\'], [DS, DS, DS], $sClassName);

    if($sPathPrefix != $sClassName) {
        $sClassName = $sPathPrefix;
    }

    # install files
    if(file_exists($sPath = PATH_INSTALL.'classes'.DS.$sClassName.'.php')) {
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

    return FALSE;
}

if(file_exists(PATH_ROOT.DS.'vendor/autoload.php')) {
    require_once PATH_ROOT.DS.'vendor/autoload.php';
}