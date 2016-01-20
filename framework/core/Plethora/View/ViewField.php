<?php

namespace Plethora\View;

use Plethora\ModelCore;
use Plethora\View;
use Plethora\Exception;
use Doctrine\ORM;

/**
 * @package        Plethora
 * @subpackage     View
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ViewField {

    /**
     * Field name.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sName = NULL;

    /**
     * Field value.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $mValue = NULL;

    /**
     * Field original value (before formatting).
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $mOriginalValue = NULL;

    /**
     * Type from model.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sTypeFromModel = NULL;

    /**
     * Parent entity to which this field belongs to.
     *
     * @access    private
     * @var        ViewEntity
     * @since     1.0.0-alpha
     */
    private $oEntity = NULL;

    /**
     * Field model, to which it belongs.
     *
     * @access    private
     * @var        ModelCore
     * @since     1.0.0-alpha
     */
    private $oModel = NULL;

    /**
     * This variable tell to show or not to show field's label.
     *
     * @access    private
     * @var        boolean
     * @since     1.0.0-alpha
     */
    private $bShowLabel = FALSE;

    /**
     * This variable specifies which heading tag will be used, if label is set
     * to be created as heading.
     *
     * @access    private
     * @var        integer
     * @since     1.0.0-alpha
     */
    private $iLabelAsHeading = 0;

    /**
     * Particular entity prefix.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sPrefix = NULL;

    /**
     * Particular entity suffix.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sSuffix = NULL;

    /**
     * Factory method.
     *
     * @access     public
     * @param    ViewEntity $oEntity
     * @param    string     $sField
     * @param    ModelCore  $oModel
     * @return    ViewField
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory(ViewEntity $oEntity, $sField, ModelCore &$oModel) {
        return new ViewField($oEntity, $sField, $oModel);
    }

    /**
     * Constructor.
     *
     * @access   public
     * @param    ViewEntity $oEntity
     * @param    string     $sFieldName
     * @param    ModelCore  $oModel
     * @throws   ORM\Mapping\MappingException
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct(ViewEntity $oEntity, $sFieldName, ModelCore &$oModel) {
        $this->oEntity = $oEntity;
        $this->sName   = $sFieldName;
        $this->oModel  = $oModel;

        // check if entity has locales
        $sTypeFromModel = NULL;
        $oLocales       = $this->oModel->hasLocales() ? $this->oModel->getLocales() : FALSE;
        /* @var $oLocales \Plethora\ModelCore\Locales */

        // get data form field view
        if($this->oModel->hasField($sFieldName)) {
            $this->mValue = $this->oModel->{$sFieldName};

            if(in_array($sFieldName, $this->oModel->getMetadata()->getFieldNames())) {
                $aMapping       = $this->oModel->getMetadata()->getFieldMapping($sFieldName);
                $sTypeFromModel = $aMapping['type'];
            } else {
                $sTypeFromModel = 'associacion';
            }
        } elseif($oLocales !== FALSE && $oLocales->hasField($sFieldName)) {
            $this->mValue = $oLocales->{$sFieldName};

            if(in_array($sFieldName, $oLocales->getMetadata()->getFieldNames())) {
                $aMapping       = $oLocales->getMetadata()->getFieldMapping($sFieldName);
                $sTypeFromModel = $aMapping['type'];
            } else {
                $sTypeFromModel = 'associacion';
            }
        }

        $this->sTypeFromModel = $sTypeFromModel;

        // change value of field if it's associacion
        if($this->sTypeFromModel === 'associacion') {
            if($this->oEntity->getConfigurator()->getCurrentLevel() < $this->oEntity->getConfigurator()->getMaxLevel()) {
                $oAssociacion = $this->mValue;
                /* @var $oAssociacion ModelCore */
                $aEntityFields = $this->getEntity()->getConfigurator()->getFields();

                if(!isset($aEntityFields[$this->getName()])) {
                    $this->mValue = $oAssociacion;
                } elseif($oAssociacion instanceof ORM\PersistentCollection) {
                    /* @var $oCollection ORM\PersistentCollection */
                    $oCollection = $oAssociacion;

                    if($oCollection->count() > 0) {
                        $aList             = $oCollection->toArray();
                        $oTmpModelRelation = array_shift($aList);
                        /* @var $oTmpModelRelation ModelCore */
                        array_unshift($aList, $oTmpModelRelation);

                        unset($oTmpModelRelation);

                        $oConfigurator = ViewList\Configurator::factory()
                            ->setList($aList)
                            ->setFields($aEntityFields[$this->getName()])
                            ->setCurrentLevel($this->oEntity->getConfigurator()->getCurrentLevel() + 1)
                            ->setMaxLevel($this->oEntity->getConfigurator()->getMaxLevel())
                            ->setProfile($this->oEntity->getConfigurator()->getProfile());

                        $this->mValue = ViewList::factory($oConfigurator)
                            ->getView()
                            ->render();
                    }
                } elseif($oAssociacion instanceof ModelCore) {
                    /* @var $oAssociacion ModelCore */

                    $oConfigurator = ViewEntity\Configurator::factory($oAssociacion)
                        ->setFields($aEntityFields[$this->getName()])
                        ->setCurrentLevel($this->oEntity->getConfigurator()->getCurrentLevel() + 1)
                        ->setMaxLevel($this->oEntity->getConfigurator()->getMaxLevel())
                        ->setProfile($this->oEntity->getConfigurator()->getProfile());

                    $this->mValue = ViewEntity::factory($oConfigurator)
                        ->getView()
                        ->render();
                }
            } else {
                $this->mValue = '!ENTITY TREE LEVEL REACHED!';
            }
        }

        // if it's not an associacion, format value of particular field
        if($this->mValue !== NULL) {
            $aFormatters = $this->oModel->getConfig()->getFieldFormatters($this->sName);
            $sProfile    = $this->getEntity()->getConfigurator()->getProfile();

            $this->mOriginalValue = $this->mValue;

            foreach($aFormatters as $oFormatter) {
                /* @var $oFormatter \Plethora\View\FieldFormatter */
                if($oFormatter->isAvailableFor($sProfile)) {
                    $oFormatter->setField($this);

                    if(is_array($this->mValue)) {
                        $this->mValue = $oFormatter->formatArray($this->mValue);
                    } elseif($this->mValue instanceof ORM\PersistentCollection) {
                        $mValueToRefactor = $this->mValue;
                        /* @var $mValueToRefactor ORM\PersistentCollection */

                        $this->mValue = $oFormatter->formatArray($mValueToRefactor->toArray());
                    } else {
                        $this->mValue = $oFormatter->format($this->mValue);
                    }
                }
            }

            if(is_array($this->mValue) || is_object($this->mValue) && !method_exists($this->mValue, '__toString')) {
                throw new Exception\Fatal(__('Field value ":file" need to be formatted to string format (:type at the moment).', ['file' => $this->sName, 'type' => gettype($this->mValue)]));
            }
        }
    }

    /**
     * Get field View.
     *
     * @access   public
     * @return   View
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getView() {
        $sFieldViewPath = $this->getEntity()->getViewFieldPath();

        $sFieldViewPath1 = $sFieldViewPath.'__'.$this->getEntity()->getHtmlClass();
        $sFieldViewPath2 = $sFieldViewPath1.'__'.$this->getTypeOfModel();
        $sFieldViewPath3 = $sFieldViewPath1.'__'.$this->getName();

        if(View::viewExists($sFieldViewPath1)) {
            $sFinalPath = $sFieldViewPath1;
        } elseif(View::viewExists($sFieldViewPath2)) {
            $sFinalPath = $sFieldViewPath2;
        } elseif(View::viewExists($sFieldViewPath3)) {
            $sFinalPath = $sFieldViewPath3;
        } else {
            $sFinalPath = $sFieldViewPath;
        }

        return View::factory($sFinalPath)
            ->bind('oField', $this)
            ->bind('sPrefix', $this->sPrefix)
            ->bind('sSuffix', $this->sSuffix);
    }

    /**
     * Get entity to which particular field belongs to.
     *
     * @access     public
     * @return    ViewEntity
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getEntity() {
        return $this->oEntity;
    }

    /**
     * Get fields name.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getName() {
        return $this->sName;
    }

    /**
     * Get field type of model.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getTypeOfModel() {
        return $this->sTypeFromModel;
    }

    /**
     * Get value of field.
     *
     * @access     public
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getValue() {
        return $this->mValue;
    }

    /**
     * Show label of this field.
     *
     * @access     public
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function showLabel() {
        $this->bShowLabel = TRUE;

        return $this;
    }

    /**
     * Check if field label is visible.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isLabelVisible() {
        return $this->bShowLabel;
    }

    /**
     * Set flag to tell whether label will be created as heading or not.
     *
     * @access   public
     * @param    integer $iHeadingType
     * @return   $this
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setLabelAsHeading($iHeadingType = 2) {
        if(!is_int($iHeadingType) || $iHeadingType < 0 || $iHeadingType > 6) {
            throw new Exception\Fatal('Inappropriate value. Must be integer with values from 1 to 6.');
        }

        $this->iLabelAsHeading = $iHeadingType;

        return $this;
    }

    /**
     * Get type of heading which will be created as this field label.
     *
     * @access     public
     * @return    integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getLabelHeadingType() {
        return $this->iLabelAsHeading;
    }

    /**
     * Get label value.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getLabel() {
        $sLabel   = '';
        $oModel   = $this->getEntity()->getModel();
        $sField   = $this->getName();
        $oLocales = $oModel->hasLocales() ? $oModel->getLocales() : FALSE;
        /* @var $oLocales \Plethora\ModelCore\Locales */

        if($oModel->hasField($sField)) {
            $oField = $oModel->getConfigForField($sField);
            /* @var $oField \Plethora\Form\Field */
            $sLabel = ($oField !== FALSE) ? $oField->getLabel() : NULL;
        } elseif($oLocales !== FALSE && $oLocales->hasField($sField)) {
            $oLocaleField = $oLocales->getConfigForField($sField);
            /* @var $oLocaleField \Plethora\Form\Field */
            $sLabel = ($oLocaleField !== FALSE) ? $oLocaleField->getLabel() : NULL;
        }

        if(empty($sLabel)) {
            $sLabel = __('label.model.'.$this->getEntity()->getHtmlClass().'.'.$sField);
        }

        if($this->iLabelAsHeading !== 0) {
            $sLabel = '<h'.$this->iLabelAsHeading.'>'.$sLabel.'</h'.$this->iLabelAsHeading.'>';
        }

        return $sLabel;
    }

    /**
     * Set prefix for this entity.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setPrefix($sValue) {
        $this->sPrefix = $sValue;

        return $this;
    }

    /**
     * Add new string to the prefix of particular entity.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addToPrefix($sValue) {
        $this->sPrefix .= $sValue;

        return $this;
    }

    /**
     * Set suffix for this entity.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setSuffix($sValue) {
        $this->sSuffix = $sValue;

        return $this;
    }

    /**
     * Add new string to the suffix of particular entity.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addToSuffix($sValue) {
        $this->sSuffix .= $sValue;

        return $this;
    }

    /**
     * Get original value (before formatting is done) of this field.
     *
     * @access     public
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getOriginalValue() {
        return $this->mOriginalValue;
    }

}
