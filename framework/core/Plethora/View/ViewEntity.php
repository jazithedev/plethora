<?php

namespace Plethora\View;

use Plethora\ModelCore;
use Plethora\View;
use Plethora\Exception;

/**
 * @package        Plethora
 * @subpackage     View
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ViewEntity {

    /**
     * Field path to View.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sView = 'base/view/entity';

    /**
     * Field path to View.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sViewField = 'base/view/field';

    /**
     * Array of fields of this entity.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aFields = [];

    /**
     * HTML class which identifies particular node in the DOM.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sHtmlClass = NULL;

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
     * List configurator.
     *
     * @access    private
     * @var        ViewEntity\Configurator
     * @since     1.0.0-alpha
     */
    private $oConfig = NULL;

    /**
     * Factory method.
     *
     * @access     public
     * @param    ViewEntity\Configurator $oConfig
     * @return    ViewEntity
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory(ViewEntity\Configurator $oConfig) {
        return new ViewEntity($oConfig);
    }

    /**
     * Constructor.
     *
     * @access     public
     * @param    ViewEntity\Configurator $oConfig
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct(ViewEntity\Configurator $oConfig) {
        $this->oConfig = $oConfig;

        // set default profile (if is NULL)
        if($this->getConfigurator()->getProfile() === NULL) {
            $this->getConfigurator()->setProfile('singleton');
        }

        // get html class
        $sChangedClass    = str_replace(['\\', 'Model_'], ['_', ''], $this->getConfigurator()->getEntity()->getClass());
        $this->sHtmlClass = strtolower($sChangedClass);

        // prepare fields to view
        $this->prepareFields($this->getConfigurator()->getFields());
    }

    /**
     * Prepare all fields.
     *
     * @access   private
     * @param    array $aFieldList
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function prepareFields(array $aFieldList) {
        $oEntity = $this->getConfigurator()->getEntity();

        // if field list is empty
        if(empty($aFieldList)) {
            $aFieldList = $oEntity->getFieldsNames();
        }

        // check if entity has locales
        $oLocales = $oEntity->hasLocales() ? $oEntity->getLocales() : FALSE;
        /* @var $oLocales \Plethora\ModelCore\Locales */

        // check all fields
        foreach($aFieldList as $sFieldKey => $mFieldValue) {
            $sField = is_array($mFieldValue) ? $sFieldKey : $mFieldValue;

            if($oEntity->hasField($sField) || ($oLocales !== FALSE && $oLocales->hasField($sField))) {
                $this->aFields[$sField] = ViewField::factory($this, $sField, $oEntity);
            } else {
                continue;
            }
        }
    }

    /**
     * Get configurator of this ViewList.
     *
     * @access     public
     * @return    ViewEntity\Configurator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getConfigurator() {
        return $this->oConfig;
    }

    /**
     * @access     public
     * @param    string $sValue
     * @return    ViewEntity
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setViewPath($sValue) {
        $this->sView = $sValue;

        return $this;
    }

    /**
     * Get path to view template of this entity view.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getViewPath() {
        return $this->sView;
    }

    /**
     * Get path to field View.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getViewFieldPath() {
        return $this->sViewField;
    }

    /**
     * Set path to field View.
     *
     * @access     public
     * @param    string $sValue
     * @return    ViewEntity
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setViewFieldPath($sValue) {
        $this->sViewField = $sValue;

        return $this;
    }

    /**
     * Get View of this entity.
     *
     * @access     public
     * @return    View
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getView() {
        return View::factory($this->sView)
            ->set('oModel', $this->getModel())
            ->set('sHtmlClass', $this->sHtmlClass)
            ->set('aFields', $this->aFields)
            ->set('sPrefix', $this->sPrefix)
            ->set('sSuffix', $this->sSuffix)
            ->set('sProfile', $this->getConfigurator()->getProfile());
    }

    /**
     * Get array of entity fields to show.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getFields() {
        return $this->aFields;
    }

    /**
     * Get single ViewField.
     *
     * @access     public
     * @param    string $sField
     * @return    ViewField
     * @throws    Exception\Fatal
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function &getField($sField) {
        if(isset($this->aFields[$sField])) {
            return $this->aFields[$sField];
        } else {
            throw new Exception\Fatal('Field "'.$sField.'" do not exists.');
        }
    }

    /**
     * Get last field.
     *
     * @access     public
     * @return    ViewField
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getLastField() {
        return end($this->aFields);
    }

    /**
     * Get HTML class.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getHtmlClass() {
        return $this->sHtmlClass;
    }

    /**
     * Get Model.
     *
     * @access     public
     * @return    ModelCore
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getModel() {
        return $this->getConfigurator()->getEntity();
    }

    /**
     * Set prefix for this entity.
     *
     * @access     public
     * @param    string $sValue
     * @return    ViewEntity
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
     * @return    ViewEntity
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
     * @return    ViewEntity
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
     * @return    ViewEntity
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addToSuffix($sValue) {
        $this->sSuffix .= $sValue;

        return $this;
    }

    /**
     * Add new field container.
     *
     * @access     public
     * @param    string $sFirstField
     * @param    string $sLastField
     * @return    ViewEntity
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addFieldContainer($sFirstField, $sLastField) {
        foreach($this->getFields() as $sFieldName => $oField) {
            /* @var $oField \Plethora\View\ViewField */
            if($sFieldName === $sFirstField) {
                $oField->addToPrefix('<div class="entity_field_container entity_field_container_'.$sFirstField.'_'.$sLastField.'">');
            } elseif($sFieldName === $sLastField) {
                $oField->addToSuffix('</div>');
            }
        }

        return $this;
    }

    /**
     * Add new field imitation.
     *
     * @access     public
     * @param    ViewFieldImitation $oFieldImitation
     * @param    string             $sBeforeField
     * @return    ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addFieldImitation(ViewFieldImitation $oFieldImitation, $sBeforeField) {
        $oFieldImitation->setEntity($this);

        $sViewOutput = $oFieldImitation->getView()->render();
        $oField      = empty($sBeforeField) ? $this->getLastField() : $this->getField($sBeforeField);

        $oField->addToSuffix($sViewOutput);

        return $this;
    }

}
