<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Select field form field
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Select extends Form\Field {
    /**
     * Set of options to choose.
     *
     * @access  private
     * @var     array    ['value' => 'label']
     * @since   1.0.0-alpha
     */
    private $options = [];

    /**
     * Set label of the first select option.
     *
     * @access  private
     * @var     string
     * @since   1.0.0-alpha
     */
    private $firstOptionValue = '-';

    /**
     * Path to the View of this field.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/select';

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

        $this->getAttributes()->addToAttribute('class', 'form-control input-sm');
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @author   Krzysztof Trzos
     * @access   public
     * @param    string $name
     * @return   Select
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($name) {
        return static::singletonByType($name, 'Select');
    }

    /**
     * Set options.
     *
     * @access   public
     * @param    array $options
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setOptions(array $options) {
        $this->options = $options;

        return $this;
    }

    /**
     * Get an array of all options used for this select field.
     *
     * @access   public
     * @return   array
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * This metod sets first option (in this select) value.
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setFirstOption($value) {
        $this->firstOptionValue = $value;

        return $this;
    }

    /**
     * Get value of the first option of the "select" type field.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFirstOption() {
        return $this->firstOptionValue;
    }
}