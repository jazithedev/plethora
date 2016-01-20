<?php

namespace Plethora;

use Plethora\Router;
use Plethora\Helper;

/**
 * Class for loading and holding configuration files data.
 *
 * @package        Plethora
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Config {

    /**
     * @static
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $aConfigs = [];

    /**
     * @static
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private static $aLoadedConfigs = [];

    /**
     * Load config file
     *
     * Get array from config file and save it to variable
     *
     * @static
     * @access   public
     * @param    string $sConfigPath
     * @param    string $sFormat
     * @return   bool
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function load($sConfigPath, $sFormat = 'php') {
        $oConfigData = static::findConfigFile($sConfigPath, $sFormat);

        // load config data
        if($oConfigData !== FALSE) {
            switch($sFormat) {
                # PHP
                case 'php':
                    $aConfig = include($oConfigData->getPath());
                    break;
                # YAML
                case "yml":
                    $aConfig = \Spyc::YAMLLoad($oConfigData->getPath());
                    break;
            }
        }

        // assign data to storage
        if(isset($aConfig)) {
            Helper\Arrays::createMultiKeys(static::$aConfigs, $sConfigPath, $aConfig);

            unset($aConfig);

            Log::insert('Config '.$sConfigPath.' ('.$sFormat.') loaded');

            return TRUE;
        }

        // if there is no data to assign (because the config file does not exists), create ERROR message and return FALSE (or throw exception)
        $sMsg = 'Unable to load '.$sConfigPath.' config file with "'.$sFormat.'" format.';

        Log::insert($sMsg, Log::ERROR);

        if(Core::getAppMode() === Core::MODE_DEVELOPMENT) {
            throw new Exception($sMsg);
        }

        return FALSE;
    }

    /**
     * @static
     * @access   private
     * @param    string $sConfigPath
     * @param    string $sFormat
     * @return   bool|Config\ConfigFileData
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function findConfigFile($sConfigPath, $sFormat = 'php') {
        // firstly, check if particular config file is in application directory
        $sPathToAppConfig = PATH_APP.'config'.DS.str_replace('.', DS, $sConfigPath).'.'.$sFormat;

        if(file_exists($sPathToAppConfig)) {
            return new Config\ConfigFileData($sPathToAppConfig);
        } // if not, check config file for particular module (first string located before dot in $sConfigPath variable)
        else {
            $aExploded   = explode('.', $sConfigPath);
            $sModule     = array_shift($aExploded);
            $sConfigPath = implode(DS, $aExploded);

            try {
                $sPathToModuleConfig = PATH_MODULES.Router::getModuleGroup($sModule).DS.$sModule.DS.'config'.DS.$sConfigPath.'.'.$sFormat;

                if(file_exists($sPathToModuleConfig)) {
                    return new Config\ConfigFileData($sPathToModuleConfig, $sModule);
                }
            } catch(Exception\Fatal $e) {

            }
        }

        return FALSE;
    }

    /**
     * Get a value from array list
     *
     * @static
     * @access   public
     * @param    string $sWholePath Config name in format [module.]fileName.array.nextArray.value(...)
     * @param    mixed  $mDefault
     * @param    bool   $bForcePath
     * @return   mixed
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function get($sWholePath, $mDefault = NULL, $bForcePath = FALSE) {
        if(!in_array($sWholePath, static::$aLoadedConfigs)) {
            static::$aLoadedConfigs[] = $sWholePath;

            $sPathToConfig = $bForcePath ? $sWholePath : static::modifyConfigPathToFilePath($sWholePath);
            static::load($sPathToConfig);
        }

        return Helper\Arrays::path(static::$aConfigs, $sWholePath, $mDefault);
    }

    /**
     * @static
     * @access   private
     * @param    string $sPath
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function modifyConfigPathToFilePath($sPath) {
        $aExploded = explode('.', $sPath);
        $sTmp      = $sPath;

        while(TRUE) {
            if(static::findConfigFile($sTmp) !== FALSE) {
                return $sTmp;
            }

            if(empty($aExploded)) {
                break;
            }

            array_pop($aExploded);
            $sTmp = implode('.', $aExploded);
        }

        return $sPath;
    }

}
