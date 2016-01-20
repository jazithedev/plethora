<?php

namespace Plethora;

use Plethora\Exception;
use Plethora\Form\Field;
use Plethora\ModelCore;
use Plethora\View;

/**
 * Forms generator
 *
 * @package    Form
 * @author     Krzysztof Trzos
 * @since      1.0.0-alpha
 * @version    1.0.0-alpha
 */
class Form
{
    const METHOD_POST = 'post';
    const METHOD_GET  = 'get';

    /**
     * Form name
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $formName;

    /**
     * Array of errors related to form (not with particular fields).
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $formErrors = [];

    /**
     * Action (in html)
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $action;

    /**
     * Array of all form fields
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $fields = [];

    /**
     * Is form has been submitted
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $isFormSubmitted = FALSE;

    /**
     * Default values for form fields [array('field_name' => 'value')]
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $defaultValues = [];

    /**
     * Counts separators
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $separatorCounter = 0;

    /**
     * @access  private
     * @var     Validator
     * @since   1.0.0-alpha
     */
    private $validator;

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $formViewPath = 'base/form';

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $attributes = [];

    /**
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $fieldNamesPrefixes = TRUE;

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $submitName;

    /**
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $submitValue;

    /**
     * Last used weight for form fields.
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $lastUsedWeightForFields = 0;

    /**
     * Saves form validation result.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $validationResult = NULL;

    /**
     * Stores errors array refactored from Validator.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $fieldsErrors = [];

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $fieldPatterns = [];

    /**
     * Model that is related with particular form. This variable can be used by
     * form fields for additional operations or data modifying.
     *
     * @access  private
     * @var     ModelCore
     * @since   1.0.0-alpha
     */
    private $relatedModel;

    /**
     * This variable tells if to use CSRF token in this form.
     *
     * @access  private
     * @var     bool
     * @since   1.0.0-alpha
     */
    private $csrfToken = TRUE;

    /**
     * Prefix of the form.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $formPrefix = '';

    /**
     * Suffix of the form.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $formSuffix = '';

    /**
     * Constructor.
     *
     * @access   public
     * @param    string $name Form name
     * @param    array  $defaultValues
     * @param    string $method
     * @throws   Exception\Fatal
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, array $defaultValues = [], $method = Form::METHOD_POST)
    {
        if(preg_match('/[^a-z0-9_]/', $name) !== 0) {
            throw new Exception\Fatal('Name of the form can contain only small letters, numbers and an underscore characters.');
        }

        $this->formName      = $name;
        $this->defaultValues = $defaultValues;
        $this->validator     = new Validator;

        $this->setMethod($method);
        $this->setSubmitValue(__('send'));
        $this->setSubmitName($this->getName().'_submit');

        if(Helper\Arrays::get($this->getMethodValue(), $this->getSubmitName(), FALSE) || isset($_FILES[$this->getName()])) {
            $this->isFormSubmitted = TRUE;
        }

        // add javascripts
        $controller = Router::getInstance()->getController();
        $controller->addJs('/themes/_common/js/form/form.js');

        // loggin form creation
        Log::insert('Form "'.$name.'" has been created.');
    }

    /**
     * @static
     * @access   public
     * @param    string      $sName
     * @param    array       $aDefaultValues
     * @param    bool|string $sMethod
     * @return   Form New Form instance
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory($sName, array $aDefaultValues = [], $sMethod = Form::METHOD_POST)
    {
        return new Form($sName, $aDefaultValues, $sMethod);
    }

    /**
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setSubmitName($sValue)
    {
        $this->submitName = $sValue;

        return $this;
    }

    /**
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getSubmitName()
    {
        return $this->submitName;
    }

    /**
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setSubmitValue($sValue)
    {
        $this->submitValue = $sValue;

        return $this;
    }

    /**
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getSubmitValue()
    {
        return $this->submitValue;
    }

    /**
     * Method used by Field object to determine, if system must add prefixes with Form name to Field name.
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isFieldsNameWithPrefix()
    {
        return $this->fieldNamesPrefixes;
    }

    /**
     * Adds form name as prefix to all form fields.
     *
     * @access   public
     * @param    boolean $bValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFieldsNameWithPrefix($bValue = TRUE)
    {
        $this->fieldNamesPrefixes = (bool)$bValue;

        return $this;
    }

    /**
     * @access   public
     * @return   Validator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Get default values.
     *
     * @access   public
     * @param    string $sKey
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getDefaultVal($sKey)
    {
        return isset($this->defaultValues[$sKey]) ? static::changeBoolOnInt($this->defaultValues[$sKey]) : [];
    }

    /**
     * Get all default values.
     *
     * @access   public
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getDefaultVals()
    {
        return $this->defaultValues;
    }

    /**
     * Changes all default values of type BOOL for type INT.
     *
     * @static
     * @access   private
     * @param    mixed $mValues
     * @return   mixed
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private static function changeBoolOnInt($mValues)
    {
        if(!is_array($mValues)) {
            return (gettype($mValues) === 'boolean') ? (int)$mValues : $mValues;
        } else {
            foreach($mValues as $i => $v) {
                $mValues[$i] = (gettype($v) === 'boolean') ? (int)$v : $v;
            }

            return $mValues;
        }
    }

    /**
     * Get form name.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getName()
    {
        return $this->formName;
    }

    /**
     * Is form submitted?
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isSubmitted()
    {
        return $this->isFormSubmitted;
    }

    /**
     * Check whether form has multilanguaged fields.
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function hasMultilangField()
    {
        foreach($this->getFields() as $oField) {
            /* @var $oField Form\Field */
            if($oField->isMultilanguage()) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Add new field to this form.
     *
     * @access   public
     * @param    string $sName field name
     * @param    string $sType field type
     * @return   Form\Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function add($sName, $sType)
    {
        $sClassName = '\\Plethora\\Form\\Field\\'.ucfirst($sType);

        $oNewField = new $sClassName($sName, $this);
        /* @var $oNewField Field */
        $oNewField->setWeight($this->getLastWeight());

        $this->fields[$sName] = $oNewField;

        return $oNewField;
    }

