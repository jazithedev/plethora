<?php

namespace Plethora;

/**
 * Form validator parent class
 *
 * @package        Plethora
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Validator {

    /**
     * Separator used in fields name, which are used as a key in error array.
     *
     * @since    1.0.0-alpha
     */
    const FIELD_NAME_SEPARATOR = '____';

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFields = [];

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aFieldLabels = [];

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aDbTables = [];

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aDbColumns = [];

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aErrors = [];

    /**
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
     */
    private $aRules = [];

    /**
     * If validator data has been checked
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $bChecked = FALSE;

    /**
     * Array of fields for which all rules will be blocked.
     *
     * @access  private
     * @var     array
     * @since   1.0.0-alpha
*/
    private $aBlockRulesFor = [];

    /**
     * Constructor.
     *
     * @access   public
     * @param    array $aFields
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct(array $aFields = []) {
        $this->aFields = $aFields;

        foreach(array_keys($this->aFields) as $sName) {
            $this->aErrors[$sName] = [];
        }
    }

    /**
     * Factory method.
     *
     * @static
     * @access    public
     * @param    array $mValueToCheck
     * @return    Validator
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public static function factory(array $mValueToCheck = []) {
        return new Validator($mValueToCheck);
    }

    /**
     * @access    public
     * @return    boolean
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function isChecked() {
        return $this->bChecked;
    }

    /**
     * Set data for database connections
     *
     * @access    public
     * @param     string $sField
     * @param     string $sTable
     * @param     string $sColumn
     * @return    Validator
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function setDbData($sField, $sTable, $sColumn) {
        $this->bChecked            = FALSE;
        $this->aDbTables[$sField]  = $sTable;
        $this->aDbColumns[$sField] = $sColumn;

        return $this;
    }

    /**
     * Add new error
     *
     * @access    public
     * @param    string $sField
     * @param    string $sError
     * @return    Validator
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function addError($sField, $sError) {
        $this->aErrors[$sField][] = $sError;

        return $this;
    }

    /**
     * Checks if validator has errors
     *
     * @access    public
     * @param    string $sField
     * @return    boolean
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function hasErrors($sField = NULL) {
        if(!is_null($sField)) {
            return !empty($this->aErrors[$sField]);
        } else {
            foreach($this->aErrors as $aErrors) {
                if(!empty($aErrors)) {
                    return TRUE;
                }
            }

            return FALSE;
        }
    }

    /**
     * Get all errors generated by Validator OR particular field errors.
     *
     * @access    public
     * @param    string $sField
     * @return    array
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function getErrors($sField = NULL) {
        return is_null($sField) ? $this->aErrors : Helper\Arrays::get($this->aErrors, $sField, []);
    }

    /**
     * Binds external errors array to particular validator
     *
     * @access    public
     * @param    array  $aArray
     * @param    string $sField
     * @return    Validator
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function bindErrors(array &$aArray, $sField = NULL) {
        if(!is_null($sField)) {
            $this->aErrors = &$aArray;
        } else {
            $this->aErrors[$sField] = &$aArray;
        }

        return $this;
    }

    /**
     * Set value to check by this Validator object
     *
     * @access    public
     * @param    string $sName
     * @param    mixed  $mValue
     * @return    Validator
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function setValue($sName, $mValue) {
        $this->bChecked        = FALSE;
        $this->aFields[$sName] = $mValue;

        return $this;
    }

    /**
     * Get value to check by this Validator object
     *
     * @access    public
     * @param     string $sName
     * @return    mixed
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function getValue($sName) {
        return Helper\Arrays::get($this->aFields, $sName);
    }

    /**
     * Remove single value for particular field name.
     *
     * @access     public
     * @param    string $sName
     * @return    boolean
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function removeValue($sName) {
        if(isset($this->aFields[$sName])) {
            unset($this->aFields[$sName]);

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Set field label
     *
     * @access   public
     * @param    string $sName
     * @param    string $sValue
     * @return   Validator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFieldLabel($sName, $sValue) {
        $this->aFieldLabels[$sName] = $sValue;

        return $this;
    }

    /**
     * Get particular field label
     *
     * @access   public
     * @param    string $sName
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFieldLabel($sName) {
        return Helper\Arrays::get($this->aFieldLabels, $sName);
    }

    /**
     * Add new rule for particular field in Validator
     *
     * Examples:
     *        rule('field_name', array('Arrays::in_array', 'in_array')),
     *        rule('field_nam3', 'in_array'),
     *        rule('field_nam3', array('in_array', array(':value', 'swswsws', $xVal),
     *
     * @access   public
     * @param    string $sField
     * @param    array  $aRule
     * @return   Validator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function rule($sField, array $aRule) {
        $this->bChecked          = FALSE;
        $this->aRules[$sField][] = $aRule;

        return $this;
    }

    /**
     * List of rules for particular field in Validator
     *
     * @access   public
     * @param    string $sField
     * @param    array  $aRules
     * @return   Validator
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function rules($sField, array $aRules) {
        foreach($aRules as $aRule) {
            $this->rule($sField, $aRule);
        }

        return $this;
    }

    /**
     * Set all rules for all fields (if rules sets downloaded from elsewhere).
     *
     * @access     public
     * @param    array $aRules
     * @return    Validator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function allRules(array $aRules) {
        $this->aRules = $aRules;

        return $this;
    }

    /**
     * Block all rules from particular field.
     *
     * @access     public
     * @param      string  $sName
     * @param      string  $sLang
     * @param      integer $iValueNumber
     * @return     Validator
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function blockRulesFor($sName, $sLang = '', $iValueNumber = NULL) {
        if($sLang === '') {
            $this->aBlockRulesFor[] = $sName;
        } elseif($iValueNumber === NULL) {
            $this->aBlockRulesFor[] = $sName.'_'.$sLang;
        } else {
            $this->aBlockRulesFor[] = $sName.'_'.$sLang.'_'.$iValueNumber;
        }

        return $this;
    }

    /**
     * Remove rule with particular ID and in the field with particular name.
     *
     * @access   public
     * @param    string $sName
     * @param    string $sID
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function removeRuleByID($sName, $sID) {
        unset($this->aRules[$sName][$sID]);
    }

    /**
     * Get all field's rules.
     *
     * @author   Krzysztof Trzos
     * @access   public
     * @param    string $sField
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getRules($sField = NULL) {
        if($sField !== NULL) {
            return Helper\Arrays::get($this->aRules, $sField, []);
        } else {
            return $this->aRules;
        }
    }

    /**
     * Check data of particular validator object.
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function check() {
        foreach($this->aRules as $sField => $aRules) {
            if(!isset($this->aFields[$sField]) || in_array($sField, $this->aBlockRulesFor)) {
                continue;
            }

            foreach($aRules as $mRule) {
                foreach($this->getValue($sField) as $sLang => $aAllValues) {
                    if(in_array($sField.'_'.$sLang, $this->aBlockRulesFor)) {
                        continue;
                    }

                    foreach($aAllValues as $iValueNumber => $mSingleValue) {
                        if(in_array($sField.'_'.$sLang.'_'.$iValueNumber, $this->aBlockRulesFor)) {
                            continue;
                        }

                        $mErrorKey = $sField.static::FIELD_NAME_SEPARATOR.$sLang.static::FIELD_NAME_SEPARATOR.$iValueNumber;

                        // if rule is simple method usage
                        if(!is_array($mRule)) {
                            if($mRule($mSingleValue) === FALSE) {
                                $this->addError($mErrorKey, static::getSystemFunctionError($mRule));
                            }
                        }

                        $aRule = $mRule;
                        $sFunc = $aRule[0];
                        $aArgs = Helper\Arrays::get($aRule, 1, [':value']);

                        // change aliases for particular values
                        foreach($aArgs as $i => $sVal) {
                            if($sVal == ':value') {
                                $aArgs[$i] = $mSingleValue;
                            }
                            if(!is_array($sVal) && strpos($sVal, ':valuefrom:') === 0) {
                                $sOtherFieldName   = str_replace(':valuefrom:', '', $sVal);
                                $aOtherFieldValues = $this->getValue($sOtherFieldName);
                                $aOtherFieldLang   = isset($aOtherFieldValues[$sLang]) ? $aOtherFieldValues[$sLang] : $aOtherFieldValues['und'];
                                $aOtherFieldSingle = isset($aOtherFieldLang[$iValueNumber]) ? $aOtherFieldLang[$iValueNumber] : $aOtherFieldLang[0];
                                $aArgs[$i]         = $aOtherFieldSingle;
                            }
                        }

                        // if rule is simple method usage with more than one arguments
                        if(strpos($sFunc, '::') == 0) {
                            if(call_user_func_array($sFunc, $aArgs) === FALSE) {
                                $this->addError($mErrorKey, static::getSystemFunctionError($sFunc, $aArgs));
                            }
                        } // if rule is using user function / method
                        elseif(($sError = call_user_func_array($sFunc, $aArgs)) !== TRUE) {
                            $this->addError($mErrorKey, $sError);
                        }
                    }
                }
            }
        }

        $this->bChecked = TRUE;
    }

    /**
     *
     * @static
     * @access   public
     * @param    string $sFunction
     * @param    mixed  $mArgs
     * @return   string
     * @version  2014-04-21
     */
    private static function getSystemFunctionError($sFunction, $mArgs = NULL) {
        switch($sFunction) {
            case 'is_numeric':
                $sReturn = __('Given value must be a number.');
                break;
            case 'in_array':
                $sReturn = __('Given value must be one of these values: '.implode(', ', $mArgs[1]).'.');
                break;
            default:
                $sReturn = 'error message not defined';
        }

        return __($sReturn);
    }

    /**
     * Get array of fields to validate.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFields() {
        return $this->aFields;
    }

}