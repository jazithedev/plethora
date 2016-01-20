<?php

namespace Plethora\Form;

use Plethora\Form;
use Plethora\Helper;
use Plethora\View;

/**
 * Separator parent class
 *
 * @package        Plethora
 * @subpackage     Form
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Separator {
    /**
     * Separator id
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sId;

    /**
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $view = 'form/separator/custom';

    /**
     * Separator value
     *
     * @access    protected
     * @var        mixed
     * @since     1.0.0-alpha
     */
    protected $mValue = NULL;

    /**
     * HTML attributes
     *
     * @access  protected
     * @var     Helper\Attributes
     * @since   1.0.0-alpha
     */
    private $attributes;

    /**
     * Separator name
     *
     * @access    protected
     * @var        string
     * @since     1.0.0-alpha
     */
    protected $sName = NULL;

    /**
     * Parent form object
     *
     * @access  protected
     * @var     Form
     * @since   1.0.0-alpha
     */
    protected $oForm;

    /**
     * Is visible?
     *
     * @access    protected
     * @var        boolean
     * @since     1.0.0-alpha
     */
    protected $bVisible = TRUE;

    /**
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $label = NULL;

    /**
     * Constructor
     *
     * @access   public
     * @param    string $name  field name
     * @param    string $value field name
     * @param    Form   $form  form instance
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, $value, Form &$form) {
        $this->sName      = $name;
        $this->sValue     = $value;
        $this->oForm      = $form;
        $this->attributes = Helper\Attributes::factory();
    }

    /**
     * Set label for current instance of separator.
     *
     * @access   public
     * @param    string $value
     * @return   $this
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function setLabel($value) {
        $this->label = $value;

        return $this;
    }

    /**
     * @access     public
     * @return    string
     * @since      1.0.0-alpha
     * @version    1.0.0-alpha
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Returns rendered field label
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function renderLabel() {
        return '<label>'.$this->getLabel().'</label>';
    }

    /**
     * Get field value
     *
     * @access    public
     * @return    mixed
     * @since     1.0.0-alpha
     * @version   1.0.0-alpha
     */
    public function getValue() {
        return $this->mValue;
    }

    /**
     * Get attributes object.
     *
     * @access   public
     * @return   Helper\Attributes
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * Get form object
     *
     * @access   public
     * @return   Form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getFormObject() {
        return $this->oForm;
    }

    /**
     * Check if field is visible
     *
     * @access   public
     * @return   boolean
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function isVisible() {
        return $this->bVisible;
    }

    /**
     * Get path to separators view.
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function getViewPath() {
        return $this->view;
    }

    /**
     * Returns rendered field
     *
     * @access   public
     * @return   string
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function render() {
        $sContent = View::factory($this->getViewPath().'::php')->render([
            'oField' => $this,
        ]);

        return View::factory('form/separator::php')->render([
            'sContent' => $sContent,
            'oField'   => $this,
        ]);
    }
}