<?php

namespace Plethora\Helper\SearchEngine;

use Plethora\Helper;

/**
 * 
 * 
 * @package        Plethora
 * @subpackage     Form\Separator
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class RelFieldInfo extends Helper {
    /**
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sVar;

    /**
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sClass;

    /**
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sOriginalName;

    /**
     * @access     public
     * @param    string $sOriginalName
     * @param    string $sRelatedVariableFromPrimary
     * @param    string $sModelClassName
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct($sOriginalName, $sRelatedVariableFromPrimary, $sModelClassName) {
        $this->sOriginalName = $sOriginalName;
        $this->sVar          = $sRelatedVariableFromPrimary;
        $this->sClass        = $sModelClassName;
    }

    /**
     * @static
     * @access     public
     * @param    string $sOriginalName
     * @param    string $sRelatedVariableFromPrimary
     * @param    string $sModelClassName
     * @return    \Plethora\Helper\SearchEngine\RelFieldInfo
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory($sOriginalName, $sRelatedVariableFromPrimary, $sModelClassName) {
        return new RelFieldInfo($sOriginalName, $sRelatedVariableFromPrimary, $sModelClassName);
    }

    /**
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getOriginalName() {
        return $this->sOriginalName;
    }

    /**
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getVar() {
        return $this->sVar;
    }

    /**
     * Get module class name.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getModuleClassName() {
        return $this->sClass;
    }
}