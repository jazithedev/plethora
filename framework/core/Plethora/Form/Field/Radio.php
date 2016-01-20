<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Radio field form field
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Radio extends Form\Field {
    /**
     * Set of options to choose
     *
     * @access  private
     * @var     array    ['value' => 'label']
     * @since   1.0.0-alpha
     */
    private $_aRadioOptions = [];

    /**
     * Set how many columns must be
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $_iColumnsAmount = 1;

    /**
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/radio';

    /**
     * Set options
     *
     * @access   public
     * @param    array $aOptions
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setOptions($aOptions) {
        $this->_aRadioOptions = $aOptions;

        return $this;
    }

    /**
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getOptions() {
        return $this->_aRadioOptions;
    }

    /**
     * Setting amount of radio columns
     *
     * @access   public
     * @param    integer $iAmount
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setColumnsAmount($iAmount) {
        if(is_int($iAmount)) {
            $this->_iColumnsAmount = $iAmount;
        }

        return $this;
    }

    /**
     * Get amount of columns in which checkboxes will appear.
     *
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getColumnsAmount() {
        return $this->_iColumnsAmount;
    }
}
