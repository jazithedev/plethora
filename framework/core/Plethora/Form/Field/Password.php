<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Password input form field
 *
 * @package        Plethora\Form
 * @subpackage     Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Password extends Form\Field {
    /**
     *
     * @access  protected
     * @var     string
     * @since   1.0.0-alpha
     */
    protected $sView = 'base/form/field/password';

    /**
     * Constructor
     *
     * @access   public
     * @param    string $name
     * @param    Form   $form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form &$form) {
        parent::__construct($name, $form);

        $this->getAttributes()->setAttribute('type', 'password');
        $this->getAttributes()->addToAttribute('class', 'form-control input-sm');
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $name
     * @return   Password
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($name) {
        return static::singletonByType($name, 'Password');
    }
}