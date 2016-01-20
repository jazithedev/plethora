<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Textarea form field
 *
 * @package        Plethora
 * @subpackage     Form\Field\Textarea
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Textarea extends Form\Field {
    /**
     * Path to view for this form.
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/textarea';

    /**
     * Textarea cols amount
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $cols = 5;

    /**
     * Textarea rows amount
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $rows = 5;

    /**
     * Variable tells to show / not to show characters countdown
     *
     * @access  private
     * @var     boolean
     * @since   1.0.0-alpha
     */
    private $countdown = FALSE;

    /**
     * Maximal characters amount in current textarea
     *
     * @access  private
     * @var     integer
     * @since   1.0.0-alpha
     */
    private $maxlen;

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

        $this->getAttributes()->addToAttribute('class', 'form-control');
    }


    /**
     * Set textarea amount of cols
     *
     * @access   public
     * @param    integer $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setCols($value) {
        $this->cols = $value;

        return $this;
    }

    /**
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getCols() {
        return $this->cols;
    }

    /**
     * Set textarea amount of rows
     *
     * @access   public
     * @param    integer $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setRows($value) {
        $this->rows = $value;

        return $this;
    }

    /**
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getRows() {
        return $this->rows;
    }

    /**
     * Show characters limit counting
     * Need: form.js
     *
     * @access   public
     * @param    integer $maxLength
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function showCounting($maxLength = NULL) {
        $this->countdown = TRUE;
        $this->maxlen    = $maxLength;

        return $this;
    }

    /**
     * @access   public
     * @return   integer
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getMaxLength() {
        return $this->maxlen;
    }

    /**
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isCountingHidden() {
        return !$this->countdown;
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $name
     * @return   Textarea
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($name) {
        return static::singletonByType($name, 'Textarea');
    }
}