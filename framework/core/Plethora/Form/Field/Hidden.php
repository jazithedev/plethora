<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Form input of "hidden" type.
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Hidden extends Form\Field {

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

        $this->getAttributes()->setAttribute('type', 'hidden');
        $this->getAttributes()->addToAttribute('class', 'form-control input-sm');
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $sName
     * @return   Hidden
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($sName) {
        return static::singletonByType($sName, 'Hidden');
    }

}