    /**
     * Add new field to the form
     *
     * @access  public
     * @param   Form\Field $oField
     * @return  $this
     * @throws  Exception
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function addField(Field $oField)
    {
        if($oField->getFormObject()->getName() === 'singletons') {
            throw new Exception('Cannot add fields which are singletons.');
        }

        $this->fields[$oField->getName()] = $oField;

        return $this;
    }

    /**
     * Remove particular field from current form instance.
     *
     * @access   public
     * @param    string $name
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function removeField($name)
    {
        if(isset($this->fields[$name])) {
            unset($this->fields[$name]);
        }

        return $this;
    }

    /**
     * Add singleton field to this form.
     *
     * @access   public
     * @param    Form\Field $oField
     * @return   Form
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addSingleton(Field $oField)
    {
        if($oField->getFormObject()->getName() !== 'singletons') {
            throw new Exception('This field is not a singleton.');
        }

        $oField->setFormIfSingleton($this);

        $this->fields[$oField->getName()] = $oField;

        return $this;
    }

    /**
     * Adding separator between form fields.
     *
     * @access   public
     * @param    string $sType  separator type
     * @param    mixed  $mValue separator value
     * @param    string $sNameParam
     * @return   Form\Separator    FormSeparator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addSeparator($sType, $mValue, $sNameParam = NULL)
    {
        $sClassName = '\\Plethora\\Form\\Separator\\'.ucfirst($sType);

        $this->separatorCounter++;
        $sName = is_null($sNameParam) ? 'separator_'.$this->separatorCounter : $sNameParam;

        if(empty($mValue)) {
            $mValue = $this->getDefaultVal($sName);
        }

        $oSeparator           = new $sClassName($sName, $mValue, $this);
        $this->fields[$sName] = $oSeparator;

        return $oSeparator;
    }

    /**
     * Get all form fields.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFields()
    {
        $aWeightFields = [];
        $aOutput       = [];

        foreach($this->fields as $field) {
            /* @var $field Form\Field */
            $key = $field->getWeight();

            while(isset($aWeightFields[$key])) {
                $key                     = $key.'_';
                $aWeightFields[$key.'_'] = $field;
            }

