<?php

namespace Plethora\Form\Field;

use Plethora\Form;

/**
 * Text input form field
 *
 * @package        Plethora
 * @subpackage     Form\Field
 * @author         Krzysztof Trzos
 * @copyright  (c) 2016, Krzysztof Trzos
 * @since          1.0.0-alpha
 * @version        1.0.0-alpha
 */
class Text extends Form\Field {

    /**
     * @access   public
     * @param    string $name
     * @param    Form   $form
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public function __construct($name, Form &$form) {
        parent::__construct($name, $form);

        $this->getAttributes()->setAttribute('type', 'text');
        $this->getAttributes()->addToAttribute('class', 'form-control input-sm');
    }

    /**
     * Create singleton version of particular type of form field.
     *
     * @static
     * @access   public
     * @param    string $sName
     * @return   Text
     * @since    1.0.0-alpha
     * @version  1.0.0-alpha
     */
    public static function singleton($sName) {
        return static::singletonByType($sName, 'Text');
    }

}