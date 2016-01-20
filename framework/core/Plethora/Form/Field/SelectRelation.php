<?php

namespace Plethora\Form\Field;

use Plethora\Form;
use Plethora\Exception;
use Plethora\Validator;

/**
 * Select field form field which value will be a Model instance.
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class SelectRelation extends Select {

    /**
     * Path to the View of this field.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/select_relation';

    /**
     * Name of model which is related with this field.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $relatedModelName = NULL;

    /**
     * Set name of the model which is related with this field (value of this
     * field will be the particular model instance).
     *
     * @access   public
     * @param    string $model
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRelatedModelName($model) {
        $this->relatedModelName = $model;

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
        return $this->relatedModelName;
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

        if($this->getFormObject()->isSubmitted()) {
            $aValue = $this->getValue();

            foreach($aValue as $sLang => $aAllDefaultValuesForLang) {
                foreach($aAllDefaultValuesForLang as $i => $mSingleValue) {
                    $oModel = \Plethora\DB::find($this->getRelatedModelName(), $mSingleValue);

                    $this->setValue($oModel, $i, $sLang);
                }
            }
        }
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $name
     * @return   SelectRelation
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($name) {
        return static::singletonByType($name, 'SelectRelation');
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
        $oRulesSet = Validator\RulesSetBuilder\Relation::factory();
        /* @var $oRulesSet \Plethora\Validator\RulesSetBuilder\FileModel */

        $this->addRulesSet($oRulesSet->notEmpty(':value'));
        $this->bRequired = TRUE;

        if($this->iQuantityMin === 0) {
            $this->setQuantityMin(1);
        }

        return $this;
    }

}
