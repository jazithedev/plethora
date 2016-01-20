<?php

namespace Plethora\View\ViewEntity;

use Plethora\ModelCore;
use Plethora\View;

/**
 * Configurator for Entities lists.
 *
 * @package        Plethora
 * @subpackage     View\ViewEntity
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Configurator {

    /**
     * Model instance.
     *
     * @access  private
     * @var     ModelCore
     * @since   1.0.0-alpha
     */
    private $oModelEntity = NULL;

    /**
     * List of model entity fields.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFieldsList = [];

    /**
     * The number of the max node level.
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $iMaxLevel = 1;

    /**
     * Current level of the node.
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $iCurrentLevel = 0;

    /**
     * Profile of this list.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $sProfile = NULL;

    /**
     * Reference to the list in which particular entity is located (if is in list).
     *
     * @access  private
     * @var     View\ViewList
     * @since   1.0.0-alpha
     */
    private $oListReference = NULL;

    /**
     * Factory method.
     *
     * @static
     * @access   public
     * @param    ModelCore $entity
     * @return   Configurator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory(ModelCore $entity) {
        return new Configurator($entity);
    }

    /**
     * Configurator constructor.
     *
     * @access  public
     * @param   ModelCore $entity
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function __construct(ModelCore $entity) {
        $this->oModelEntity = $entity;
    }

    /**
     * Set array of fields (belong to Model) which will be viewed on the list.
     *
     * @access   public
     * @param    array $aFields
     * @return   Configurator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFields(array $aFields) {
        $this->aFieldsList = $aFields;

        return $this;
    }

    /**
     * Get array of fields (belong to Model) which will be viewed on the list.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFields() {
        return $this->aFieldsList;
    }

//	/**
//	 * Set Model entity.
//	 *
//	 * @access   public
//	 * @param    Model $oModel
//	 * @return   Configurator
//	 * @since    1.0.0-alpha
//	 * @version  1.0.0-alpha
//	 */
//	public function setEntity(Model $oModel) {
//		$this->oModelEntity = $oModel;
//
//		return $this;
//	}

    /**
     * Get Model entity.
     *
     * @access   public
     * @return   ModelCore
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getEntity() {
        return $this->oModelEntity;
    }

    /**
     * Set maximal level of the nodes tree.
     *
     * @access   public
     * @param    integer $iLevel
     * @return   Configurator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setMaxLevel($iLevel) {
        $this->iMaxLevel = $iLevel;

        return $this;
    }

    /**
     * Get maximal level of the nodes tree.
     *
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMaxLevel() {
        return $this->iMaxLevel;
    }

    /**
     * Set current level.
     *
     * @access   public
     * @param    integer $iLevel
     * @return   Configurator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setCurrentLevel($iLevel) {
        $this->iCurrentLevel = $iLevel;

        return $this;
    }

    /**
     * Get current level.
     *
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getCurrentLevel() {
        return $this->iCurrentLevel;
    }

    /**
     * Set profile.
     *
     * @access   public
     * @param    string $sProfile
     * @return   Configurator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setProfile($sProfile) {
        $this->sProfile = $sProfile;

        return $this;
    }

    /**
     * Get profile.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getProfile() {
        return $this->sProfile;
    }

    /**
     * Set reference to ViewList instance.
     *
     * @access   public
     * @param    View\ViewList $oList
     * @return   Configurator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setViewListReference(View\ViewList $oList) {
        $this->oListReference = $oList;

        return $this;
    }

    /**
     * Get reference to ViewList instance.
     *
     * @access   public
     * @return   View\ViewList
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getViewListReference() {
        return $this->oListReference;
    }

}
