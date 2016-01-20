<?php

namespace Plethora\Form\Field;

use Plethora\Helper;
use Plethora\Form;
use Plethora\Exception;
use Plethora\Validator;

/**
 * Checkbox form field.
 *
 * @package        Plethora\Form
 * @subpackage     Field
 * @author         Krzysztof Trzos
 * @version        2.37.11-dev, 2015-08-21
 */
class Checkbox extends Form\Field {

    /**
     * Amount of checkboxes columns.
     *
     * @access    private
     * @var        integer
     * @since     1.0.0-alpha
     */
    private $iColumns = 1;

    /**
     * Which of the value(s) must be checked as default.
     *
     * @access    private
     * @var        array
     * @since     1.0.0-alpha
     */
    private $aOptions = [];

    /**
     * Path to fields View.
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sView = 'base/form/field/checkbox';

    /**
     * Value which will be returned as value of the field if none of options will be checked.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $mUncheckedValue = [];

    /**
     * Flag which tells to return all values, or only one of them.
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $bReturnSingleValue = FALSE;

    /**
     * Constructor.
     *
     * @access   public
     * @param    string $name
     * @param    Form   $form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form &$form) {
        parent::__construct($name, $form);

        $this->getAttributes()->setAttribute('type', 'checkbox');
    }

    /**
     * Reset form values.
     *
     * @access   protected
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function resetValue() {
        $aDefaultValue           = $this->getFormObject()->getDefaultVal($this->getName());
        $this->aFormMethodValues = $this->getFormObject()->getMethodValue();

        foreach($this->getLangs() as $sLang) {
            if(isset($aDefaultValue[$sLang])) {
                $aDefaultValue[$sLang] = [$aDefaultValue[$sLang]];
            }
        }

        if($this->getFormObject()->isFieldsNameWithPrefix()) {
            $mSentData = Helper\Arrays::path($this->aFormMethodValues, $this->getFormObject()->getName().'.'.$this->getName(), FALSE);
        } else {
            $mSentData = Helper\Arrays::get($this->aFormMethodValues, $this->getName(), FALSE);
        }

        if($mSentData === FALSE && $this->getFormObject()->isSubmitted()) {
            $mSentData = [];

            foreach($this->getLangs() as $sLang) {
                $mSentData[$sLang][] = [];
            }
        }

        $this->setValue($mSentData !== FALSE ? $mSentData : $aDefaultValue);

        return $this;
    }

    /**
     * @access   public
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setReturnValueAsString() {
        $this->bReturnSingleValue = TRUE;

        return $this;
    }

    /**
     * Set unchecked value. If amount of options is greater than 1, then $mVal
     * should be an array. In other case, it should be string.
     *
     * @access   public
     * @param    string|array $mVal
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setUncheckedValue($mVal) {
        $this->mUncheckedValue = is_array($mVal) ? $mVal : [$mVal];

        return $this;
    }

    /**
     * @access     public
     * @return    string|array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getUncheckedValue() {
        return $this->mUncheckedValue;
    }

    /**
     * Setting amount of checkbox columns
     *
     * @param    integer
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setColumnsAmount($iAmount) {
        if(is_int($iAmount)) {
            $this->iColumns = $iAmount;
        }

        return $this;
    }

    /**
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getColumnsAmount() {
        return $this->iColumns;
    }

    /**
     * Setting checkboxes
     *
     * @access   public
     * @param    array $values array($key => array('value' => '', 'label' => ''))
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setOptions($values) {
        if(is_array($values)) {
            foreach($values as $key => $value) {
                $this->aOptions[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Get field options.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getOptions() {
        return $this->aOptions;
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access     public
     * @param    string $sName
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($sName) {
        return static::singletonByType($sName, 'Checkbox');
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @overwritten
     * @access   public
     * @param    string $sType
     * @return   Form\Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function cloneToOtherType($sType) {
        $oField = parent::cloneToOtherType($sType);
        /* @var $oField Select */
        $aOptions = [];

        switch($sType) {
            case 'select':
                foreach($this->getOptions() as $aOption) {
                    $aOptions[$aOption['value']] = $aOption['label'];
                }

                $oField->setOptions($aOptions);

                break;
        }

        return $oField;
    }

    /**
     * Set field as required (can't be empty).
     *
     * @access   public
     * @return   Form\Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRequired() {
        $oRulesSet = Validator\RulesSetBuilder\Arrays::factory();
        $oRulesSet->notEmpty(':value');
        /* @var $oRulesSet Validator\RulesSetBuilder\Arrays */

        $this->addRulesSet($oRulesSet);
        $this->bRequired = TRUE;

        if($this->iQuantityMin === 0) {
            $this->iQuantityMin = 1;
        }

        return $this;
    }

    /**
     * Set field values quantity.
     *
     * @access   public
     * @param    integer $iValue
     * @return   void
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setQuantity($iValue) {
        throw new Exception\Fatal(__('Cannot set quantity for form field of "Checkbox" type.'));
    }

    /**
     * Set minimal quantity of this form field values.
     *
     * @access   public
     * @param    integer $iValue
     * @return   void
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setQuantityMin($iValue) {
        throw new Exception\Fatal(__('Cannot set quantity for form field of "Checkbox" type.'));
    }

    /**
     * Set maximal quantity of this form field values.
     *
     * @access   public
     * @param    integer $iValue
     * @return   void
     * @throws   Exception\Fatal
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setQuantityMax($iValue) {
        throw new Exception\Fatal(__('Cannot set quantity for form field of "Checkbox" type.'));
    }

}
