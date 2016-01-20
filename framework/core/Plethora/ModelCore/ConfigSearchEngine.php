<?php

namespace Plethora\ModelCore;

/**
 * Config data class
 *
 * @package        Plethora
 * @subpackage     Model
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ConfigSearchEngine {
    /**
     * Array of fields to be joined from model relations.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFromRels = [];

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @return   ConfigSearchEngine
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory() {
        return new ConfigSearchEngine;
    }

    /**
     * Add field to search engine which will be loaded from related with
     * particular model.
     *
     * @access   public
     * @param    string $sJoin
     * @param    string $sField
     * @return   ConfigSearchEngine
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addFromRel($sJoin, $sField) {
        if(!isset($this->aFromRels[$sJoin])) {
            $this->aFromRels[$sJoin] = [];
        }

        if(!in_array($sField, $this->aFromRels[$sJoin])) {
            $this->aFromRels[$sJoin][] = $sField;
        }

        return $this;
    }

    /**
     * Return all fields which will be added to search engine.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getRelsFields() {
        return $this->aFromRels;
    }
}