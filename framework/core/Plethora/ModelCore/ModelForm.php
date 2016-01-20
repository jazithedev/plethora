<?php

namespace Plethora\ModelCore;

use Doctrine;
use Doctrine\ORM;
use Plethora\Config;
use Plethora\Core;
use Plethora\DB;
use Plethora\Form;
use Plethora\Helper\Arrays;
use Plethora\ModelCore;
use Plethora\Router;
use Plethora\Session;
use Plethora\Validator\RulesSetBuilder;
use Model\User;
use Plethora\Exception;

/**
 * Class which generates \Plethora\Form basing on \Plethora\Model.
 *
 * @package        Plethora
 * @subpackage     Model
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class ModelForm
{

    /**
     * @access    private
     * @var        string
     * @since     1.0.0-alpha
     */
    private $sName = NULL;

    /**
     * @access    private
     * @var        ModelCore
     * @since     1.0.0-alpha
     */
    private $oModel = NULL;

    /**
     * @access    private
     * @var        ModelFormConfig
     * @since     1.0.0-alpha
     */
    private $oConfig = NULL;

    /**
     * Factory method.
     *
     * @access   public
     * @param    ModelCore       $model
     * @param    string          $formName
     * @param    ModelFormConfig $config
     * @return   ModelForm
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory(ModelCore $model, $formName, ModelFormConfig $config = NULL)
    {
        return new ModelForm($model, $formName, $config);
    }

    /**
     * Constructor.
     *
     * @access   public
     * @param    ModelCore       $oModel
     * @param    string          $sFormName
     * @param    ModelFormConfig $oConfig
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct(ModelCore &$oModel, $sFormName, ModelFormConfig $oConfig = NULL)
    {
        $this->oModel  = $oModel;
        $this->sName   = $sFormName;
        $this->oConfig = $oConfig;
    }

    /**
     * Generate \Plethora\Form instance which is based on Model.
     *
     * @access   public
     * @return   Form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function generate()
    {
        $oConfigFromLocales       = NULL;
        $aDefaultValues           = [];
        $aFieldsFromConfigLocales = [];
        $oModel                   = $this->getModel();
        $oMetadata                = $oModel->getMetadata();
        $oMetadataLocales         = $oModel->getLocalesMetadata();
        $bHasLocales              = $oModel->hasLocales();

        if($this->getModel()->hasLocales()) {
            /* @var $oConfigFromLocales MConfig */
            $oConfigFromLocales       = call_user_func($this->getModel()->getLocalesClass().'::getConfig');
            $aFieldsFromConfigLocales = $oConfigFromLocales->getFieldsNames();
        }

        $aFieldsFromConfig = array_unique(
            array_merge(
                $this->getModel()->getConfig()->getFieldsNames(),
                $aFieldsFromConfigLocales
            )
        );

        // get all default values from model
        foreach($aFieldsFromConfig as $sField) {
            $mValueFromForm = NULL;
            $sFieldModel    = $oMetadata->getFieldName($sField);
            $sFieldLocales  = $oMetadataLocales !== FALSE ? $oMetadataLocales->getFieldName($sField) : NULL;

            // associations fields from model
            if($oMetadata->hasAssociation($sFieldModel)/*in_array($sField, $aAssociationsFromModel)*/) {
                $mValuesFromModel = $this->getModel()->$sFieldModel;

                if($mValuesFromModel !== NULL) {
                    // persistend collection
                    if(
                        $mValuesFromModel instanceof ORM\PersistentCollection ||
                        $mValuesFromModel instanceof Doctrine\Common\Collections\ArrayCollection
                    ) {
                        $aValuesToOverwrite = [];

                        foreach($mValuesFromModel as $oModelRow) {
                            /* @var $oModelRow ModelCore */
                            $aValuesToOverwrite[] = $oModelRow;
                        }

                        $mValueFromForm = [
                            'und' => $aValuesToOverwrite,
                        ];

                        unset($aValuesToOverwrite);
                    } // single associacion
                    else {
                        $mValueFromForm = [
                            'und' => [$mValuesFromModel],
                        ];
                    }
                }
            } // associations fields from locales
            elseif($bHasLocales && $oMetadataLocales->hasAssociation($sFieldLocales)) {
                $aValuesInAllLangs = $this->getModel()->getLocales('all');

                foreach($aValuesInAllLangs as $oLocaleModel) {
                    /* @var $oLocaleModel \Plethora\ModelCore\Locales */
                    $sLang            = $oLocaleModel->getLanguage();
                    $mValuesFromModel = $oLocaleModel->$sFieldLocales;

                    if($mValuesFromModel !== NULL) {
                        if(
                            $mValuesFromModel instanceof ORM\PersistentCollection ||
                            $mValuesFromModel instanceof Doctrine\Common\Collections\ArrayCollection
                        ) {
                            // persistend collection
                            $aValuesToOverwrite = [];

                            foreach($mValuesFromModel as $oModelRow) {
                                /* @var $oModelRow ModelCore */
                                $aValuesToOverwrite[] = $oModelRow;
                            }

                            $mValueFromForm[$sLang] = $aValuesToOverwrite;
                            unset($aValuesToOverwrite);
                        } else {
                            // single associacion
                            $mValueFromForm[$sLang] = [$mValuesFromModel];
                        }
                    }
                }
            } // if particular field is base module property
            elseif($oMetadata->hasField($sFieldModel)/*in_array($sField, $aFieldsFromModel)*/) {
                $mValueFromModel = $this->getModel()->$sFieldModel;

                if($mValueFromModel !== NULL) {
                    $mValueFromForm = [
                        'und' => is_array($mValueFromModel) ? $mValueFromModel : [$mValueFromModel],
                    ];
                }

                unset($mValueFromModel);
            } // if field is owned by locales
            elseif($bHasLocales && $oMetadataLocales->hasField($sFieldLocales)/*in_array($sField, $aFieldsFromLocales)*/) {
                $mValueFromForm = [];

                foreach(Router::getLangs() as $sLang) {
                    $mValueFromForm[$sLang] = [];
                }

                foreach($this->getModel()->getLocales('all') as $oLocales) {
                    /* @var $oLocales \Plethora\ModelCore\Locales */
                    if($oLocales->$sField !== NULL) {
                        $mValueFromLocale = $oLocales->$sFieldLocales;

                        $mValueFromForm[$oLocales->getLanguage()] = is_array($mValueFromLocale) ? $mValueFromLocale : [$oLocales->$sField];

                        unset($mValueFromLocale);
                    }
                }
            }

            // set default values
            $aDefaultValues[$sField] = $mValueFromForm;

            // unset temporary variable
            unset($mValueFromForm);
        }

        // creating form object
        $oForm = new Form($this->getName().'_form', $aDefaultValues);
        $oForm->setRelationWithModel($this->getModel());

        // create list of fields
        if($this->getConfig() !== NULL) {
            // limit fields to particular list
            if($this->getConfig()->getFieldsRestriction() !== []) {
                $aFieldsFromConfig = array_intersect($aFieldsFromConfig, $this->getConfig()->getFieldsRestriction());
            }

            // remove particular fields from fields list
            if($this->getConfig()->getFieldsToRemove() !== []) {
                foreach($this->getConfig()->getFieldsToRemove() as $sFieldToRemove) {
                    $mSearchedKey = array_search($sFieldToRemove, $aFieldsFromConfig);

                    if($mSearchedKey !== FALSE) {
                        unset($aFieldsFromConfig[$mSearchedKey]);
                    }
                }
            }
        }

        // create form fields and assign them to the form
        foreach($aFieldsFromConfig as $sField) {
            $aMappings     = [];
            $sFieldModel   = $oMetadata->getFieldName($sField);
            $sFieldLocales = $oMetadataLocales !== FALSE ? $oMetadataLocales->getFieldName($sField) : NULL;

            // get form field
            if($bHasLocales && $oConfigFromLocales->hasField($sField)) {
                $oField = $oConfigFromLocales->getField($sField);
            } else {
                $oField = $this->getModel()->getConfig()->getField($sField);
            }

            // if it's "add" form, remove "disabled" attribute
            if($this->getName() === 'add' && $oField->isDisabled()) {
                $oField->getAttributes()
                    ->removeAttribute('disabled');
            }

            // setting flag if particular field is multilanguaged
            if($bHasLocales && !$oMetadata->hasField($sFieldModel) && ($oMetadataLocales->hasField($sFieldLocales) || $oMetadataLocales->hasAssociation($sFieldLocales))) {
                $oField->setMultilanguage();
            }

            // add field to singleton form
            $oForm->addSingleton($oField);

            // adding new additional rules based on annotations from Doctrine ORM
            if($oMetadata->hasField($sFieldModel)) {
                $aMappings = $oMetadata->getFieldMapping($sFieldModel);
            } elseif($bHasLocales && $oMetadataLocales->hasField($sFieldLocales)) {
                $aMappings = $oMetadataLocales->getFieldMapping($sFieldLocales);
            }

            // add validation rules by mapping
            $this->addValidationByMapping($oField, $aMappings);
        }

        // alter form
        $this->alterForm($oForm);

        // make other, custom operations on Form object
        if($oForm->isSubmitted()) {
            $this->beforeFormValidation($oForm);
        }

        // if form is submitted and valid
        if(!$this->getConfig()->isManualValidation() && $oForm->isSubmittedAndValid()) {
            $this->sendDataToModel($oForm);
        }

        return $oForm;
    }

    /**
     * Add validation rules on the basis of ORM annotation mappings.
     *
     * @access  private
     * @param   Form\Field $oField
     * @param   array      $aMappings
     * @return  bool
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    private function addValidationByMapping(Form\Field &$oField, array $aMappings)
    {
        $iLength = Arrays::get($aMappings, 'length');
        $sType   = Arrays::get($aMappings, 'type');

        // if there are no field mappings
        if(empty($aMappings) || $oField instanceof Form\Field\Hidden) {
            return FALSE;
        }

        // 'required' validation rule
        if(Arrays::get($aMappings, 'nullable', FALSE) === FALSE) {
            $oField->setRequired();
        }

        // validation rules by type
        switch($sType) {
            case 'string':
                if($iLength !== NULL) {
                    $oField->addRulesSet(
                        RulesSetBuilder\String::factory()
                            ->max(':value', $iLength)
                    );
                }
                break;
            case 'integer':
                if($iLength !== NULL) {
                    $oField->addRulesSet(
                        RulesSetBuilder\Number::factory()
                            ->max(':value', $iLength)
                    );
                }
                break;
        }

        return TRUE;
    }

    /**
     * Get Model instance.
     *
     * @access     public
     * @return    ModelCore
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function &getModel()
    {
        return $this->oModel;
    }

    /**
     * Get config instance.
     *
     * @access     public
     * @return    ModelFormConfig
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function &getConfig()
    {
        return $this->oConfig;
    }

    /**
     * Get form name.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getName()
    {
        return $this->sName;
    }

    /**
     * Alter Form fields. Remove those fields which shouldn't be in particular Form.
     *
     * Method to overwrite.
     *
     * @access     protected
     * @param      Form $form
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function alterForm(Form &$form)
    {

    }

    /**
     * Do some actions before Form validation amd when Form is submitted.
     *
     * Method to overwrite.
     *
     * @access     protected
     * @param    Form $oForm
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function beforeFormValidation(Form &$oForm)
    {

    }

    /**
     * If form is submitted and valid, send new data (from Form) to database.
     *
     * @access   public
     * @param    Form $oForm
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function sendDataToModel(Form &$oForm)
    {
        if(!$oForm->isSubmittedAndValid()) {
            return FALSE;
        }

        // transfer data from Form to Model
        foreach($oForm->getFields() as $oFormField) {
            /* @var $oFormField Form\Field */
            if($oFormField->isDisabled()) {
                continue;
            }

            $oModel           = $this->getModel();
            $oMetadata        = $oModel->getMetadata();
            $oMetadataLocales = $oModel->getLocalesMetadata();
            $sName            = $oFormField->getName();
            $sFieldModel      = $oMetadata->getFieldName($sName);
            $sFieldLocales    = $oMetadataLocales !== FALSE ? $oMetadataLocales->getFieldName($sName) : NULL;
            $sField           = NULL;

            if($oMetadata->hasField($sFieldModel) || $oMetadata->hasAssociation($sFieldModel)) {
                $sField = $sFieldModel;
            } elseif($oModel->hasLocales() && ($oMetadataLocales->hasField($sFieldLocales) || $oMetadataLocales->hasAssociation($sFieldLocales))) {
                $sField = $sFieldLocales;
            }

            if($sField !== NULL) {
                $aValue = $this->getValueAfterValidation($oFormField);

                $this->makeDataTransfer($sField, $aValue, $oFormField);
//				$oFormField->afterValidationModelOperations($this->getModel());
            }
        }

        // make data save
        if(!$this->getConfig()->isManualSave()) {
            $this->makeSave($oForm);
        }

        return TRUE;
    }

    /**
     * Return value for database field from form.
     *
     * @access     protected
     * @param    Form\Field $oFormField
     * @return    mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function getValueAfterValidation(Form\Field &$oFormField)
    {
//		$aAssociationsFromModel = $this->getModel()->getMetadata()->getAssociationNames();
//
//		if($this->getModel()->hasLocales()) {
//			$aAssociationsFromLocales = $this->getModel()->getLocalesMetadata()->getAssociationNames();
//		} else {
//			$aAssociationsFromLocales = [];
//		}
//
//		$sField			 = $oFormField->getName();
//		$aValueForModel	 = $oFormField->getValue();
//
//		foreach($aValueForModel as &$aAllValues) {
//			foreach($aAllValues as &$mValueForModel) {
//				if($sField !== 'locales' && (in_array($sField, $aAssociationsFromModel) || in_array($sField, $aAssociationsFromLocales))) {
//					if(in_array($sField, $aAssociationsFromModel)) {
//						$aAnnotations = $this->getModel()->getMetadata()->getAssociationMapping($sField);
//					} else {
//						$aAnnotations = $this->getModel()->getLocalesMetadata()->getAssociationMapping($sField);
//					}
//
//					$sModel = \Plethora\Helper\Arrays::get($aAnnotations, 'targetEntity');
//
//					d($mValueForModel);
//
//					if(is_array($mValueForModel)) {
//						if(count($mValueForModel) > 0) {
//							$mValueForModel = \Plethora\DB::query('SELECT t FROM '.$sModel.' t WHERE t.id IN (:id)')
//								->param('id', $mValueForModel)
//								->execute();
//						}
//					} else {
//						$mValueForModel = \Plethora\DB::find($sModel, $mValueForModel);
//					}
//				}
//			}
//		}

        return $oFormField->getValue();
    }

    /**
     * Make data transfer (set values) from Form to Model.
     *
     * @access   protected
     * @param    string     $sName
     * @param    array      $aValue
     * @param    Form\Field $oFormField
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function makeDataTransfer($sName, $aValue, Form\Field &$oFormField)
    {
        $aCheckedLanguages = $oFormField->getFormObject()->getCheckedLanguages();

        if($oFormField->isMultilanguage()) {
            foreach(Core::getLanguages() as $sLang) {
                if(!isset($aValue[$sLang]) && in_array($sLang, $aCheckedLanguages)) {
                    $aValue[$sLang] = NULL;
                }
            }
        }

        foreach($aValue as $sLang => $aAllValues) {
            if(!in_array($sLang, $aCheckedLanguages)) {
                continue;
            }

            if(!empty($aAllValues)) {
                $mValue = ($oFormField->getQuantity() === 1) ? array_shift($aAllValues) : array_values($aAllValues);
            } else {
                $mValue = NULL;
            }

            if($sLang === 'und') {
                if(is_array($mValue)) {
                    $mValue = array_values($mValue);
                }

                $this->getModel()->$sName = $mValue;
            } else {
                $this->getModel()->getLocales($sLang)->$sName = $mValue;
            }
        }

        return TRUE;
    }

    /**
     * Save new Model data. Method created for "public" uses, when needed to
     * make a save in, for example, controller (do not have access no the part
     * of saving functionality).
     *
     * @access   public
     * @param    Form $oForm
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function makeSave(Form &$oForm)
    {
        $this->makeSaveProtected($oForm);
    }

    /**
     * Save new Model data. Method created for "public" uses, when needed to
     * make a save in, for example, controller.
     *
     * @access   protected
     * @param    Form $oForm
     * @throws   Exception
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function makeSaveProtected(Form &$oForm)
    {
        $oConfig = $this->getConfig();

        try {
            $this->beforeSave($oForm);
            $this->getModel()->save();

            DB::flush();

            if($oConfig == NULL || $oConfig->isReloading() === TRUE) {
                $sUrl  = ($oConfig->getAction() === NULL) ? $oForm->getAttribute('action') : $oConfig->getAction();
                $sComm = ($oConfig->getMessage() === NULL) ? __('Form data submitted.') : $oConfig->getMessage();

                Session::flash($sUrl, $sComm);
            }
        } catch(Exception $e) {
            if(Config::get('base.mode') == 'development') {
                throw $e;
            } else {
                throw new Exception\Fatal(__('Error occured while saving data in database.'));
            }
        }
    }

    /**
     * Method in which can do some operations before saving to database.
     *
     * @access   protected
     * @param    Form $form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function beforeSave(Form &$form)
    {
        if($this->getModel()->hasLocales()) {
            $aDoNotSaveFor = array_diff(Core::getLanguages(), $form->getCheckedLanguages());

            foreach($aDoNotSaveFor as $sLang) {
                $this->getModel()->removeLocales($sLang);
            }
        }

        if(property_exists($this->getModel(), 'author') && !$this->getModel()->getAuthor() instanceof User) {
            $this->getModel()->setAuthor(User::getLoggedUser());
        }

        if(property_exists($this->getModel(), 'modification_date')) {
            $this->getModel()->updateModificationDate();
        }
    }

}
