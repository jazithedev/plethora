<?php

namespace Plethora\Form\Field;

use Plethora\DB;
use Plethora\Helper;
use Plethora\Form;
use Plethora\Exception;

/**
 * Checkbox form field.
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class CheckboxRelation extends Checkbox {

    /**
     * Path to fields View.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/checkbox_relation';

    /**
     * Name of model which is related with this field.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sRelatedModelName = NULL;

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $sName
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($sName) {
        return static::singletonByType($sName, 'CheckboxRelation');
    }

    /**
     * Reset form values.
     *
     * @access   protected
     * @return   Form\Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    protected function resetValue() {
        $this->aFormMethodValues = $this->getFormObject()->getMethodValue();

        if($this->getFormObject()->isFieldsNameWithPrefix()) {
            $mSentData = Helper\Arrays::path($this->aFormMethodValues, $this->getFormObject()->getName().'.'.$this->getName(), FALSE);
        } else {
            $mSentData = Helper\Arrays::get($this->aFormMethodValues, $this->getName(), FALSE);
        }

        if($mSentData !== FALSE) {
            foreach($mSentData as $sLang => $aAllDefaultValuesForLang) {
                foreach($aAllDefaultValuesForLang as $i => $mSingleValue) {
                    foreach($mSingleValue as $i => &$mValue) {
                        $mValue = DB::find($this->getRelatedModelName(), $mValue);
                    }

                    $this->setValue($mSingleValue, $i, $sLang);
                }
            }
        } else {
            $aDefaultValue = $this->getFormObject()->getDefaultVal($this->getName());

            foreach($aDefaultValue as $sLang => $aValues) {
                $aDefaultValue[$sLang] = [$aValues];
            }

            $this->setValue($aDefaultValue);
        }

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
        $this->aFormMethodValues = $this->getFormObject()->getMethodValue();

        if($this->getFormObject()->isFieldsNameWithPrefix()) {
            $mSentData = Helper\Arrays::path($this->aFormMethodValues, $this->getFormObject()->getName().'.'.$this->getName(), FALSE);
        } else {
            $mSentData = Helper\Arrays::get($this->aFormMethodValues, $this->getName(), FALSE);
        }

        if($mSentData === FALSE) {
            foreach($this->getLangs() as $lang) {
                $this->setValue([], 0, $lang);
            }
        }
    }


    /**
     * Make some actions / operations for particular field just before form
     * validation has
     *
     * @access   public
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function beforeValidation() {
        if($this->getRelatedModelName() === NULL) {
            throw new Exception\Fatal('To continue, there must be a related model name added by setRelatedModelName() method.');
        }
    }

    /**
     * Set name of the model which is related with this field (value of this
     * field will be the particular model instance).
     *
     * @access   public
     * @param    string $sModel
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRelatedModelName($sModel) {
        $this->sRelatedModelName = $sModel;

        return $this;
    }

    /**
     * Get name of model which is related with this form field.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getRelatedModelName() {
        return $this->sRelatedModelName;
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @access   public
     * @param    string $sType
     * @return   Form\Field
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function cloneToOtherType($sType) {
        $oField = parent::cloneToOtherType($sType);

        if($sType === 'select') {
            $oField->getAttributes()->addToAttribute('class', 'form-control input-sm');
        }

        return $oField;
    }

}