            $aWeightFields[$key] = $field;
        }

        // @TODO: Implementacja by nie sortowało pól przy każdym ich pobieraniu.
        ksort($aWeightFields, SORT_STRING);

        foreach($aWeightFields as $field) {
            $aOutput[$field->getName()] = $field;
        }

        return $aOutput;
    }

    /**
     * Get particular field.
     *
     * @access   public
     * @param    string    field name
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getField($sKey)
    {
        return $this->fields[$sKey];
    }

    /**
     * Check if form has a particular field.
     *
     * @access   public
     * @param    string $sKey
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function hasField($sKey)
    {
        return isset($this->fields[$sKey]);
    }

    /**
     * Getting value from particular field
     *
     * @access   public
     * @param    string $sFieldKey field name
     * @return   mixed
     * @throws   \Plethora\Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function get($sFieldKey)
    {
        if(!isset($this->fields[$sFieldKey])) {
            throw new Exception('Field with name "'.$sFieldKey.'" does not exist!');
        }

        $mValue = $this->fields[$sFieldKey]->getValue();

        if(isset($mValue['und']) && count($mValue['und']) === 1) {
            $mValue = array_shift($mValue['und']);
        }

        return $mValue;
    }

    /**
     * Set html action (<form action="[...]">)
     *
     * CHANGELOG:
     * 2.0.9, 2013-12-07: Medyfikacja metody setAction(), aby zwracała obiekt klasy \Plethora\Form.
     *
     * @access   public
     * @param    string $sAction action
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setAction($sAction)
    {
        $this->action = $sAction;

        return $this;
    }

    /**
     * Get form action URL.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAction()
    {
        if($this->action === NULL) {
            $this->action = Router::getCurrentUrl();
        }

        return $this->action;
    }

    /**
     * Set content of attribute (overwrites if exists).
     *
     * @access   public
     * @param    string $sName
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setAttribute($sName, $sValue)
    {
        $this->attributes[$sName] = $sValue;

        return $this;
    }

    /**
     * Get particular form attribute.
     *
     * @access   public
     * @param    string $sName
     * @return   string
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAttribute($sName)
    {
        switch($sName) {
            case 'action':
                return $this->getAction();
        }

        if(isset($this->attributes[$sName])) {
            return $this->attributes[$sName];
        } else {
            throw new Exception\Fatal('Attribute "'.$sName.'" in form "'.$this->getName().'" does not exists.');
        }
    }

    /**
     * Add new content to one of the attributes.
     *
     * @access   public
     * @param    string $sName
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addToAttribute($sName, $sValue)
    {
        if(!isset($this->attributes[$sName])) {
            $this->attributes[$sName] = '';
        }

        $this->attributes[$sName] .= $sValue;

        return $this;
    }

    /**
     * Render all form attributes.
     *
     * @access   private
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function renderAttributes()
    {
        $sReturn = '';

        $this->setAttribute('action', $this->getAction());

        foreach($this->attributes as $sName => $sValue) {
            $sReturn .= $sName.'="'.$sValue.'" ';
        }

        return rtrim($sReturn, ' ');
    }

    /**
     * Get form method
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMethod()
    {
        return $this->getAttribute('method');
    }

    /**
     * @access   public
     * @param    string $sValue
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    private function setMethod($sValue)
    {
        if(!in_array($sValue, [Form::METHOD_GET, Form::METHOD_POST], TRUE)) {
            throw new Exception('Wrong value.');
        }

        $this->setAttribute('method', $sValue);
    }

    /**
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMethodValue()
    {
        $mValues = ($this->getMethod() === Form::METHOD_POST) ? filter_input_array(INPUT_POST) : filter_input_array(INPUT_GET);

        return is_null($mValues) ? [] : $mValues;
    }

    /**
     * Add the enctype to form tag.
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addEnctype()
    {
        $this->setAttribute('enctype', 'multipart/form-data');

        return $this;
    }

    /**
     * Check whether the form is submitted and valid.
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isSubmittedAndValid()
    {
        return ($this->isSubmitted() && $this->isValid()) ? TRUE : FALSE;
    }

    /**
     * Check whether the form is submitted and NOT valid.
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isSubmittedAndNotValid()
    {
        return ($this->isSubmitted() && !$this->isValid()) ? TRUE : FALSE;
    }

    /**
     * Get array of languages for which the form will load and save values for
     * it's fields.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getCheckedLanguages()
    {
        $aCheckedLanguages = [];

        if($this->isSubmitted()) {
            $aCheckedLanguages = Helper\Arrays::path($this->getMethodValue(), $this->getName().'.form_language', []);
        } else {
            foreach($this->getFields() as $oField) {
                /* @var $oField Form\Field */


                if($oField->isMultilanguage()) {
                    foreach(Core::getLanguages() as $sLang) {
                        $mValue = $oField->getValue($sLang, 0);

                        if(!empty($mValue)) {
                            $aCheckedLanguages[$sLang] = $sLang;
                        }
                    }
                }
            }
        }

        $sMainLanguage                     = Core::getMainLanguage();
        $aCheckedLanguages[$sMainLanguage] = $sMainLanguage;
        $aCheckedLanguages['und']          = 'und';

        return $aCheckedLanguages;
    }

    /**
     * Checks if the form contains any errors.
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isValid()
    {
        if($this->validationResult !== NULL) {
            return $this->validationResult;
        }

        # if form is submitted
        if($this->isSubmitted()) {
            # make some pre-validation operations
            foreach($this->getFields() as $field) {
                /* @var $field Field */
                $whenSubmittedMethod = new \ReflectionMethod(get_class($field), 'whenFormSubmitted');
                $whenSubmittedMethod->setAccessible(TRUE);
                $whenSubmittedMethod->invoke($field);
                $whenSubmittedMethod->setAccessible(FALSE);

                # if particular field isn't required and it's value is empty, remove all rules
                if($field->isRequired() === FALSE) {
                    foreach($field->getLangs() as $sLang) {
                        foreach($field->getValue($sLang) as $i => $value) {
                            if($value == '' || $value === []) {
                                $this->getValidator()->blockRulesFor($field->getName(), $sLang, $i);
                            }
                        }
                    }
                }

                # if particular field is disabled, remove all rules
                if($field->isDisabled()) {
                    $this->getValidator()->blockRulesFor($field->getName());
                }

                # remove all rules for multilang field and it's unchecked language
                if($field->isMultilanguage()) {
                    foreach(Core::getLanguages() as $sLang) {
                        if(!in_array($sLang, $this->getCheckedLanguages())) {
                            $this->getValidator()->blockRulesFor($field->getName(), $sLang);
                        }
                    }
                }
            }

            # CSRF token validation
            if($this->csrfToken) {
                $aMethodValues     = $this->getMethodValue();
                $sTokenFromForm    = Helper\Arrays::path($aMethodValues, $this->getName().'.csrf_token');
                $sTokenFromSession = $this->getFormToken();

                if($sTokenFromForm !== $sTokenFromSession) {
                    $this->addFormError(__('Bad request token. Please, send the form once again.'));
                }
            }

            # make some operations for all fields before validation
            foreach($this->getFields() as $field) {
                /* @var $field Field */
                $field->beforeValidation();
            }

            # check amount of field values
            foreach($this->getFields() as $field) {
                /* @var $field Field */
                foreach($field->getLangs() as $sLang) {
                    if(in_array($sLang, $this->getCheckedLanguages())) {
                        $iValuesAmount = count($field->getValue($sLang));

                        if($field->getQuantity() === 0) {
                            if($field->getQuantityMin() > $iValuesAmount) {
                                $this->getValidator()->addError($field->getName().'_'.$sLang, __('Insufficient amount of values given for this field (should be :number).', ['number' => $field->getQuantityMin()]));
                            }
                            if($field->getQuantityMax() != 0 && $field->getQuantityMax() < $iValuesAmount) {
                                $this->getValidator()->addError($field->getName().'_'.$sLang, __('Too hight amount of values given for this field (should be :number).', ['number' => $field->getQuantityMin()]));
                            }
                        } elseif($field->getQuantity() != $iValuesAmount) {
                            $this->getValidator()->addError($field->getName().'_'.$sLang, __('The amount of values for this field should be :number (:amount given).', ['number' => $field->getQuantity(), 'amount' => $iValuesAmount]));
                        }
                    }
                }
            }

            # if form has not been checked earler, do it now
            if(!$this->getValidator()->isChecked()) {
                $this->getValidator()->check();
                $this->refactorErrors();
            }

            # make some operations for all fields which are valid
            foreach($this->getFields() as $field) {
                /* @var $field Field */
                foreach($field->getLangs() as $sLang) {
                    if(!$field->hasErrors($sLang)) {
                        $field->afterValidationWhenValid($sLang);
                    }
                }
            }
        }

        # check if form has any errors
        $hasErrors = $this->getValidator()->hasErrors();

        # if form is submitted and doesn't have errors
        if($this->isSubmitted() && !$hasErrors) {
            foreach($this->getFields() as $field) {
                /* @var $field Field */
                $field->afterValidation();
            }
        }

        # return value whether the form is valid
        return $this->validationResult = !$hasErrors;
    }

    /**
     * Set Form View path.
     *
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setView($sValue)
    {
        $this->formViewPath = $sValue;

        return $this;
    }

    /**
     * Generate form ID.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFormID()
    {
        $sFormID = $this->getName();

        foreach($this->getFields() as $oField) {
            /* @var $oField Form\Field */
            $sFormID .= $oField->getName();
        }

        return base64_encode($sFormID);
    }

    /**
     * Render form as a string.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function render()
    {
        // render form
        return View::factory($this->formViewPath)
            ->bind('oForm', $this)
            ->render();
    }

    /**
     * Get (generate) token for this form.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFormToken()
    {
        $sFormID     = $this->getFormID();
        $aFormTokens = Session::get('form_tokens');

        if(!isset($aFormTokens[$sFormID]) || !is_array($aFormTokens[$sFormID]) || $aFormTokens[$sFormID][1] < time()) {
            $sToken                = base64_encode(openssl_random_pseudo_bytes(16));
            $aFormTokens[$sFormID] = [$sToken, time() + 3600];

            Session::set('form_tokens', $aFormTokens);
        } else {
            $sToken = $aFormTokens[$sFormID][0];
        }

        return $sToken;
    }

    /**
     * Generate CSRF code for this form.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function generateCsrf()
    {
        if($this->csrfToken) {
            return '<input type="hidden" name="'.$this->getName().'[csrf_token]" value="'.$this->getFormToken().'" />';
        } else {
            return '';
        }
    }

    /**
     * This function tells to not to add CSRF token to this form.
     *
     * @access  public
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function removeCsrfToken()
    {
        $this->csrfToken = FALSE;

        return $this;
    }

    /**
     * Get last field weight.
     *
     * @access   public
     * @param    boolean $bIncrease
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getLastWeight($bIncrease = TRUE)
    {
        if($bIncrease === TRUE) {
            $this->lastUsedWeightForFields++;
        }

        return $this->lastUsedWeightForFields;
    }

    /**
     * Set last used form field weight.
     *
     * @access   public
     * @param    integer $iWeight
     * @return   Form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setLastWeight($iWeight)
    {
        $this->lastUsedWeightForFields = $iWeight;

        return $this;
    }

    /**
     * Refactor errors from Validator.
     *
     * @access   protected
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function refactorErrors()
    {
        if($this->getValidator()->hasErrors()) {
            $aErrors = $this->getValidator()->getErrors();

            foreach($aErrors as $sField => $aErrorsForSingleValue) {
                $aExpl = explode(Validator::FIELD_NAME_SEPARATOR, $sField);

                if(count($aExpl) === 3) {
                    Helper\Arrays::createMultiKeys($this->fieldsErrors, implode('.', $aExpl), $aErrorsForSingleValue);
                }
            }

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Get errors for particular field.
     *
     * @access   public
     * @param    string $sPath
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getErrorsForField($sPath)
    {
        return Helper\Arrays::path($this->fieldsErrors, $sPath, []);
    }

    /**
     * Add new field pattern.
     *
     * @access   public
     * @param    string $sFieldName
     * @param    string $sValue
     * @return   Form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addFieldPattern($sFieldName, $sValue)
    {
        $this->fieldPatterns[$sFieldName] = $sValue;

        return $this;
    }

    /**
     * Get all fields patterns.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFieldPatterns()
    {
        return $this->fieldPatterns;
    }

    /**
     * Add new error of this form.
     *
     * @access   public
     * @param    string $sString
     * @return   Form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function addFormError($sString)
    {
        $this->formErrors[] = $sString;

        return $this;
    }

    /**
     * Get form main errors.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFormErrors()
    {
        return $this->formErrors;
    }

    /**
     * Set a model that particular form will be related with.
     *
     * @access   public
     * @param    ModelCore $oModel
     * @return   Form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRelationWithModel(ModelCore $oModel)
    {
        $this->relatedModel = $oModel;

        return $this;
    }

    /**
     * Get related model.
     *
     * @access   public
     * @return   ModelCore
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getModel()
    {
        return $this->relatedModel;
    }

    /**
     * Set form prefix.
     *
     * @access  public
     * @param   string $value
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function setPrefix($value)
    {
        $this->formPrefix = $value;

        return $this;
    }

    /**
     * Add content to form prefix.
     *
     * @access  public
     * @param   string $value
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function addToPrefix($value)
    {
        $this->formPrefix .= $value;

        return $this;
    }

    /**
     * Get form prefix.
     *
     * @access  public
     * @return  string
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function getPrefix()
    {
        return $this->formPrefix;
    }

    /**
     * Set form suffix.
     *
     * @access  public
     * @param   string $value
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function setSuffix($value)
    {
        $this->formSuffix = $value;

        return $this;
    }

    /**
     * Add content to suffix.
     *
     * @access  public
     * @param   string $value
     * @return  $this
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function addToSuffix($value)
    {
        $this->formSuffix .= $value;

        return $this;
    }

    /**
     * Get form suffix.
     *
     * @access  public
     * @return  string
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function getSuffix()
    {
        return $this->formSuffix;
    }
}
