<?php

namespace Plethora\View;

use Plethora\View;

/**
 * @package        Plethora
 * @subpackage     View
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ViewList {

    /**
     * Path to default list View.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sViewPathListDefault = 'base/view/list_divs';

    /**
     * Path to default View with list single row.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sViewPathSingleRowDefault = 'base/view/list/single_row_divs';

    /**
     * Path to default View with list single field.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sViewPathFieldDefault = 'base/view/field';

    /**
     * List of entities AFTER preparation.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aListPrepared = [];

    /**
     * Model class.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sModelClass = NULL;

    /**
     * Path to list View.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sViewPathList = NULL;

    /**
     * Path to View of list single row.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sViewPathSingleRow = NULL;

    /**
     * HTML class which identifies particular node in the DOM.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sHtmlClass = '';

    /**
     * Particular entity list prefix.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sPrefix = '';

    /**
     * Particular entity listsuffix.
     *
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sSuffix = '';

    /**
     * List configurator.
     *
     * @access    private
     * @var        ViewList\Configurator
     * @since     1.0.0-alpha
     */
    private $oConfig = NULL;

    /**
     * Factory method.
     *
     * @access     public
     * @param    ViewList\Configurator $oConfig
     * @return ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public static function factory(ViewList\Configurator $oConfig) {
        return new ViewList($oConfig);
    }

    /**
     * Constructor.
     *
     * @access     public
     * @param    ViewList\Configurator $oConfig
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function __construct(ViewList\Configurator $oConfig) {
        $this->oConfig            = $oConfig;
        $this->sViewPathList      = $this->sViewPathListDefault;
        $this->sViewPathSingleRow = $this->sViewPathSingleRowDefault;

        // set default profile (if is NULL)
        if($this->getConfigurator()->getProfile() === NULL) {
            $this->getConfigurator()->setProfile('list');
        }

        // prepare fields
        $aList   = $this->getConfigurator()->getList();
        $aFields = $this->getConfigurator()->getFields();

        if(!empty($aList)) {
            // get model class
            $oFirstElement = reset($aList);
            /* @var $oFirstElement \Plethora\ModelCore */
            $this->sModelClass = $oFirstElement->getClass();

            // set particular list fields
            if(empty($aFields)) {
                $this->getConfigurator()->setFields(call_user_func([$this->sModelClass, 'getListFields']));
            }

            // prepare list
            $this->prepare();
        }
    }

    /**
     * Prepare list of entities for \Plethora\View.
     *
     * @access     private
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    private function prepare() {
        // get html class
        $sChangedClass    = str_replace(['\\', 'Model_'], ['_', ''], $this->getModelClassName());
        $this->sHtmlClass = strtolower($sChangedClass);

        // set path to View to single row for particular Model
        $sViewSingleRowByClass = $this->sViewPathSingleRowDefault.'__'.$this->sHtmlClass;

        if(View::viewExists($sViewSingleRowByClass)) {
            $this->sViewPathSingleRow = $sViewSingleRowByClass;
        }

        // set path to View for the whole list
        $sViewListByClass = $this->sViewPathListDefault.'__'.$this->sHtmlClass;

        if(View::viewExists($sViewListByClass)) {
            $this->sViewPathList = $sViewListByClass;
        }

        // prepare entities
        foreach($this->getConfigurator()->getList() as $oEntityFromModel) {
            /* @var $oEntityFromModel \Plethora\ModelCore */
            $oConfigurator = ViewEntity\Configurator::factory($oEntityFromModel)
                ->setFields($this->getConfigurator()->getFields())
                ->setMaxLevel($this->getConfigurator()->getMaxLevel())
                ->setCurrentLevel($this->getConfigurator()->getCurrentLevel())
                ->setProfile($this->getConfigurator()->getProfile())
                ->setViewListReference($this);

            $oViewEntity = ViewEntity::factory($oConfigurator);
            $oViewEntity->setViewFieldPath($this->getViewPathField());

            $this->aListPrepared[] = $oViewEntity;
        }
    }

    /**
     * Get configurator of this ViewList.
     *
     * @access     public
     * @return    \Plethora\View\ViewList\Configurator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getConfigurator() {
        return $this->oConfig;
    }

    /**
     * Generate field labels to list table header.
     *
     * @access     protected
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function generateHeaderLabels() {
        if(empty($this->aListPrepared)) {
            return [];
        }

        $aFieldLabels = [];

        // get first element
        $oFirstEntity = $this->getFirstEntity();
        $oModel       = $oFirstEntity->getModel();

        // generate array of field labels
        foreach($this->getConfigurator()->getFields() as $sFieldKey => $mFieldValue) {
            $sField   = is_array($mFieldValue) ? $sFieldKey : $mFieldValue;
            $oLocales = $oModel->hasLocales() ? $oModel->getLocales() : FALSE;
            /* @var $oLocales \Plethora\ModelCore\Locales */

            if($oModel->hasField($sField)) {
                $oField = $oModel->getConfigForField($sField);
                /* @var $oField \Plethora\Form\Field */
                $aFieldLabels[$sField] = ($oField !== FALSE) ? $oField->getLabel() : NULL;
            } elseif($oLocales !== FALSE && $oLocales->hasField($sField)) {
                $oLocaleField = $oLocales->getConfigForField($sField);
                /* @var $oLocaleField \Plethora\Form\Field */
                $aFieldLabels[$sField] = ($oLocaleField !== FALSE) ? $oLocaleField->getLabel() : NULL;
            } else {
                $aFieldLabels[$sField] = '';
            }
        }

        // filling up remaining empty labels
        foreach($aFieldLabels as $sField => &$sLabel) {
            if(empty($sLabel)) {
                $sLabel = __('label.model.'.$this->sHtmlClass.'.'.$sField);
            }
        }

        return $aFieldLabels;
    }

    /**
     * Get model class name.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getModelClassName() {
        return $this->sModelClass;
    }

    /**
     * Set path to single row View of this entity list.
     *
     * @access     public
     * @param    string $sValue
     * @return    $this
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setViewPathSingleRow($sValue) {
        $this->sViewPathSingleRow = $sValue;

        return $this;
    }

    /**
     * Set path to View of this entity list.
     *
     * @access     public
     * @param    string $sValue
     * @return    ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setViewPathList($sValue) {
        $this->sViewPathList = $sValue;

        return $this;
    }

    /**
     * Get default path of View path for list field.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getViewPathField() {
        return $this->sViewPathFieldDefault;
    }

    /**
     * Get default path of View path for list field.
     *
     * @access     public
     * @param $sValue
     * @return ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setViewPathField($sValue) {
        $this->sViewPathFieldDefault = $sValue;

        foreach($this->aListPrepared as $oViewEntity) {
            /* @var $oViewEntity ViewEntity */
            $oViewEntity->setViewFieldPath($this->sViewPathFieldDefault);
        }

        return $this;
    }

    /**
     * Get array of prepared fields list.
     *
     * @access     public
     * @return    array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getPreparedEntities() {
        return $this->aListPrepared;
    }

    /**
     * Get first entity from list.
     *
     * @access     public
     * @return    ViewEntity
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function &getFirstEntity() {
        if(!empty($this->aListPrepared)) {
            return $this->aListPrepared[0];
        } else {
            $bNoEntities = FALSE;

            return $bNoEntities;
        }
    }

    /**
     * Set field container to all entities in the list.
     *
     * @access     public
     * @param    string $sFirstField
     * @param    string $sLastField
     * @return    ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addFieldContainer($sFirstField, $sLastField) {
        foreach($this->getPreparedEntities() as $oEntity) {
            /* @var $oEntity \Plethora\View\ViewEntity */
            $oEntity->addFieldContainer($sFirstField, $sLastField);
        }

        return $this;
    }

    /**
     * Add new field imitation.
     *
     * @access     public
     * @param    string                            $sBeforeField
     * @param    \Plethora\View\ViewFieldImitation $oFieldImitation
     * @return    ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addFieldImitation($sBeforeField, ViewFieldImitation $oFieldImitation) {
        foreach($this->getPreparedEntities() as $oEntity) {
            /* @var $oEntity \Plethora\View\ViewEntity */
            $oEntity->addFieldImitation($sBeforeField, $oFieldImitation);
        }

        return $this;
    }

    /**
     * Set prefix for this list.
     *
     * @access     public
     * @param    string $sValue
     * @return    ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setPrefix($sValue) {
        $this->sPrefix = $sValue;

        return $this;
    }

    /**
     * Add new string to the prefix.
     *
     * @access     public
     * @param    string $sValue
     * @return    ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addToPrefix($sValue) {
        $this->sPrefix .= $sValue;

        return $this;
    }

    /**
     * Set suffix for this list.
     *
     * @access     public
     * @param    string $sValue
     * @return    ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setSuffix($sValue) {
        $this->sSuffix = $sValue;

        return $this;
    }

    /**
     * Add new string to the suffix.
     *
     * @access     public
     * @param    string $sValue
     * @return    ViewList
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addToSuffix($sValue) {
        $this->sSuffix .= $sValue;

        return $this;
    }

    /**
     * Get entities list View object.
     *
     * @access     public
     * @return    View
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getView() {
        // return View
        return View::factory($this->sViewPathList)
            ->set('aList', $this->getPreparedEntities())
            ->set('sHtmlClass', $this->sHtmlClass)
            ->set('oFirstEntity', $this->getFirstEntity())
            ->set('aFieldLabels', $this->generateHeaderLabels())
            ->set('sViewPathSingleRow', $this->sViewPathSingleRow)
            ->bind('sPrefix', $this->sPrefix)
            ->bind('sSuffix', $this->sSuffix);
    }

}
