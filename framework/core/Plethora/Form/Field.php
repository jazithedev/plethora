<?php

namespace Plethora\Form;

use Plethora\Core;
use Plethora\Form;
use Plethora\ModelCore;
use Plethora\Router;
use Plethora\Validator\RulesSetBuilder;
use Plethora\View;
use Plethora\Traits;
use Plethora\Helper as Helper;
use Plethora\Exception as Exception;

/**
 * Forms field parent class.
 *
 * @package        Plethora/Form
 * @subpackage     FormField
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Field
{
    use Traits\PrefixSuffix;
    /**
     * Field ID
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sId;

    /**
     *
     * @access    protected
     * @var       string
     * @since     1.0.0-alpha
     */
    protected $sView = 'base/form/field/input';

    /**
     * @access    private
     * @var       string
     * @since     1.0.0-alpha
     */
    private $sViewBase = 'base/form/field';

    /**
     * @access    private
     * @var       string
     * @since     1.0.0-alpha
     */
    private $sViewMultilangBox = 'base/form/field_multilang_box';

    /**
     * Field label
     *
     * @access    protected
     * @var       string
     * @since     1.0.0-alpha
     */
    protected $label;

    /**
     * Field that tells if the label is hidden or not
     *
     * @access    protected
     * @var       boolean
     * @since     1.0.0-alpha
     */
    protected $bShowLabel = TRUE;

    /**
     * Field name
     *
     * @access    protected
     * @var       string
     * @since     1.0.0-alpha
     */
    protected $name;

    /**
     * Is visible?
     *
     * @access    protected
     * @var       boolean
     * @since     1.0.0-alpha
     */
    protected $bVisible = TRUE;

    /**
     * Parent form object
     *
     * @access    protected
     * @var       Form
     * @since     1.0.0-alpha
     */
    protected $oForm = NULL;

    /**
     * Field HTML attributes.
     *
     * @access    private
     * @var       Helper\Attributes
     * @since     1.0.0-alpha
     */
    private $oAttributes = NULL;

    /**
     * Final value for field
     *
     * @access    protected
     * @var       array
     * @since     1.0.0-alpha
     */
    protected $values = [];

    /**
     * Show error list (if exists) without need of submitting form
     *
     * @access    protected
     * @var       boolean
     * @since     1.0.0-alpha
     */
    protected $bShowErrorWithoutSubmit = FALSE;

    /**
     * Is form submitted
     *
     * @access    protected
     * @var       boolean
     * @since     1.0.0-alpha
     */
    protected $bFormSubmitted = FALSE;

    /**
     * @access    protected
     * @var       array
     * @since     1.0.0-alpha
     */
    protected $aFormMethodValues;

    /**
     * @access    protected
     * @var       array
     * @since     1.0.0-alpha
     */
    protected $aHelpToggle = [];

    /**
     * Przechowuje tekst pomocniczy / informujący użytkowników dla danego pola
     *
     * @access    protected
     * @var       string
     * @since     1.0.0-alpha
     */
    protected $sTip = '';

    /**
     * Value that indicates if particular field has values in more than one language.
     *
     * @access    protected
     * @var       boolean
     * @since     1.0.0-alpha
     */
    protected $bMultilanguage = FALSE;

    /**
     * Value provide information whether particular field
     *
     * @access    protected
     * @var       boolean
     * @since     1.0.0-alpha
     */
    protected $bRequired = FALSE;

    /**
     * Weight of field (used to sort form fields). Lower number is higher.
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $iWeight = 0;

    /**
     * Quantity of possible field values.
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $iQuantity = 1;

    /**
     * Set minimal quantity of field values.
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $iQuantityMin = 1;

    /**
     * Set minimal quantity of field values.
     *
     * @access  protected
     * @var     integer
     * @since   1.0.0-alpha
     */
    protected $iQuantityMax = 1;

    /**
     * Create new instance of Field class.
     *
     * @static
     * @access   public
     * @param    string $name field name
     * @param    Form   $form form
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function factory($name, Form $form)
    {
        $class = get_called_class();

        return new $class($name, $form);
    }

    /**
     * Constructor.
     *
     * @access   public
     * @param    string $name field name
     * @param    Form   $form form
     * @throws   Exception
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form $form)
    {
        $this->name        = $name;
        $this->oForm       = $form;
        $this->oAttributes = Helper\Attributes::factory();

        $this->resetAllNeededAttributes();

        $this->bFormSubmitted = $this->getFormObject()->isSubmitted();

        // add particular field to form (if not a singleton)
        if($form->getName() !== 'singletons') {
            if(!$form->hasField($name)) {
                $form->addField($this);
            } else {
                throw new Exception('Form "'.$form->getName().'" already has field with name "'.$this->getName().'".');
            }
        }
    }

    /**
     * Reset form field attributes which needed to change after changing Form object to which this field belongs.
     *
     * @access     protected
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function resetAllNeededAttributes()
    {
        $this->resetNameAttribute();
        $this->resetIdAttribute();
        $this->resetValue();

        return $this;
    }

    /**
     * Reset form field NAME attribute. Use when field changes Form object to which it belongs.
     *
     * @access   protected
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function resetNameAttribute()
    {
        $name = $this->getFormObject()->isFieldsNameWithPrefix() ?
            $this->getFormObject()->getName().'['.$this->getName().']' :
            $this->getName();

        $this->getAttributes()->setAttribute('name', $name);

        return $this;
    }

    /**
     * Reset form field ID attribute. Use when field changes Form object to which it belongs.
     *
     * @access   protected
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function resetIdAttribute()
    {
        $id = 'form_'.$this->getFormObject()->getName().'_field_'.$this->getName();

        $this->getAttributes()->setAttribute('id', $id);

        return $this;
    }

    /**
     * Reset form values.
     *
     * @access   protected
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function resetValue()
    {
        $aDefaultValue           = $this->getFormObject()->getDefaultVal($this->getName());
        $this->aFormMethodValues = $this->getFormObject()->getMethodValue();

        if($this->getFormObject()->isFieldsNameWithPrefix()) {
            $mSentData = Helper\Arrays::path($this->aFormMethodValues, $this->getFormObject()->getName().'.'.$this->getName(), FALSE);
        } else {
            $mSentData = Helper\Arrays::get($this->aFormMethodValues, $this->getName(), FALSE);
        }

        $this->setValue($mSentData !== FALSE ? $mSentData : $aDefaultValue);

        return $this;
    }

    /**
     * Method is called by Form object when this particular form is used (sent).
     *
     * @access   protected
     * @return   void
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function whenFormSubmitted()
    {

    }

    /**
     * Method which initialize validators set
     *
     * @access   public
     * @param    array $aRules
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function validator(array $aRules)
    {
        $this->getFormObject()->getValidator()->rules($this->getName(), $aRules);

        return $this;
    }

    /**
     * Adding validation rules set generated by RulesSetBuilder. Additionaly,
     * adds tips related with created rules.
     *
     * @access     public
     * @param      RulesSetBuilder $oBuilder
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addRulesSet(RulesSetBuilder $oBuilder)
    {
        // rules
        $this
            ->getFormObject()
            ->getValidator()
            ->rules($this->getName(), $oBuilder->getRules());

        // tips
        foreach($oBuilder->getTips() as $sTip) {
            $this->addTipParagraph($sTip);
        }

        return $this;
    }

    /**
     * Add new rule for particular field.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @param      array $rule
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addRule(array $rule)
    {
        $this->getFormObject()->getValidator()->rule($this->getName(), $rule);

        return $this;
    }

    /**
     * Get field value.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @param      string  $lang
     * @param      integer $valueNumber
     * @return     mixed
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getValue($lang = NULL, $valueNumber = NULL)
    {
        if($lang === NULL && $valueNumber === NULL) {
            return $this->values;
        } elseif($lang !== NULL && $valueNumber === NULL) {
            return Helper\Arrays::get($this->values, $lang, []);
        } else {
            return Helper\Arrays::path($this->values, $lang.'.'.$valueNumber);
        }
    }

    /**
     * Get first value of the field.
     *
     * @access  public
     * @param   string $lang
     * @return  mixed|null
     * @throws  Exception\Fatal
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function getValueFirst($lang = 'und')
    {
        if($this->isMultilanguage()) {
            if(in_array($lang, Core::getLanguages())) {
                return $this->getValue($lang, 0);
            } else {
                throw new Exception\Fatal('Wrong language parameter ('.$lang.')!');
            }
        } else {
            return $this->getValue('und', 0);
        }
    }

    /**
     * Set field new value.
     *
     * @author     Krzysztof Trzos
     * @access     public
     * @param      mixed   $value
     * @param      integer $valueNumber
     * @param      string  $lang
     * @return     Field
     * @throws     Exception\Fatal
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setValue($value, $valueNumber = NULL, $lang = NULL)
    {
        if($valueNumber === NULL && $lang === NULL) {
            $this->values = $value;
        } else {
            if($this->isMultilanguage() && !in_array($lang, Core::getLanguages())) {
                throw new Exception\Fatal('Wrong language parameter ('.$lang.')! For multilanguage fields You must define valid language parameter.');
            }

            if($lang !== NULL) {
                if($this->isMultilanguage() && in_array($lang, Core::getLanguages()) || !$this->isMultilanguage() && $lang === 'und') {
                    Helper\Arrays::createMultiKeys($this->values, $lang.'.'.$valueNumber, $value);
                } else {
                    throw new Exception\Fatal('Wrong language parameter ('.$lang.')!');
                }
            } elseif(is_array($value)) {
                foreach($value as $sLangKey => $mValuePerLang) {
                    if($this->isMultilanguage() && in_array($sLangKey, Core::getLanguages()) || !$this->isMultilanguage() && $sLangKey === 'und') {
                        Helper\Arrays::createMultiKeys($this->values, $sLangKey.'.'.$valueNumber, $mValuePerLang);
                    } else {
                        throw new Exception\Fatal('Wrong language parameter ('.$sLangKey.')!');
                    }
                }
            }

            if($lang === NULL) {
                $lang = 'und';
            }

            Helper\Arrays::createMultiKeys($this->values, $lang.'.'.$valueNumber, $value);
        }

        $this->getFormObject()->getValidator()->setValue($this->getName(), $this->values);
        $this->getFormObject()->getValidator()->setFieldLabel($this->getName(), $this->getLabel());

        return $this;
    }

    /**
     * Get name of the field.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get form object, which is related to this field.
     *
     * @access   public
     * @return   Form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFormObject()
    {
        return $this->oForm;
    }

    /**
     * Set field label.
     *
     * @access   public
     * @param    string $label
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get field label (or field name, if label is empty)
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getLabel()
    {
        if(empty($this->label)) {
            return $this->name;
        } else {
            return $this->label;
        }
    }

    /**
     * Set field tip
     *
     * @access   public
     * @param    string $value
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setTip($value)
    {
        $this->sTip = $value;

        return $this;
    }

    /**
     * Add new paragraph to tip box.
     *
     * @access     public
     * @param    string $sValue
     * @return    Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function addTipParagraph($sValue)
    {
        $this->sTip .= '<p>'.$sValue.'</p>';

        return $this;
    }

    /**
     * Get tip for particular field.
     *
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getTip()
    {
        return $this->sTip;
    }

    /**
     * Get attributes Helper.
     *
     * @access   public
     * @return   Helper\Attributes
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAttributes()
    {
        return $this->oAttributes;
    }

    /**
     * Get ID of this field.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getId()
    {
        return $this->getAttributes()->getAttribute('id');
    }

    /**
     * Set field ID.
     *
     * @access     public
     * @param    string $sValue
     * @return    Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setId($sValue)
    {
        $this->getAttributes()->setAttribute('id', $sValue);

        return $this;
    }

    /**
     * Set field as disabled.
     *
     * @access     public
     * @author     Krzysztof Trzos
     * @return    Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setDisabled()
    {
        $this->getAttributes()->setAttribute('disabled', 'disabled');

        return $this;
    }

    /**
     * Check if particular field is disabled (have "disabled" attribute).
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isDisabled()
    {
        return ($this->getAttributes()->getAttribute('disabled') === 'disabled');
    }

    /**
     * Set field as required (can't be empty).
     *
     * @access   public
     * @author   Krzysztof Trzos
     * @return   Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRequired()
    {
        $sType      = str_replace('Plethora\Form\Field\\', '', get_class($this));
        $sClassName = '\Plethora\Validator\RulesSetBuilder\\'.$sType;

        if(!class_exists($sClassName)) {
            $sClassName = '\Plethora\Validator\RulesSetBuilder\String';
        }

        $oRulesSet = call_user_func([$sClassName, 'factory']);
        /* @var $oRulesSet RulesSetBuilder */

        $this->addRulesSet($oRulesSet->notEmpty(':value'));
        $this->bRequired = TRUE;

        if($this->iQuantityMin === 0) {
            $this->setQuantityMin(1);
        }

        return $this;
    }

    /**
     * Set field as NOT required (if it was set as required previously).
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setRequiredNot()
    {
        if($this->bRequired === FALSE) {
            return TRUE;
        }

        $sField     = $this->getName();
        $oValidator = $this->getFormObject()->getValidator();
        $aRules     = $oValidator->getRules($sField);

        foreach($aRules as $i => $aRule) {
            if(strpos($aRule[0], '::notEmpty') !== FALSE) {
                $oValidator->removeRuleByID($sField, $i);

                $this->bRequired = FALSE;

                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Cheks if the field has errors.
     *
     * @access     public
     * @param      string $sLang
     * @return     boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function hasErrors($sLang = NULL)
    {
        $aErrors = $this->getErrors($sLang);

        return !empty($aErrors);
    }

    /**
     * Cheks if the field has errors.
     *
     * @access     public
     * @param      $sLang
     * @param      $iValueNumber
     * @return     bool
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function hasErrorsParticular($sLang, $iValueNumber)
    {
        $aErrors = $this->getFormObject()->getErrorsForField($this->getName().'.'.$sLang.'.'.$iValueNumber);

        return !empty($aErrors);
    }

    /**
     * Returns field errors
     *
     * @access     public
     * @param      string $sLang
     * @return     array
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getErrors($sLang = NULL)
    {
        $sPath = $this->getName();

        if($sLang !== NULL && in_array($sLang, Router::getLangs())) {
            $sPath .= '.'.$sLang;
        }

        return $this->getFormObject()->getErrorsForField($sPath);
    }

    /**
     * Set field visibility
     *
     * @access   public
     * @param    boolean
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function visible($bVal)
    {
        $this->bVisible = $bVal;

        return $this;
    }

    /**
     * Check if field is visible
     *
     * @access     public
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isVisible()
    {
        return $this->bVisible;
    }

    /**
     * Check whether the value is required.
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isRequired()
    {
        return $this->bRequired;
    }

    /**
     * Setting $this->bShowLabel on FALSE
     *
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function hideLabel()
    {
        $this->bShowLabel = FALSE;

        return $this;
    }

    /**
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isLabelVisible()
    {
        return $this->bShowLabel;
    }

    /**
     * @access    public
     * @param    string $sValue
     * @return    Field
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function setView($sValue)
    {
        $this->sView = $sValue;

        return $this;
    }

    /**
     * @access    public
     * @return    string
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function getView()
    {
        return $this->sView;
    }

    /**
     * @access   public
     * @param    string $sValue
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setViewMultilangBox($sValue)
    {
        $this->sViewMultilangBox = $sValue;
    }

    /**
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getViewMultilangBox()
    {
        return $this->sViewMultilangBox;
    }

    /**
     * Make some actions / operations for particular field just before form
     * validation.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function beforeValidation()
    {

    }

    /**
     * @access     public
     * @version    2.0.4, 2013-09-18
     */
    public function afterValidation()
    {

    }

    /**
     * Make some operations when form was checked with validator and this
     * particular field was valid.
     *
     * @access   public
     * @param    string $sLang
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function afterValidationWhenValid($sLang)
    {
        // to overwrite
    }

    /**
     * Set base view path.
     *
     * @access   public
     * @param    string $sValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setViewBase($sValue)
    {
        $this->sViewBase = $sValue;

        return $this;
    }

    /**
     * Get template path for base View.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getViewBase()
    {
        return $this->sViewBase;
    }

    /**
     * Get type of particular form field.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getType()
    {
        $sClassName = get_class($this);
        $sType      = strtolower(str_replace('Plethora\\Form\\Field\\', '', $sClassName));

        return $sType;
    }

    /**
     * Render field and return its rendered value.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function render()
    {
        $content      = '';
        $values       = $this->getValue();
        $langVersions = $this->isMultilanguage() ? Router::getLangs() : ['und'];

        $fieldDefaultId   = $this->getAttributes()->getAttribute('id');
        $fieldDefaultName = $this->getAttributes()->getAttribute('name');

        // create all cases of this field's value
        foreach($langVersions as $sLang) {
            $fieldValueContent   = [];
            $langValues          = Helper\Arrays::get($values, $sLang, []);
            $amountOfFieldValues = count($langValues);

            // check quantity of this field's value for particular lang
            if($this->getQuantity() !== 0 && $this->getQuantity() < $amountOfFieldValues) {
                $amountOfFieldValues = $this->getQuantity();
            }

            // if current amount of fields is below minimal quantity
            if($amountOfFieldValues < $this->getQuantityMin()) {
                $amountOfFieldValues = $this->getQuantityMin();
            }

            // if current amount of fields is higher than maximal quantity
            if($this->getQuantityMax() > 0 && $amountOfFieldValues > $this->getQuantityMax()) {
                $amountOfFieldValues = $this->getQuantityMax();
            }

            // container for one lang values
            $fieldSingleValue = View::factory('base/form/field_single_lang')
                ->set('sLang', $sLang)
                ->bind('oField', $this);

            // for each value number
            for($i = 0; $i < $amountOfFieldValues; $i++) {
                $this->getAttributes()
                    ->setAttribute('id', $fieldDefaultId.'_'.$sLang.'_'.$i)
                    ->setAttribute('name', $fieldDefaultName.'['.$sLang.']['.$i.']');

                $this->renderSingleValue($fieldValueContent, $sLang, $i);
            }

            // prepend to field whole content
            $content .= $fieldSingleValue
                ->bind('aLangValues', $fieldValueContent)
                ->render();
        }

        // field pattern
        if($this->getQuantity() !== 1) {
            $this->getAttributes()
                ->setAttribute('id', $fieldDefaultId.'_LANGUAGE_NUMBER')
                ->setAttribute('name', $fieldDefaultName.'[LANGUAGE][NUMBER]');

            $sPatternContent = $this->renderSingleValuePattern();

            $sPattern = View::factory('base/form/field_single_value')
                ->set('sLang', 'LANGUAGE')
                ->set('sOneValueNumber', 'NUMBER')
                ->bind('sOneValueContent', $sPatternContent)
                ->bind('oField', $this)
                ->render();

            $this->getFormObject()->addFieldPattern($this->getName(), $sPattern);
        }

        // reset ID and NAME attributes
        $this->resetIdAttribute();
        $this->resetNameAttribute();

        // rendering base of field
        return View::factory($this->getViewBase())->render([
            'sContent' => $content,
            'oField'   => $this,
        ]);
    }

    /**
     * Render only one value of the field.
     *
     * @access     protected
     * @param      array   $aFieldValueContent
     * @param      string  $sLang
     * @param      integer $i
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function renderSingleValue(array &$aFieldValueContent, $sLang, $i)
    {
        $aFieldValueContent[$i] = View::factory($this->getView())
            ->set('sLang', $sLang)
            ->set('iValueNumber', $i)
            ->bind('oField', $this)
            ->set('mValue', $this->getValue($sLang, $i))
            ->render();
    }

    /**
     * Render only one value of the field.
     *
     * @access     protected
     * @return     string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function renderSingleValuePattern()
    {
        return View::factory($this->getView())
            ->set('sLang', 'LANGUAGE')
            ->set('iValueNumber', 'NUMBER')
            ->bind('oField', $this)
            ->set('mValue', NULL)
            ->render();
    }

    /**
     * Get information about, if this field is available in more than one languages.
     *
     * @access     public
     * @return     boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function isMultilanguage()
    {
        return $this->bMultilanguage;
    }

    /**
     * Set this field as multilanguage (each value in array corresponds each language set via base config file).
     *
     * @access     public
     * @param      bool $bValue
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setMultilanguage($bValue = TRUE)
    {
        $this->bMultilanguage = $bValue;

        return $this;
    }

    /**
     * If this field is a singleton, append it to a particular form which is not a form for singletons.
     *
     * @access  public
     * @param   Form $form
     * @throws  Exception
     * @throws  Exception\Fatal
     * @since   1.0.0-alpha
     * @version 1.0.0-alpha
     */
    public function setFormIfSingleton(Form &$form)
    {
        if($form->getName() === 'singletons') {
            throw new Exception\Fatal('You can\'t append this field to the singletons form.');
        } elseif($this->getFormObject()->getName() !== 'singletons') {
            throw new Exception\Fatal('This field is already appended to other form.');
        } else {
            $rules = $this->getFormObject()->getValidator()->getRules($this->getName());

            $this->oForm = $form;
            $this->oForm->addField($this);
            $this->oForm->getValidator()->rules($this->getName(), $rules);

            $this->resetAllNeededAttributes();
            $this->setWeight($this->oForm->getLastWeight());
        }
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access     protected
     * @param      string $sName
     * @param      string $sFormFieldClassName
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected static function singletonByType($sName, $sFormFieldClassName)
    {
        $oForm  = FieldSingleton::getForm();
        $sClass = '\\'.__CLASS__.'\\'.$sFormFieldClassName;

        $oField = new $sClass($sName, $oForm);
        /* @var Field $oField */
        $oField->setWeight($oForm->getLastWeight());

        return $oField;
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @access     public
     * @param      string $sType
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function cloneToOtherType($sType)
    {
        $oForm  = FieldSingleton::getForm();
        $sClass = '\\'.__CLASS__.'\\'.ucfirst($sType);

        $oField = new $sClass($this->getName(), $oForm);
        /* @var $oField Field */
        $oField->setWeight($oForm->getLastWeight());

        foreach($this->getAttributes()->getAttributes() as $name => $value) {
            $oField->getAttributes()->addToAttribute($name, $value);
        }

        $oField->setValue($this->getValue());
        $oField->setTip($this->getTip());
        $oField->setLabel($this->getLabel());
        $oField->resetAllNeededAttributes();

        return $oField;
    }

    /**
     * Set field weight.
     *
     * @access     public
     * @param      integer $iWeight
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setWeight($iWeight)
    {
        $this->iWeight = $iWeight;

        return $this;
    }

    /**
     * Get field weight.
     *
     * @access     public
     * @return     integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getWeight()
    {
        return $this->iWeight;
    }

    /**
     * Set position of this field to be after other which name is given as an argument.
     *
     * @access     public
     * @param      string $sFieldName
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setWeightToBeAfter($sFieldName)
    {
        return $this->setWeightBeforeAfterOp($sFieldName);
    }

    /**
     * Set position of this field to be before other which name is given as an argument.
     *
     * @access     public
     * @param      string $sFieldName
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setWeightToBeBefore($sFieldName)
    {
        return $this->setWeightBeforeAfterOp($sFieldName, 'before');
    }

    /**
     * Change position of field in form to be after/before other field.
     *
     * @access     public
     * @param      string $sFieldName
     * @param      string $sOp
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    protected function setWeightBeforeAfterOp($sFieldName, $sOp = 'after')
    {
        $oFieldAfter       = $this->getFormObject()->getField($sFieldName);
        $iFieldAfterWeight = $oFieldAfter->getWeight();
        $iIncreaseAmount   = $sOp == 'after' ? 1 : -1;

        $this->setWeight($iFieldAfterWeight + $iIncreaseAmount);

        $aFields = $this->getFormObject()->getFields();

        foreach($aFields as $oField) {
            /* @var $oField Field */
            if($oField->getName() !== $this->getName()) {
                if(($sOp == 'after' && $oField->getWeight() > $iFieldAfterWeight) || ($sOp == 'before' && $oField->getWeight() < $iFieldAfterWeight)) {
                    $oField->setWeight($oField->getWeight() + $iIncreaseAmount);
                }
            }
        }

        return $this;
    }

    /**
     * Get field values quantity.
     *
     * @access     public
     * @return     integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getQuantity()
    {
        return $this->iQuantity;
    }

    /**
     * Set field values quantity.
     *
     * @access     public
     * @param      integer $iValue
     * @return     Field
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function setQuantity($iValue)
    {
        $this->iQuantity = (int)$iValue;

        if($this->iQuantity === 0) {
            $this->setQuantityMin(0);
            $this->setQuantityMax(0);
        }

        return $this;
    }

    /**
     * Get minimal quantity of this form field values.
     *
     * @access     public
     * @return     integer
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getQuantityMin()
    {
        return $this->iQuantityMin;
    }

    /**
     * Set minimal quantity of this form field values.
     *
     * @access   public
     * @param    integer $iValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setQuantityMin($iValue)
    {
        if($iValue > 0 || $iValue === 0 && !$this->isRequired()) {
            $this->iQuantityMin = (int)$iValue;
        }

        return $this;
    }

    /**
     * Get maximal quantity of this form field values.
     *
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getQuantityMax()
    {
        return $this->iQuantityMax;
    }

    /**
     * Set maximal quantity of this form field values.
     *
     * @access   public
     * @param    integer $iValue
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setQuantityMax($iValue)
    {
        $this->iQuantityMax = (int)$iValue;

        return $this;
    }

    /**
     * Get field lang.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getLangs()
    {
        return $this->isMultilanguage() ? Core::getLanguages() : ['und'];
    }

    /**
     * Make some operations related to this field before Model Entity will be
     * completely removed.
     *
     * @access   public
     * @param    ModelCore $oModel
     * @param    string    $mValue
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function whenRemovingEntity(ModelCore $oModel, $mValue)
    {
        // to overwrite
    }
}
