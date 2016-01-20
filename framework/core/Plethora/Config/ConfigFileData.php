<?php

namespace Plethora\Config;

/**
 * Config data class
 *
 * @package        Plethora
 * @subpackage     Config
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ConfigFileData {
    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $sPath = NULL;

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $sModule;

    /**
     * Constructor
     *
     * @access   public
     * @param    string $sPath
     * @param    string $sModule
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($sPath, $sModule = NULL) {
        $this->setPath($sPath);
        $this->setModule($sModule);
    }

    /**
     * @access     public
     * @param    string $sPath
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setPath($sPath) {
        $this->sPath = $sPath;
    }

    /**
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getPath() {
        return $this->sPath;
    }

    /**
     * @access     public
     * @param    string $sModule
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setModule($sModule) {
        $this->sModule = $sModule;
    }

    /**
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getModule() {
        return $this->sModule;
    }
}