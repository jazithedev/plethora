<?php

namespace Plethora\View\ViewList;

/**
 * Configurator for Entities lists.
 *
 * @package        Plethora
 * @subpackage     View\ViewList
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Configurator {
    /**
     * List with Model entities.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aModelEntitiesList = [];

    /**
     * Array of entities fields which will be viewed on the list.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aFieldsList = [];

    /**
     * The number of the max node level.
     *
     * @access    private
     * @var        integer
     * @since     1.0.0-alpha
     */
    private $iMaxLevel = 1;

    /**
     * Current level of the node.
     *
     * @access    private
     * @var        integer
     * @since     1.0.0-alpha
     */
    private $iCurrentLevel = 0;

    /**
     * Profile of this list.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sProfile = NULL;

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @param    array $list
     * @return   Configurator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory(array $list) {
        return new Configurator($list);
    }

    /**
     * Configurator constructor.
     *
     * @access  public
     * @param   array $list
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function __construct(array $list) {
        $this->aModelEntitiesList = $list;
    }

    /**
     * Get list of Model entities.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getList() {
        return $this->aModelEntitiesList;
    }

    /**
     * Set array of fields (belong to Model) which will be viewed on the list.
     *
     * @access     public
     * @param    array $aFields
     * @return    Configurator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setFields(array $aFields) {
        $this->aFieldsList = $aFields;

        return $this;
    }

    /**
     * Get array of fields (belong to Model) which will be viewed on the list.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFields() {
        return $this->aFieldsList;
    }

    /**
     * Set maximal level of the nodes tree.
     *
     * @access     public
     * @param    integer $iLevel
     * @return    Configurator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setMaxLevel($iLevel) {
        $this->iMaxLevel = $iLevel;

        return $this;
    }

    /**
     * Get maximal level of the nodes tree.
     *
     * @access     public
     * @return    integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getMaxLevel() {
        return $this->iMaxLevel;
    }

    /**
     * Set current level.
     *
     * @access     public
     * @param    integer $iLevel
     * @return    Configurator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setCurrentLevel($iLevel) {
        $this->iCurrentLevel = $iLevel;

        return $this;
    }

    /**
     * Get current level.
     *
     * @access     public
     * @return    integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getCurrentLevel() {
        return $this->iCurrentLevel;
    }

    /**
     * Set profile.
     *
     * @access     public
     * @param    string $sProfile
     * @return    Configurator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setProfile($sProfile) {
        $this->sProfile = $sProfile;

        return $this;
    }

    /**
     * Get profile.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getProfile() {
        return $this->sProfile;
    }
}